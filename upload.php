<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/model/main.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';

?>


<!DOCTYPE html>
<html>
<head>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/chunks/head.php'; ?>
    <title>Добавить заявку</title>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/chunks/header.php'; ?>
    
    <main class="central">
        <?php if (!isUser()): ?>
            <div class="access-denied">
                <h4>Доступ к странице запрещён</h4>
                <a href="index.php">Вернуться на главную</a>
            <div>

        <?php else: ?>
            <div class="form" id="report-form">
                <div class="input-block">
                    <h6>Название доклада</h6>
                    <input type="text" id="name-input">
                </div>
                <div class="input-block">
                    <h6>Информация о докладчике</h6>
                    <textarea id="speaker-info-textarea"></textarea>
                </div>
                <div class="input-line-block">
                        <h6>Категория:</h6>
                        <select id="category-selector">

                        <?php for ($i = 0; $i < count($categories); $i++): ?>
                            <option><?= $categories[$i]; ?></option>
                        <?php endfor; ?>

                        </select>
                </div>
                <div class="input-block">
                    <h6>Описание доклада</h6>
                    <textarea id="description-textarea"></textarea>
                </div>
                <div class="input-block">
                    <h6>Файл с выступлением (doc, docx, pdf - не более 10 Мб)</h6>
                    <input type="file" accept="application/msword, application/pdf" id="speech-file-input" class="file-input">
                </div>
                <div class="input-block">
                    <h6>Файл с презентацией (ppt, pptx, pdf - не более 30 Мб)</h6>
                    <input type="file" accept="application/mspowerpoint, application/pdf" id="present-file-input" class="file-input">
                </div>
                <div class="form-footer">
                    <div class="message-block" id="report-message-block">
                    </div>
                    <div class="send-actions">
                        <button class="btn btn-info" id="upload-files-button">Загрузить файлы</button>
                        <a href="index.php"><button class="btn btn-secondary" id="cancel-button">Отмена</button></a>
                    </div>
                </div>
            </div>
            <script src="js/new-report.js"></script>

        <?php endif; ?>
    </main>
</body>
</html>
