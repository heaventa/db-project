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

// Сообщения для каждой формы
$add_observer_error = $add_observer_success = "";
$edit_observer_error = $edit_observer_success = "";
$delete_observer_error = $delete_observer_success = "";

// Проверка прав доступа
$canManageObservers = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'worker']);

// Обработка добавления посетителя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_observer']) && $canManageObservers) {
    $museum_id = $_POST['museum_id'] ?? '';
    $o_name = trim($_POST['o_name']);
    $date = $_POST['date'] ?? '';
    $tarif = $_POST['tarif'] ?? '';
    $o_login = $_POST['o_login'] ?? '';
    $o_password = $_POST['o_password'] ?? '';

    $valid_tarifs = ['общий', 'студенческий', 'детский', 'пенсионный'];

    if (empty($museum_id) || empty($o_name) || empty($date) || empty($tarif) || empty($o_login) || empty($o_password)) {
        $add_observer_error = "Пожалуйста, заполните все поля.";
    } elseif (!in_array($tarif, $valid_tarifs)) {
        $add_observer_error = "Некорректный тариф.";
    } else {
        $query = "INSERT INTO exhibit_observers (museum_id, o_name, date, tarif, o_login, o_password)
                  VALUES ($1, $2, $3, $4, $5, $6)";
        $result = pg_query_params($link, $query, [$museum_id, $o_name, $date, $tarif, $o_login, $o_password]);

        if ($result) {
            $add_observer_success = "Посетитель успешно добавлен.";
        } else {
            $add_observer_error = "Ошибка при добавлении посетителя: " . pg_last_error($link);
        }
    }
}

// Обработка редактирования посетителя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_observer']) && $canManageObservers) {
    $observer_id = $_POST['observer_id'] ?? '';
    $museum_id = $_POST['museum_id'] ?? '';
    $o_name = trim($_POST['o_name']);
    $date = $_POST['date'] ?? '';
    $tarif = $_POST['tarif'] ?? '';
    $o_login = $_POST['o_login'] ?? '';
    $o_password = $_POST['o_password'] ?? '';

    $valid_tarifs = ['общий', 'студенческий', 'детский', 'пенсионный'];

    if (empty($observer_id) || empty($museum_id) || empty($o_name) || empty($date) || empty($tarif) || empty($o_login) || empty($o_password)) {
        $edit_observer_error = "Пожалуйста, заполните все поля.";
    } elseif (!in_array($tarif, $valid_tarifs)) {
        $edit_observer_error = "Некорректный тариф.";
    } else {
        $query = "UPDATE exhibit_observers 
                  SET museum_id = $1, o_name = $2, date = $3, tarif = $4, o_login = $5, o_password = $6 
                  WHERE observer_id = $7";
        $result = pg_query_params($link, $query, [$museum_id, $o_name, $date, $tarif, $o_login, $o_password, $observer_id]);

        if ($result) {
            $edit_observer_success = "Посетитель успешно отредактирован.";
        } else {
            $edit_observer_error = "Ошибка при редактировании: " . pg_last_error($link);
        }
    }
}

// Обработка удаления посетителя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_observer']) && $canManageObservers) {
    $observer_id = $_POST['observer_id'] ?? '';

    if (!empty($observer_id) && is_numeric($observer_id)) {
        $query = "DELETE FROM exhibit_observers WHERE observer_id = $1";
        $result = pg_query_params($link, $query, [$observer_id]);

        if ($result && pg_affected_rows($result) > 0) {
            $delete_observer_success = "Посетитель успешно удалён.";
        } else {
            $delete_observer_error = "Посетитель с указанным ID не найден.";
        }
    } else {
        $delete_observer_error = "Некорректный ID посетителя.";
    }
}

// Получение списка посетителей
function getObservers($link) {
    $query = "SELECT * FROM exhibit_observers ORDER BY observer_id";
    $result = pg_query($link, $query);

    if (!$result) {
        die("Ошибка выполнения запроса: " . pg_last_error($link));
    }

    $observers = [];
    while ($row = pg_fetch_assoc($result)) {
        $observers[] = $row;
    }
    pg_free_result($result);
    return $observers;
}

$observers = getObservers($link);
?>

<main>
<div>
    <div class="forms">
        <?php if ($canManageObservers): ?>
        <!-- Форма добавления посетителя -->
        <form method="POST" action="" class="add">
            <input type="hidden" name="add_observer" value="1">
            <input type="number" name="museum_id" placeholder="Номер музея">
            <input type="text" name="o_name" placeholder="Имя">
            <input type="date" name="date" placeholder="Дата посещения">
            <select name="tarif">
                <option value="общий">Общий</option>
                <option value="студенческий">Студенческий</option>
                <option value="детский">Детский</option>
                <option value="пенсионный">Пенсионный</option>
            </select>
            <input type="text" name="o_login" placeholder="Логин">
            <input type="password" name="o_password" placeholder="Пароль">
            <?php
                if (!empty($add_observer_error)) echo "<p class='error'>$add_observer_error</p>";
                if (!empty($add_observer_success)) echo "<p class='success'>$add_observer_success</p>";
            ?>
            <button type="submit">Добавить посетителя</button>
        </form>

        <!-- Форма редактирования посетителя -->
        <form method="POST" action="" class="edit">
            <input type="hidden" name="edit_observer" value="1">
            <input type="number" name="observer_id" placeholder="ID посетителя">
            <input type="number" name="museum_id" placeholder="Номер музея">
            <input type="text" name="o_name" placeholder="Имя">
            <input type="date" name="date" placeholder="Дата посещения">
            <select name="tarif">
                <option value="общий">Общий</option>
                <option value="студенческий">Студенческий</option>
                <option value="детский">Детский</option>
                <option value="пенсионный">Пенсионный</option>
            </select>
            <input type="text" name="o_login" placeholder="Логин">
            <input type="password" name="o_password" placeholder="Пароль">
            <?php
                if (!empty($edit_observer_error)) echo "<p class='error'>$edit_observer_error</p>";
                if (!empty($edit_observer_success)) echo "<p class='success'>$edit_observer_success</p>";
            ?>
            <button type="submit">Редактировать посетителя</button>
        </form>

        <!-- Форма удаления посетителя -->
        <form method="POST" action="" class="delete">
            <input type="hidden" name="delete_observer" value="1">
            <input type="number" name="observer_id" placeholder="ID посетителя">
            <?php
                if (!empty($delete_observer_error)) echo "<p class='error'>$delete_observer_error</p>";
                if (!empty($delete_observer_success)) echo "<p class='success'>$delete_observer_success</p>";
            ?>
            <button type="submit">Удалить посетителя</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Таблица посетителей -->
    <div class="card">
        <table class="big-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Номер музея</th>
                    <th>Имя</th>
                    <th>Дата посещения</th>
                    <th>Тариф</th>
                    <th>Логин</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($observers as $observer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($observer['observer_id']); ?></td>
                    <td><?php echo htmlspecialchars($observer['museum_id']); ?></td>
                    <td><?php echo htmlspecialchars($observer['o_name']); ?></td>
                    <td><?php echo htmlspecialchars($observer['date']); ?></td>
                    <td><?php echo htmlspecialchars($observer['tarif']); ?></td>
                    <td><?php echo htmlspecialchars($observer['o_login']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include("foot.php"); ?>
