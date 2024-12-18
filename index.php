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
?>

<body>
    <main>
    <div>
            <p>
                <strong>Бурылинский музей</strong> — это одно из старейших и значимых культурных учреждений города Иваново.
                Он был основан в начале XX века благодаря инициативе известного промышленника и мецената 
                <strong>Дмитрия Геннадьевича Бурылина</strong>. Музей славится своей богатой коллекцией экспонатов, которые охватывают 
                широкие исторические, этнографические и художественные темы.
            </p>
            <p>
                Коллекция музея включает:
                <ul>
                    <li>Уникальные произведения искусства;</li>
                    <li>Исторические артефакты, относящиеся к разным эпохам;</li>
                    <li>Предметы народного быта и этнографии;</li>
                    <li>Книги и редкие документы из личного собрания Бурылина.</li>
                </ul>
            </p>

            <p>
                Музей расположен в историческом здании, которое само по себе является архитектурной достопримечательностью. 
                В его стенах проходят интересные выставки, образовательные мероприятия и экскурсии, которые будут интересны как 
                взрослым, так и детям.
            </p>
            <p>
                Мы приглашаем вас посетить наш музей и познакомиться с уникальными экспонатами, раскрывающими богатое историческое 
                и культурное наследие нашего края. Бурылинский музей всегда рад новым гостям и готов поделиться знаниями о прошлом, 
                чтобы сохранить его для будущих поколений.
            </p>
       </div>
    </main>
</body>

<?php include("foot.php"); ?>