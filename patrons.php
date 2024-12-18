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
    }
} else {
    include("guest_head.php");
}

// Сообщения для форм
$add_patron_error = $add_patron_success = "";
$edit_patron_error = $edit_patron_success = "";
$delete_patron_error = $delete_patron_success = "";

// Проверка прав доступа
$canManagePatrons = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'worker']);

// Обработка добавления патрона
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patron']) && $canManagePatrons) {
    $patron_id = $_POST['patron_id'] ?? '';
    $exhibit_id = $_POST['exhibit_id'] ?? '';

    if (empty($patron_id) || empty($exhibit_id)) {
        $add_patron_error = "Пожалуйста, заполните все поля.";
    } else {
        $query = "INSERT INTO exhibit_patrons (patron_id, exhibit_id) VALUES ($1, $2)";
        $result = pg_query_params($link, $query, [$patron_id, $exhibit_id]);

        if ($result) {
            $add_patron_success = "Патрон успешно добавлен.";
        } else {
            $add_patron_error = "Ошибка при добавлении патрона: " . pg_last_error($link);
        }
    }
}

// Обработка редактирования патрона
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_patron']) && $canManagePatrons) {
    $patron_id = $_POST['patron_id'] ?? '';
    $exhibit_id = $_POST['exhibit_id'] ?? '';

    if (empty($patron_id) || empty($exhibit_id)) {
        $edit_patron_error = "Пожалуйста, заполните все поля.";
    } else {
        $query = "UPDATE exhibit_patrons SET exhibit_id = $1 WHERE patron_id = $2";
        $result = pg_query_params($link, $query, [$exhibit_id, $patron_id]);

        if ($result) {
            $edit_patron_success = "Патрон успешно отредактирован.";
        } else {
            $edit_patron_error = "Ошибка при редактировании: " . pg_last_error($link);
        }
    }
}

// Обработка удаления патрона
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_patron']) && $canManagePatrons) {
    $patron_id = $_POST['patron_id'] ?? '';

    if (!empty($patron_id)) {
        $query = "DELETE FROM exhibit_patrons WHERE patron_id = $1";
        $result = pg_query_params($link, $query, [$patron_id]);

        if ($result && pg_affected_rows($result) > 0) {
            $delete_patron_success = "Патрон успешно удалён.";
        } else {
            $delete_patron_error = "Патрон с указанным ID не найден.";
        }
    } else {
        $delete_patron_error = "Пожалуйста, укажите ID патрона.";
    }
}

// Получение списка патронов
function getPatrons($link) {
    $query = "SELECT ep.patron_id, ep.exhibit_id, eo.o_name AS observer_name, el.name AS exhibit_name
              FROM exhibit_patrons ep
              JOIN exhibit_observers eo ON ep.patron_id = eo.observer_id
              JOIN exhibits_list el ON ep.exhibit_id = el.exhibit_id
              ORDER BY ep.patron_id";
    $result = pg_query($link, $query);

    if (!$result) {
        die("Ошибка выполнения запроса: " . pg_last_error($link));
    }

    $patrons = [];
    while ($row = pg_fetch_assoc($result)) {
        $patrons[] = $row;
    }
    pg_free_result($result);
    return $patrons;
}

$patrons = getPatrons($link);
?>

<main>
<div>
    <div class="forms">
        <?php if ($canManagePatrons): ?>
        <!-- Форма добавления патрона -->
        <form method="POST" action="" class="add">
            <input type="hidden" name="add_patron" value="1">
            <input type="number" name="patron_id" placeholder="ID посетителя">
            <input type="number" name="exhibit_id" placeholder="ID экспоната">
            <?php
                if (!empty($add_patron_error)) echo "<p class='error'>$add_patron_error</p>";
                if (!empty($add_patron_success)) echo "<p class='success'>$add_patron_success</p>";
            ?>
            <button type="submit">Добавить патрона</button>
        </form>

        <!-- Форма редактирования патрона -->
        <form method="POST" action="" class="edit">
            <input type="hidden" name="edit_patron" value="1">
            <input type="number" name="patron_id" placeholder="ID патрона (как посетителя)">
            <input type="number" name="exhibit_id" placeholder="ID экспоната">
            <?php
                if (!empty($edit_patron_error)) echo "<p class='error'>$edit_patron_error</p>";
                if (!empty($edit_patron_success)) echo "<p class='success'>$edit_patron_success</p>";
            ?>
            <button type="submit">Редактировать патрона</button>
        </form>

        <!-- Форма удаления патрона -->
        <form method="POST" action="" class="delete">
            <input type="hidden" name="delete_patron" value="1">
            <input type="number" name="patron_id" placeholder="ID патрона">
            <?php
                if (!empty($delete_patron_error)) echo "<p class='error'>$delete_patron_error</p>";
                if (!empty($delete_patron_success)) echo "<p class='success'>$delete_patron_success</p>";
            ?>
            <button type="submit">Удалить патрона</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Таблица патронов -->
    <div class="card">
        <table class="big-table">
            <thead>
                <tr>
                    <th>ID патрона</th>
                    <th>Имя посетителя</th>
                    <th>ID экспоната</th>
                    <th>Имя экспоната</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patrons as $patron): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patron['patron_id']); ?></td>
                    <td><?php echo htmlspecialchars($patron['observer_name']); ?></td>
                    <td><?php echo htmlspecialchars($patron['exhibit_id']); ?></td>
                    <td><?php echo htmlspecialchars($patron['exhibit_name']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include("foot.php"); ?>
