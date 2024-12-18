<?php
session_start();

// Включение отображения ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Включаем буферизацию вывода
ob_start();

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

// Сообщения об ошибках и успехах
$review_error = $review_success = $delete_error = $delete_success = "";
if (isset($_SESSION['review_success'])) {
    $review_success = $_SESSION['review_success'];
    unset($_SESSION['review_success']);
}
if (isset($_SESSION['delete_success'])) {
    $delete_success = $_SESSION['delete_success'];
    unset($_SESSION['delete_success']);
}

// Проверка авторизации для добавления отзыва
$isVisitor = isset($_SESSION['role']) && $_SESSION['role'] === 'user';
$isAdminOrWorker = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'worker');

// Обработка добавления отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review']) && $isVisitor) {
    $review_content = trim($_POST['review_content']);
    $exhibtion_id = $_POST['exhibtion_id'] ?? '';
    $observer_id = $_SESSION['id'];

    // Проверяем заполненность полей
    if (empty($review_content) || empty($exhibtion_id)) {
        $review_error = "Пожалуйста, заполните все поля.";
    } else {
        // Добавляем отзыв в базу данных
        $query = "INSERT INTO exhibit_review (observer_id, exhibtion_id, review_content, review_date) VALUES ($1, $2, $3, NOW())";
        $result = pg_query_params($link, $query, [$observer_id, $exhibtion_id, $review_content]);

        if ($result) {
            $_SESSION['review_success'] = "Ваш отзыв успешно добавлен.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $review_error = "Ошибка при добавлении отзыва: " . pg_last_error($link);
        }
    }
}

// Обработка удаления отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review']) && $isAdminOrWorker) {
    $review_id = $_POST['review_id'] ?? '';
    
    // Проверка наличия ID
    if (!empty($review_id) && is_numeric($review_id)) {
        $query = "DELETE FROM exhibit_review WHERE review_id = $1";
        $result = pg_query_params($link, $query, [$review_id]);

        if ($result && pg_affected_rows($result) > 0) {
            $_SESSION['delete_success'] = "Отзыв успешно удалён.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($result) {
            $delete_error = "Отзыв с указанным ID не найден.";
        } else {
            $delete_error = "Ошибка при удалении отзыва: " . pg_last_error($link);
        }
    } else {
        $delete_error = "Пожалуйста, укажите корректный номер отзыва.";
    }
}

// Функция получения отзывов
function getReviews($link) {
    $query = "SELECT r.review_id, r.review_content, r.review_date, o.o_name, e.exhibition_name 
              FROM exhibit_review r
              JOIN exhibit_observers o ON r.observer_id = o.observer_id
              JOIN exhibition_list e ON r.exhibtion_id = e.exhibition_number
              ORDER BY r.review_date DESC";
    $result = pg_query($link, $query);

    if (!$result) {
        die("Ошибка выполнения запроса: " . pg_last_error($link));
    }

    $reviews = [];
    while ($row = pg_fetch_assoc($result)) {
        $reviews[] = $row;
    }
    pg_free_result($result);
    return $reviews;
}

// Получение отзывов
$reviews = getReviews($link);

// Завершаем буферизацию вывода
ob_end_flush();
?>

<main>
    <div >
        <div class="forms">
            <?php if ($isVisitor): ?>
            <!-- Форма добавления отзыва -->
            <form method="POST" action="" class="review-form">
                <input type="hidden" name="add_review" value="1">
                <textarea name="review_content" placeholder="Ваш отзыв"></textarea>
                <input type="number" name="exhibtion_id" placeholder="Номер зала">
                <?php
                if (!empty($review_error)) echo "<p class='error'>$review_error</p>";
                if (!empty($review_success)) echo "<p class='success'>$review_success</p>";
                ?>
                <button type="submit">Оставить отзыв</button>
            </form>
            <?php endif; ?>
            
            <?php if ($isAdminOrWorker): ?>
            <!-- Форма удаления отзыва -->
            <form method="POST" action="" class="review-form">
                <input type="hidden" name="delete_review" value="1">
                <input type="number" name="review_id" placeholder="Номер отзыва">
                <?php
                if (!empty($delete_error)) echo "<p class='error'>$delete_error</p>";
                if (!empty($delete_success)) echo "<p class='success'>$delete_success</p>";
                ?>
                <button type="submit">Удалить отзыв</button>
            </form>
            <?php endif; ?>
        </div>
        <div class="reviews">
            <?php
            // Вывод отзывов
            if (count($reviews) > 0) {
                foreach ($reviews as $review) { ?>
                    <table class="review-table">
                        <tbody>
                            <tr>
                            	<th>Дата: <?php echo htmlspecialchars($review['review_date']); ?></th>
                                <th>Номер отзыва: <?php echo htmlspecialchars($review['review_id']); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2"><strong><?php echo htmlspecialchars($review['o_name']); ?></strong>
                                    о зале "<strong><?php echo htmlspecialchars($review['exhibition_name']); ?></strong>":
                                    <br>
                                    "<?php echo htmlspecialchars($review['review_content']); ?>"
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php } 
            } else {
                echo "<p>Отзывов пока нет.</p>";
            }
            ?>
        </div>
    </div>
</main>

<?php include("foot.php"); ?>
