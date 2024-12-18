<?php
session_start();

// Включение отображения ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключение к базе данных
$link = pg_connect("host=localhost dbname=museum user=postgres password=postgres");
if (!$link) {
    die("<p>Не удалось подключиться к базе данных.</p>");
}

include("all_styles.php");

// Подключение соответствующего заголовка на основе роли пользователя
if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] === 'admin') {
        include("admin_head.php");
    } elseif ($_SESSION['role'] === 'worker') {
        include("worker_head.php");
    } elseif ($_SESSION['role'] === 'user') {
        include("visitor_head.php");
    }
} else {
    include("guest_head.php");
}

// Сообщения об ошибках и успехах для каждой формы
$add_worker_error = $add_worker_success = "";
$edit_worker_error = $edit_worker_success = "";
$delete_worker_error = $delete_worker_success = "";

// Проверка прав доступа
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Обработка добавления работника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_worker']) && $isAdmin) {
    $museum_id = $_POST['museum_id'] ?? '';
    $w_name = trim($_POST['w_name']);
    $w_surname = trim($_POST['w_surname']);
    $w_patronymic = trim($_POST['w_patronymic']);
    $employment_date = $_POST['employment_date'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $w_login = $_POST['w_login'] ?? '';
    $w_password = $_POST['w_password'] ?? '';

    if (empty($museum_id) || empty($w_name) || empty($w_surname) || empty($employment_date) || empty($birthday) || empty($w_login) || empty($w_password)) {
        $add_worker_error = "Пожалуйста, заполните все поля.";
    } else {
        $query = "INSERT INTO exhibit_workers (museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password)
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        $result = pg_query_params($link, $query, [$museum_id, $w_name, $w_surname, $w_patronymic, $employment_date, $birthday, $w_login, $w_password]);

        if ($result) {
            $add_worker_success = "Работник успешно добавлен.";
        } else {
            $add_worker_error = "Ошибка при добавлении работника: " . pg_last_error($link);
        }
    }
}

// Обработка редактирования работника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_worker']) && $isAdmin) {
    $worker_id = $_POST['worker_id'] ?? '';
    $museum_id = $_POST['museum_id'] ?? '';
    $w_name = trim($_POST['w_name']);
    $w_surname = trim($_POST['w_surname']);
    $w_patronymic = trim($_POST['w_patronymic']);
    $employment_date = $_POST['employment_date'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $w_login = $_POST['w_login'] ?? '';
    $w_password = $_POST['w_password'] ?? '';

    if (empty($worker_id) || empty($museum_id) || empty($w_name) || empty($w_surname) || empty($employment_date) || empty($birthday) || empty($w_login) || empty($w_password)) {
        $edit_worker_error = "Пожалуйста, заполните все поля.";
    } else {
        $query = "UPDATE exhibit_workers SET museum_id = $1, w_name = $2, w_surname = $3, w_patronymic = $4, employment_date = $5, birthday = $6, w_login = $7, w_password = $8 WHERE worker_id = $9";
        $result = pg_query_params($link, $query, [$museum_id, $w_name, $w_surname, $w_patronymic, $employment_date, $birthday, $w_login, $w_password, $worker_id]);

        if ($result) {
            $edit_worker_success = "Работник успешно отредактирован.";
        } else {
            $edit_worker_error = "Ошибка при редактировании работника: " . pg_last_error($link);
        }
    }
}

// Обработка удаления работника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_worker']) && $isAdmin) {
    $worker_id = $_POST['worker_id'] ?? '';

    if (!empty($worker_id) && is_numeric($worker_id)) {
        $query = "DELETE FROM exhibit_workers WHERE worker_id = $1";
        $result = pg_query_params($link, $query, [$worker_id]);

        if ($result && pg_affected_rows($result) > 0) {
            $delete_worker_success = "Работник успешно удалён.";
        } else {
            $delete_worker_error = "Работник с указанным ID не найден.";
        }
    } else {
        $delete_worker_error = "Пожалуйста, укажите корректный номер работника.";
    }
}

// Получение списка работников
function getWorkers($link) {
    $query = "SELECT * FROM exhibit_workers ORDER BY worker_id";
    $result = pg_query($link, $query);

    if (!$result) {
        die("Ошибка выполнения запроса: " . pg_last_error($link));
    }

    $workers = [];
    while ($row = pg_fetch_assoc($result)) {
        $workers[] = $row;
    }
    pg_free_result($result);
    return $workers;
}

$workers = getWorkers($link);
?>

<main>
<div>
    <div class="forms">
        <?php if ($isAdmin): ?>
        <!-- Форма добавления работника -->
        <form method="POST" action="" class="add">
            <input type="hidden" name="add_worker" value="1">
            <input type="number" name="museum_id" placeholder="Номер музея">
            <input type="text" name="w_name" placeholder="Имя">
            <input type="text" name="w_surname" placeholder="Фамилия">
            <input type="text" name="w_patronymic" placeholder="Отчество">
            <input type="date" name="employment_date">
            <input type="date" name="birthday">
            <input type="text" name="w_login" placeholder="Логин">
            <input type="password" name="w_password" placeholder="Пароль">
            <?php
                if (!empty($add_worker_error)) echo "<p class='error'>$add_worker_error</p>";
                if (!empty($add_worker_success)) echo "<p class='success'>$add_worker_success</p>";
            ?>
            <button type="submit">Добавить работника</button>
        </form>

        <!-- Форма редактирования работника -->
        <form method="POST" action="" class="edit">
            <input type="hidden" name="edit_worker" value="1">
            <input type="number" name="worker_id" placeholder="ID работника">
            <input type="number" name="museum_id" placeholder="Номер музея">
            <input type="text" name="w_name" placeholder="Имя">
            <input type="text" name="w_surname" placeholder="Фамилия">
            <input type="text" name="w_patronymic" placeholder="Отчество">
            <input type="date" name="employment_date" placeholder="Дата трудоустройства">
            <input type="date" name="birthday" placeholder="Дата рождения">
            <input type="text" name="w_login" placeholder="Логин">
            <input type="password" name="w_password" placeholder="Пароль">
            <?php
                if (!empty($edit_worker_error)) echo "<p class='error'>$edit_worker_error</p>";
                if (!empty($edit_worker_success)) echo "<p class='success'>$edit_worker_success</p>";
            ?>
            <button type="submit">Редактировать работника</button>
        </form>

        <!-- Форма удаления работника -->
        <form method="POST" action="" class="delete">
            <input type="hidden" name="delete_worker" value="1">
            <input type="number" name="worker_id" placeholder="ID работника">
            <?php
                if (!empty($delete_worker_error)) echo "<p class='error'>$delete_worker_error</p>";
                if (!empty($delete_worker_success)) echo "<p class='success'>$delete_worker_success</p>";
            ?>
            <button type="submit">Удалить работника</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Таблица работников -->
    <div class="card">
        <table class="big-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Номер музея</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Отчество</th>
                    <th>Дата трудоустройства</th>
                    <th>Дата рождения</th>
                    <th>Логин</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workers as $worker): ?>
                <tr>
                    <td><?php echo htmlspecialchars($worker['worker_id']); ?></td>
                    <td><?php echo htmlspecialchars($worker['museum_id']); ?></td>
                    <td><?php echo htmlspecialchars($worker['w_name']); ?></td>
                    <td><?php echo htmlspecialchars($worker['w_surname']); ?></td>
                    <td><?php echo htmlspecialchars($worker['w_patronymic']); ?></td>
                    <td><?php echo htmlspecialchars($worker['employment_date']); ?></td>
                    <td><?php echo htmlspecialchars($worker['birthday']); ?></td>
                    <td><?php echo htmlspecialchars($worker['w_login']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include("foot.php"); ?>
