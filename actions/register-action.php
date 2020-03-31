<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/model/main.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';

if (isUser()) {
    die('Действие запрещено');
}

header('Content-Type: application/json; charset=UTF-8');

$response = [
    'is_error' => false,
    'error' => null,
    'data' => [
        [
            'value' => $_POST['name'],
            'field' => '#reg-name-input',
            'err_msg' => [],
            'max_length' => 32
        ],
        [
            'value' => $_POST['email'],
            'field' => '#reg-email-input',
            'err_msg' => [],
            'max_length' => 32
        ],
        [
            'value' => $_POST['password'],
            'field' => '#reg-password-input',
            'err_msg' => [],
            'max_length' => 32
        ],
        [
            'value' => $_POST['rep_password'],
            'field' => '#reg-rep-password-input',
            'err_msg' => [],
            'max_length' => 32
        ]
    ]
];


for ($i = 0; $i < 4; $i++) {
    $error_array = [];
    if (!isset($response['data'][$i]['value']) || strlen($response['data'][$i]['value']) === 0) {
        $response['is_error'] = true;
        array_push($response['data'][$i]['err_msg'], 'Данное поле обязательно для заполнения');
    } elseif (strlen($response['data'][$i]['value']) > $response['data'][$i]['max_length']) {
        $response['is_error'] = true;
        array_push($response['data'][$i]['err_msg'], 'Поле должно содержать не более '.$response['data'][$i]['max_length'].' символов');
    }
}

if (strcmp($response['data'][3]['value'], $response['data'][2]['value']) != 0) {
    $response['is_error'] = true;
    array_push($response['data'][3]['err_msg'], 'Повтор пароля не совпадает');
}

if (preg_match("/[^а-яА-Я \-]{1,}/u", $response['data'][0]['value']) == 1) {
    $response['is_error'] = true;
    array_push($response['data'][0]['err_msg'], 'Допустимы только русские буквы, пробелы и дефисы');
}

if (filter_var($response['data'][1]['value'], FILTER_VALIDATE_EMAIL) == false) {
    $response['is_error'] = true;
    array_push($response['data'][1]['err_msg'], 'Указан невалидный email');
}

if (strlen($response['data'][2]['value']) < 6) {
    $response['is_error'] = true;
    array_push($response['data'][2]['err_msg'], 'Пароль должен содержать как минимум 6 символов');
}


if ($response['is_error'] === true) {
    die(json_encode($response));
} else {
    $db = new Database();
    if ($db->connect() === true) {
        $details = $db->getUser($_POST['email']);
        if ($details !== false) {
            if ($details !== null && is_array($details) === true) {
                $response['is_error'] = true;
                $response['error'] = 'Данный Email уже зарегистрирован';
                die(json_encode($response));
            } else {
                $id = $db->insertUser($_POST['name'], $_POST['email'], md5("QjhJPE7R".$_POST['password']."ZKPzIAyb"));
                if ($id === null) {
                    $response['is_error'] = true;
                    $response['error'] = "Ошибка БД";
                    die(json_encode($response));
                }
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $_POST['name'];
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['access_level'] = 1;

                mkdir("{$_SERVER['DOCUMENT_ROOT']}/users/{$_SESSION['email']}");

                echo json_encode($response);
            }
        } else {
            $response['is_error'] = true;
            $response['error'] = "Ошибка БД";
            die(json_encode($response));
        }
    } else {
        $response['is_error'] = true;
        $response['error'] = "Ошибка БД";
        die(json_encode($response));
    }
}
