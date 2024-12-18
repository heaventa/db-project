<?php session_start(); ?>
<?php
if (isset($_POST['logout'])) {
    // Завершение сессии и перенаправление на страницу входа
    session_destroy();
    header("Location: cabinet_updated.php");
    exit;
}
$err = ""; // Сообщение об ошибке
$success = ""; // Сообщение об успешном входе
if (isset($_POST['login'])) {
    // Подключение к PostgreSQL
    $connectionString = "host=localhost dbname=museum user=postgres password=postgres";
    $link = pg_connect($connectionString);

    if (!$link) {
        die("Не удалось подключиться к базе данных.");
    }
    $login = pg_escape_string($link, $_POST['login']); // Защита от SQL-инъекций
    $password = pg_escape_string($link, $_POST['password']); // Защита от SQL-инъекций
    if ($login === 'admin' && $password === '123456') {
        // Вход за администратора
        $_SESSION['id'] = 'admin';
        $_SESSION['role'] = 'admin';
        $success = "Успешный вход!";
    } else {
        // Проверка пользователей
        $userQuery = "SELECT observer_id, o_name FROM exhibit_observers WHERE o_login = '$login' AND o_password = '$password'";
        $userResult = pg_query($link, $userQuery);

        if ($userResult && pg_num_rows($userResult) > 0) {
            $userData = pg_fetch_assoc($userResult);
            $_SESSION['id'] = $userData['observer_id'];
            $_SESSION['role'] = 'user';
            $_SESSION['user_data'] = $userData;
            $success = "Успешный вход!";
        } else {
            // Проверка работников
            $workerQuery = "SELECT worker_id, w_name, w_surname FROM exhibit_workers WHERE w_login = '$login' AND w_password = '$password'";
            $workerResult = pg_query($link, $workerQuery);

            if ($workerResult && pg_num_rows($workerResult) > 0) {
                $workerData = pg_fetch_assoc($workerResult);
                $_SESSION['id'] = $workerData['worker_id'];
                $_SESSION['role'] = 'worker';
                $_SESSION['user_data'] = $workerData;
                $success = "Успешный вход!";
            } else {
                $err = "Неверный логин или пароль.";
            }
        }
    }
    pg_close($link);
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
?>
<main>
    <?php if (!isset($_SESSION['id']) && empty($_POST['role']) && empty($err)) { ?>
        <div class="role-selection" align="center">
            <p class="midtext_dark">Кто вы?<br></p>
            <form method="post">
                <button type="submit" name="role" value="admin">Администратор</button>
                <button type="submit" name="role" value="worker">Работник</button>
                <button type="submit" name="role" value="user">Пользователь</button>
            </form>
        </div>
    <?php } elseif (isset($_SESSION['id'])) { ?>
        <div class="logout-form" align="center">
            <p>Добро пожаловать,
                <?php if ($_SESSION['role'] === 'admin') {
                    echo "Администратор";
                } elseif ($_SESSION['role'] === 'worker') {
                    echo htmlspecialchars($_SESSION['user_data']['w_name'] . ' ' . $_SESSION['user_data']['w_surname']);
                } else {
                    echo htmlspecialchars($_SESSION['user_data']['o_name']);
                } ?>
            </p>
            <?php if (!empty($success)) { ?>
                <p> <?php echo htmlspecialchars($success); ?> </p>
            <?php } ?>
            <form method="post">
                <input name="logout" type="hidden" />
                <input type="submit" value="Выйти" />
            </form>
        </div>
    <?php } else { ?>
        <div class="login-form" align="center">
		<form method="post">
                <input type="text" name="login" placeholder="Логин" required />
                <input type="password" name="password" placeholder="Пароль" required />
                <input type="submit" value="Войти" />
		</form>
            <?php if (!empty($err)) { ?>
                <p> <?php echo htmlspecialchars($err); ?> </p>
            <?php } ?>
		<form method="post">
                    <button type="submit" name="role" value="">Назад</button>
		</form>
        </div>
    <?php } ?>
</main>
<?php include("foot.php"); ?>
