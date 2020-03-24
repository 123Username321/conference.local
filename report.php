<?php

require_once 'user.php';
require_once 'psql_config.php';

session_start();

?>


<!DOCTYPE html>
<html>
<head>
    <?php require_once 'chunks/head.html'; ?>
    <title><?= "Подробнее"; ?></title>
</head>
<body>
    <?php require_once 'chunks/header.html' ?>
    
    <?php if (!isUser() || !isset($_GET['id'])): ?>
        <main class="central">
            <div class="access-denied">
                <h4>Заявка не найдена или доступ к ней запрещён</h4>
                <a href="index.php">Вернуться на главную</a>
            <div>
        <main>

    <?php else: ?>
        <main class="central">
            <?php 
                $report = $_SESSION['user']->getDetailedReport($_GET['id']);
            ?>

            <?php if (is_array($report)): ?>
            <div class="report-info">
                <div class="block-info">
                    <p><?= "<b>Название: </b>".$report['report_name']; ?></p>
                </div>

                <div class="block-info">
                    <p><?= "<b>Категория: </b>".$categories[$report['category']]; ?></p>
                </div>

                <div class="block-info">
                    <p><?= "<b>Информация о докладчике: </b>".$report['user_info']; ?></p>
                </div>

                <div class="block-info">
                    <p><?= "<b>Краткое описание: </b>".$report['description']; ?></p>
                </div>

                <div>
                    <table>
                        <tr class="tr-info">
                            <td><h6><?= "Файл выступления: " ?></h6></td>
                            <td>
                                <?= '<a href="download.php?report_id='.$report["report_id"].'&file=speech"><button class="btn btn-info">Скачать</button></a>' ?>
                            </td>
                        </tr>

                        <tr class="tr-info">
                            <td><h6><?= "Файл презентации: " ?></h6></td>
                            <td>
                                <?= '<a href="download.php?report_id='.$report["report_id"].'&file=present"><button class="btn btn-info">Скачать</button></a>' ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="report-footer">
                    <a href="index.php"><button class="btn btn-info">Вернуться к списку</button></a>
                </div>
            <div>

            <?php else: ?>
                <div class="access-denied">
                    <h4>Заявка не найдена или доступ к ней запрещён</h4>
                    <a href="index.php">Вернуться на главную</a>
                <div>

            <?php endif; ?>
        </main>
    <?php endif; ?>
</body>
</html>

<?php

unset($_SESSION['report']);

?>