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
    <div class="card">
        <table class="big-table">
            <tr>
                <th colspan="2">1. Арсенал</th>
            </tr>
            <tr>
                <td>Экспозиция представляет около 500 предметов холодного и огнестрельного оружия различных стран мира. Здесь можно увидеть доспехи русских воинов XIV века и самураев XVIII века.</td>
                <td><img src="images/z1.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">2. Белый зал</th>
            </tr>
            <tr>
                <td>Служит концертно-выставочным пространством для проведения культурных мероприятий, выставок и концертов. Зал известен своей светлой архитектурой и великолепной акустикой.</td>
                <td><img src="images/z2.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">3. Природа Ивановского края</th>
            </tr>
            <tr>
                <td>Экспозиция рассказывает о богатой природе Ивановского региона: растительном и животном мире, а также природных и экологических особенностях края.</td>
                <td><img src="images/z3.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">4. Мемориальный кабинет Д. Г. Бурылина</th>
            </tr>
            <tr>
                <td>Мемориальный кабинет представляет личную обстановку Дмитрия Геннадьевича Бурылина – основателя музея. Здесь хранятся его рабочие принадлежности, книги и другие личные вещи.</td>
                <td><img src="images/z4.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">5. Европейская коллекция</th>
            </tr>
            <tr>
                <td>Экспозиция демонстрирует предметы искусства и быта стран Европы, включая живопись, скульптуры и декоративно-прикладное искусство.</td>
                <td><img src="images/z5.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">6. Книжный зал</th>
            </tr>
            <tr>
                <td>Зал содержит редкие и ценные издания из коллекции Бурылина, включая старопечатные книги, манускрипты и исторические документы.</td>
                <td><img src="images/z6.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">7. Золотая кладовая</th>
            </tr>
            <tr>
                <td>Экспозиция представляет драгоценные предметы: церковную утварь, ювелирные изделия, монеты и личные вещи значимых исторических личностей.</td>
                <td><img src="images/z7.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">8. Искусство и время</th>
            </tr>
            <tr>
                <td>В зале представлено искусство разных эпох и стилей: живопись, скульптура, мебель и предметы интерьера из коллекции Бурылина.</td>
                <td><img src="images/z8.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">9. Библиотека Д. Г. Бурылина</th>
            </tr>
            <tr>
                <td>Библиотека содержит уникальные книги и документы, собранные Дмитрием Бурылиным. Многие из них представляют историческую и культурную ценность.</td>
                <td><img src="images/z9.jpg"></td>
            </tr>
        </table>

        <table class="big-table">
            <tr>
                <th colspan="2">10. Комната Л. Н. Толстого</th>
            </tr>
            <tr>
                <td>Комната посвящена жизни и творчеству Льва Николаевича Толстого. В экспозиции представлены материалы и вещи, связанные с великим русским писателем.</td>
                <td><img src="images/z10.jpg"></td>
            </tr>
        </table>
    </div>
    </main>
</body>
</html>


<?php include("foot.php"); ?>
