<?php
session_start();

// Включение отображения ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Включаем буферизацию вывода, чтобы избежать ошибок "headers already sent"
ob_start();

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

// Сообщения об ошибках и успехах
$add_error = $delete_error = $edit_error = "";
$add_success = $delete_success = $edit_success = "";
if (isset($_SESSION['add_success'])) {
    $add_success = $_SESSION['add_success'];
    unset($_SESSION['add_success']);
}
if (isset($_SESSION['delete_success'])) {
    $delete_success = $_SESSION['delete_success'];
    unset($_SESSION['delete_success']);
}
if (isset($_SESSION['edit_success'])) {
    $edit_success = $_SESSION['edit_success'];
    unset($_SESSION['edit_success']);
}

// Проверка прав доступа
$hasCrudAccess = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'worker');

// Обработка CRUD-операций
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasCrudAccess) {
    if (!empty($_POST['add_exhibit'])) {
        $name = $_POST['name'] ?? '';
        $country = $_POST['country'] ?? '';
        $date = $_POST['date'] ?? '';
        $exhibition_number = $_POST['exhibition_number'] ?? '';
        $pic = $_POST['pic'] ?? '';

        if (!empty($name) && !empty($country) && !empty($date) && !empty($exhibition_number) && !empty($pic)) {
            $query = "INSERT INTO exhibits_list (name, country, date, exhibition_number, pic) VALUES ($1, $2, $3, $4, $5)";
            $result = pg_query_params($link, $query, [$name, $country, $date, $exhibition_number, $pic]);

            if ($result) {
                $_SESSION['add_success'] = "Экспонат успешно добавлен.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $add_error = "Ошибка при добавлении экспоната: " . pg_last_error($link);
            }
        } else {
            $add_error = "Пожалуйста, заполните все поля.";
        }
    }

    if (!empty($_POST['delete_exhibit'])) {
        $exhibit_id = $_POST['exhibit_id'] ?? '';
        if (!empty($exhibit_id) && is_numeric($exhibit_id)) {
            $query = "DELETE FROM exhibits_list WHERE exhibit_id = $1";
            $result = pg_query_params($link, $query, [$exhibit_id]);

            if ($result && pg_affected_rows($result) > 0) {
                $_SESSION['delete_success'] = "Экспонат успешно удалён.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } elseif ($result) {
                $delete_error = "Экспонат с указанным ID не найден.";
            } else {
                $delete_error = "Ошибка при удалении экспоната: " . pg_last_error($link);
            }
        } else {
            $delete_error = "Пожалуйста, укажите корректный номер экспоната.";
        }
    }

    if (!empty($_POST['edit_exhibit'])) {
        $exhibit_id = $_POST['exhibit_id'] ?? '';
        $name = $_POST['name'] ?? '';
        $country = $_POST['country'] ?? '';
        $date = $_POST['date'] ?? '';
        $exhibition_number = $_POST['exhibition_number'] ?? '';
        $pic = $_POST['pic'] ?? '';

        if (!empty($exhibit_id) && !empty($name) && !empty($country) && !empty($date) && !empty($exhibition_number) && !empty($pic)) {
            $query = "UPDATE exhibits_list SET name = $1, country = $2, date = $3, exhibition_number = $4, pic = $5 WHERE exhibit_id = $6";
            $result = pg_query_params($link, $query, [$name, $country, $date, $exhibition_number, $pic, $exhibit_id]);

            if ($result) {
                $_SESSION['edit_success'] = "Экспонат успешно обновлён.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $edit_error = "Ошибка при обновлении экспоната: " . pg_last_error($link);
            }
        } else {
            $edit_error = "Пожалуйста, заполните все поля.";
        }
    }
}

// Функция получения списка экспонатов
function getExhibits($link) {
    $query = "SELECT * FROM exhibits_list 
    JOIN exhibition_list ON exhibition_list.exhibition_number = exhibits_list.exhibition_number
    ORDER BY exhibits_list.exhibit_id";
    $result = pg_query($link, $query);
    if (!$result) {
        die("Ошибка запроса: " . pg_last_error($link));
    }
    $exhibits = [];
    while ($row = pg_fetch_assoc($result)) {
        $exhibits[] = $row;
    }
    pg_free_result($result);
    return $exhibits;
}

$exhibits = getExhibits($link);

// Завершаем буферизацию и отправляем данные
ob_end_flush();
?>
<main>
<div>
<div class="forms">
<?php if ($hasCrudAccess): ?>
<form method="POST" action="" class="add">
    <input type="hidden" name="add_exhibit" value="1">
    <input type="text" id="name" name="name" placeholder="Название экспоната">
    <input type="text" id="country" name="country" placeholder="Страна">
    <input type="date" id="date" name="date" placeholder="Дата/век"><br>
    <input type="number" id="exhibition_number" name="exhibition_number" placeholder="Номер зала">
    <input type="text" id="pic" name="pic" placeholder="Картинка">
    <?php
    if (!empty($add_error)) echo "<p class='error'>$add_error</p>";
    if (!empty($add_success)) echo "<p class='success'>$add_success</p>";
    ?>
    <button type="submit">Добавить экспонат</button>
</form>

<form method="POST" action="" class="delete">
    <input type="hidden" name="delete_exhibit" value="1">
    <input type="number" id="exhibit_id" name="exhibit_id" placeholder="Номер экспоната">
    <?php
    if (!empty($delete_error)) echo "<p class='error'>$delete_error</p>";
    if (!empty($delete_success)) echo "<p class='success'>$delete_success</p>";
    ?>
    <button type="submit">Удалить экспонат</button>
</form>

<form method="POST" action="" class="edit">
    <input type="hidden" name="edit_exhibit" value="1">
    <input type="number" id="exhibit_id" name="exhibit_id" placeholder="Номер экспоната">
    <input type="text" id="name" name="name" placeholder="Название экспоната">
    <input type="text" id="country" name="country" placeholder="Страна"><br>
    <input type="date" id="date" name="date" placeholder="Дата/век">
    <input type="number" id="exhibition_number" name="exhibition_number" placeholder="Номер зала">
    <input type="text" id="pic" name="pic" placeholder="Картинка"><br>
    <?php
    if (!empty($edit_error)) echo "<p class='error'>$edit_error</p>";
    if (!empty($edit_success)) echo "<p class='success'>$edit_success</p>";
    ?>
    <button type="submit">Обновить экспонат</button>
</form>
<?php endif; ?>
</div>
<div class="card">
    <?php foreach ($exhibits as $exhibit): ?>
        <table class="table-container">
            <tbody>
                <tr>
                    <th colspan="2"><?php echo htmlspecialchars($exhibit['exhibit_id']);?>. <?php echo htmlspecialchars($exhibit['name']);?></th>
                </tr>
                <tr>
                    <td colspan="2"><img src="images/<?php echo htmlspecialchars($exhibit['pic']);?>"></td>
                </tr>
                <tr>
                    <td>Зал</td>
                    <td><?php echo htmlspecialchars($exhibit['exhibition_name']);?></td>
                </tr>
                <tr>
                    <td>Страна</td>
                    <td><?php echo htmlspecialchars($exhibit['country']);?></td>
                </tr>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
</div>
</main>
<?php include("foot.php"); ?>
