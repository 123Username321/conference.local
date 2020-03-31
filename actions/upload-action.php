<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/model/main.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';

if (!isUser()) {
    die('Действие запрещено');
}

header('Content-Type: application/json; charset=UTF-8');

$_FILES['userfiles']['name'][0] = preg_replace('/^.*(?=\.)/u', bin2hex(random_bytes(8)), $_FILES['userfiles']['name'][0]);
$_FILES['userfiles']['name'][1] = preg_replace('/^.*(?=\.)/u', bin2hex(random_bytes(8)), $_FILES['userfiles']['name'][1]);

$report = [
    'speaker_info' => $_POST['speaker_info'],
    'category' => $_POST['category'],
    'name' => $_POST['name'],
    'description' => $_POST['description'],
    'speech_file' => $_FILES['userfiles']['name'][0],
    'present_file' => $_FILES['userfiles']['name'][1]
];

$response = [
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
    $element = &$response['data'][$i];

    if (!isset($element['value']) || strlen($element['value']) === 0) {
        $response['is_error'] = true;
        array_push($element['err_msg'], 'Поле обязательно для заполнения');
    } elseif (strlen($element['value']) > $element['max_length']) {
        $response['is_error'] = true;
        array_push($element['err_msg'], 'Поле должно содержать не более '.$element['max_length'].' символов');
    }
}

for ($i = 3; $i < 5; $i++) {
    $element = &$response['data'][$i];

    if (!isset($element['name']) || strlen($element['name']) === 0) {
        $response['is_error'] = true;
        array_push($element['err_msg'], 'Файл обязателен для загрузки');
    } else {
        if ($element['size'] > $element['max_size']) {
            $response['is_error'] = true;
            array_push($element['err_msg'], 'Вес файла не должен превышать '.($element['max_size'] / (1024 * 1024)).' МБ');
        }
        
        if (preg_match($element['regexp'][0], $element['type']) === 0 || preg_match($element['regexp'][1], $element['name'] === 0)) {
            $response['is_error'] = true;
            array_push($element['err_msg'], 'Допустимые форматы файла: '.($element['allowed_exts']));
        }
    }
}

if (!ctype_digit($response['data'][5]['value']) || $response['data'][5]['value'] > $response['data'][5]['max_value']) {
    $response['is_error'] = true;
    array_push($response['data']['error'], 'Ошибка данных');
}

if ($response['is_error'] === true) {
    die(json_encode($response));
} else {
    $db = new Database();
    if (!$db->connect()) {
        die("Не удалось подключиться к БД");
    } else {
        $id = $db->addReport($_SESSION['id'], $report);
        
        if ($id !== false) {
            $reportDir = "{$_SERVER['DOCUMENT_ROOT']}/users/{$_SESSION['email']}/$id";
            mkdir($reportDir);

            if (is_dir($reportDir)) {
                move_uploaded_file($_FILES['userfiles']['tmp_name'][0], "$reportDir/{$_FILES['userfiles']['name'][0]}");
                move_uploaded_file($_FILES['userfiles']['tmp_name'][1], "$reportDir/{$_FILES['userfiles']['name'][1]}");
                echo json_encode($response);
            } else {
                $response['is_error'] = true;
                $response['error'] = 'Ошибка файловой системы';
                die(json_encode($response));
            }
        } else {
            $response['is_error'] = true;
            $response['error'] = 'Не удалось выполнить запрос';
            die(json_encode($response));
        }
    }
}
