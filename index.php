<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/model/main.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';

?>


<!DOCTYPE html>
<html>
<head>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/chunks/head.php'; ?>
    <?php
    if (isUser()) {
        $title = "Список докладов";
    } else {
        $title = "Главная";
    }
    ?>
    <title><?= $title; ?></title>
        
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/chunks/header.php'; ?>

    <?php if (!isUser()): ?>
        <main class="central">
            <div class="centered">
                <h3 class="highlighted-text">Добро пожаловать на сайт конференции <b>IT-Con 2020</b></h3>
                <h4>О конференции:</h4>
                <p>
                    Конференция <b>IT-Con</b> проходит каждый год в 256 день года, здесь участники делятся своими идеями и разработками из любой сферы IT.
                    <br>
                    Если у вас есть идеи, стартапы или просто хотите хорошо провести время и подчерпнуть для себя что-то новое, то IT-Con 2020 ждёт вас.
                </p>
                <h4>Список категорий этого года:</h4>
                <ul>
                    <?php foreach ($categories as $key => $value): ?>
                        <li><?= $value; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="centered">
                <h6>Войдите или зарегистрируйтесь для управления своими докладами</h6>
                <button class="btn btn-info" id="login-offer-button">Войти</button>
                <button class="btn btn-warning" id="register-offer-button">Зарегистрироваться</button>

                <script src="js/offer-buttons.js"></script>
            </div>
        </main>

    <?php else: ?>
        <main class="central">
            <div>
                <h4 class="header highlighted-text">Список докладов</h4>
            </div>

            <?php
            $db = new Database();
            $db->connect();
            $reports = $db->getAllReports($_SESSION['id'], $_SESSION['access_level']);
            if (count($reports) === 0) {
                echo '<h6 class="centered">Заявок ещё нет</h6>';
            } elseif ($reports === false || $reports === null) {
                echo '<h6 class="centered">Ошибка БД</h6>';
            }
            ?>

            <div class="report-list">
                <?php for ($i = 0; $i < count($reports); $i++): ?>
                    <div class="list-item">
                        <h4 class="header"><?= $reports[$i]['report_name']; ?></h4>
                        <p class="category"><?= $categories[$reports[$i]['category']]; ?></p>
                        <p class="description"><?= $reports[$i]['description']; ?></p>
                        <div class="footer">
                            <div class="speaker-info">
                                <p><?= "<b>Имя: </b>".$reports[$i]['name'] ?></p>
                                <p><?= "<b>Email: </b>".$reports[$i]['email']; ?></p>
                            </div>
                            <a href=<?= "report.php?id=".$reports[$i]['id']; ?>><button class="btn btn-info">Подробнее</button></a>
                        </div>
                    </div>

                <?php endfor; ?>
            </div>

            <div class="centered">
                <a href="upload.php"><button class="btn btn-info">Добавить заявку</button></a>
            </div>
        </main>

    <?php endif; ?>
</body>
</html>
