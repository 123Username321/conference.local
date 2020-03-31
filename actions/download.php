<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/main.php';


if (!isUser() || !isset($_GET['report_id']) || !isset($_GET['file'])) {
    die('Действие запрещено');
} else {
    $db = new Database();
    if (!$db->connect()) {
        die("Ошибка подключения к БД");
    }
    $result = $db->getReport($_GET['report_id'], $_SESSION['id'], $_SESSION['access_level']);

    if ($result !== false) {
        if (is_array($result)) {
            if ($_SESSION['access_level'] === 1) {
                $dir = "{$_SERVER['DOCUMENT_ROOT']}/users/{$_SESSION['email']}/{$_SESSION['id']}/";
            } elseif ($_SESSION['access_level'] === 0) {
                $dir = "{$_SERVER['DOCUMENT_ROOT']}/users/{$result['email']}/{$result['user_id']}/";
            }

            if (strcmp($_GET['file'], 'speech') === 0) {
                $file = $dir.$result['speech_file'];
            } elseif (strcmp($_GET['file'], 'present') === 0) {
                $file = $dir.$result['present_file'];
            } else {
                die("Неизвестный тип файла");
            }

            if (file_exists($file)) {
                if (ob_get_level()) {
                    ob_end_clean();
                }
                
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                
                readfile($file);
                exit;
            } else {
                die('Файл не найден');
            }
        } else {
            die('Заявка не найдена или доступ запрещён');
        }
    } else {
        die("Ошибка БД: ".print_r($db->getError()));
    }
}
