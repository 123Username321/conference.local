<?php

require_once 'user.php';
require_once 'psql_config.php';

session_start();
if (!isUser()) die ('Действие запрещено');
header('Content-Type: application/json; charset=UTF-8');

$_FILES['userfiles']['name'][0] = preg_replace('/^.*[\.$]/u', 'speech.', $_FILES['userfiles']['name'][0]);
$_FILES['userfiles']['name'][1] = preg_replace('/^.*[\.$]/u', 'presentation.', $_FILES['userfiles']['name'][1]);

$report = [
    'speaker_info' => $_POST['speaker_info'],
    'category' => $_POST['category'],
    'name' => $_POST['name'],
    'description' => $_POST['description'],
    'speech_file' => $_FILES['userfiles']['name'][0],
    'present_file' => $_FILES['userfiles']['name'][1]
];

$responce = [
    'is_error' => false,
    'error' => null,
    'data' => [
        [
            'value' => $_POST['name'],
            'field' => '#name-input',
            'err_msg' => [],
            'max_length' => 64
        ],
        [
            'value' => $_POST['speaker_info'],
            'field' => '#speaker-info-textarea',
            'err_msg' => [],
            'max_length' => 256
        ],
        [
            'value' => $_POST['description'],
            'field' => '#description-textarea',
            'err_msg' => [],
            'max_length' => 256
        ],
        [
            'name' => $_FILES['userfiles']['name'][0],
            'field' => '#speech-file-input',
            'size' => $_FILES['userfiles']['size'][0], 
            'type' => $_FILES['userfiles']['type'][0],
            'allowed_exts' => 'doc, docx, pdf',
            'regexp' => ['/^(application\/msword|application\/pdf)$/u', '/(\.doc|\.docx|.\.pdf)$/u'],
            'max_size' => 10485760,
            'err_msg' => []
        ],
        [
            'name' => $_FILES['userfiles']['name'][1],
            'field' => '#present-file-input',
            'size' => $_FILES['userfiles']['size'][1], 
            'type' => $_FILES['userfiles']['type'][1],
            'allowed_exts' => 'ppt, pptx, pdf',
            'regexp' => ['/^(application\/msword|application\/pdf)$/u', '/(\.ppt|\.pptx|.\.pdf)$/u'],
            'max_size' => 31457280,
            'err_msg' => []
        ],
        [
            'value' => $_POST['category'],
            'field' => '#category-input',
            'max_value' => count($categories) - 1,
            'err_msg' => []
        ]
    ]
];

for ($i = 0; $i < 3; $i++) {
    if (!isset($responce['data'][$i]['value']) || strlen($responce['data'][$i]['value']) === 0) {
        $responce['is_error'] = true;
        array_push($responce['data'][$i]['err_msg'], 'Поле обязательно для заполнения');
    }
    else if (strlen($responce['data'][$i]['value']) > $responce['data'][$i]['max_length']) {
        $responce['is_error'] = true;
        array_push($responce['data'][$i]['err_msg'], 'Поле должно содержать не более '.$responce['data'][$i]['max_length'].' символов');
    }
}

for ($i = 3; $i < 5; $i++) {
    if ( !isset($responce['data'][$i]['name']) || strlen($responce['data'][$i]['name']) === 0) {
        $responce['is_error'] = true;
        array_push($responce['data'][$i]['err_msg'], 'Файл обязателен для загрузки');
    }
    else {
        if ($responce['data'][$i]['size'] > $responce['data'][$i]['max_size']) {
            $responce['is_error'] = true;
            array_push($responce['data'][$i]['err_msg'], 'Вес файла не должен превышать '.($responce['data'][$i]['max_size'] / (1024 * 1024)).' МБ');
        }
        
        //Информировать?
        if (preg_match($responce['data'][$i]['regexp'][0], $responce['data'][$i]['type']) === 0 || preg_match($responce['data'][$i]['regexp'][1], $responce['data'][$i]['name'] === 0)) {
            $responce['is_error'] = true;
            array_push($responce['data'][$i]['err_msg'], 'Допустимые форматы файла: '.($responce['data'][$i]['allowed_exts']));
        }
    }
}

if (!ctype_digit($responce['data'][5]['value']) || $responce['data'][5]['value'] > $responce['data'][5]['max_value']) {
    $responce['is_error'] = true;
    array_push($responce['data']['error'], 'Ошибка данных');
}

if ($responce['is_error'] === true) die (json_encode($responce));
else {
    try {
        $connection = new PDO("pgsql:host=$host;port=$port;dbname=$db;user=$user");
    } 
    catch (PDOException $e) {
        $responce['is_error'] = true;
        $responce['error'] = 'Не удалось подключиться к БД, попробуйте позднее';
        die (json_encode($responce));
    }

    $query = $connection->prepare("INSERT INTO reports VALUES (DEFAULT, ?, ?, ?, ?, ?, ?, ?) RETURNING id");

    if ($query->execute([$_SESSION['user']->getId(), $report['speaker_info'], $report['category'], $report['name'], $report['description'], $report['speech_file'], $report['present_file']])) {
        $id = $query->fetchAll()[0]['id'];
        $reportDir = $_SESSION['user']->getFolder()."/"."/".$id."_".$report['name'];

        mkdir($reportDir);
        if (is_dir($reportDir)) {
            move_uploaded_file($_FILES['userfiles']['tmp_name'][0], $reportDir."/".$_FILES['userfiles']['name'][0]);
            move_uploaded_file($_FILES['userfiles']['tmp_name'][1], $reportDir."/".$_FILES['userfiles']['name'][1]);
            echo json_encode($responce);
        }
        else {
            $responce['is_error'] = true;
            $responce['error'] = 'Ошибка файловой системы, попробуйте позднее';
            die (json_encode($responce));
        }
    }
    else {
        $responce['is_error'] = true;
        $responce['error'] = print_r($query->errorInfo());//'Не удалось выполнить запрос, попробуйте позднее';
        die (json_encode($responce));
    }

    $connection = null;
    $result = null;
}

?>
