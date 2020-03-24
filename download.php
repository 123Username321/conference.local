<?php

require_once 'user.php';
require_once 'psql_config.php';

session_start();

if (!isUser() || !isset($_GET['report_id'])) die ('Действие запрещено');
else {
    $result = $_SESSION['user']->getDetailedReport($_GET['report_id']);

    if (is_array($result) && count($result) > 0) {

        if ($_SESSION['user']->getAccessLevel() === 1) $file = $_SESSION['user']->getFolder()."/".$result['report_id']."_".$result['report_name']."/";
        else $file = "users/".$result['email']."/".$result['report_id']."_".$result['report_name']."/";

        if (isset($_GET['file'])) {
            if ($_GET['file'] == 'speech') $file = $file.$result['speech_file'];
            else if ($_GET['file'] == 'present') $file = $file.$result['present_file'];
        }


        if (file_exists($file)) {
            
            // Сброс буфера вывода
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
        }
        else die ('Файл не найден');
    }
    else {
        die ('Действие запрещено');
    }
}


?>