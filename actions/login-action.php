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
            'value' => $_POST['email'],
            'field' => '#login-email-input',
            'err_msg' => []
        ],
        [
            'value' => $_POST['password'],
            'field' => '#login-password-input',
            'err_msg' => []
        ]
    ]
];

$data = &$response['data'];

for ($i = 0; $i < 2; $i++) {
    $error_array = [];

    if (!isset($data[$i]['value']) || strlen($data[$i]['value']) === 0) {
        $response['is_error'] = true;
        array_push($data[$i]['err_msg'], 'Данное поле обязательно для заполнения');
    }
}


if ($response['is_error'] === true) {
    die(json_encode($response));
} else {
    $db = new Database();
    if ($db->connect() === true) {
        $details = $db->getUser($_POST['email']);
        if ($details !== false) {
            $hashedPass = md5("QjhJPE7R".$_POST['password']."ZKPzIAyb");

            if ($details === null || strcmp($hashedPass, $details['password']) !== 0) {
                $response['is_error'] = true;
                $response['error'] = 'Неверный логин или пароль';
                die(json_encode($response));
            } else {
                $_SESSION['id'] = $details['id'];
                $_SESSION['name'] = $details['name'];
                $_SESSION['email'] = $details['email'];
                $_SESSION['access_level'] = $details['access_level'];
                echo json_encode($response);
            }
        } else {
            $response['is_error'] = true;
            $response['error'] = "Ошибка БД: ".$db->getError();
            die(json_encode($response));
        }
    } else {
        $response['is_error'] = true;
        $response['error'] = "Ошибка БД: ".$db->getError();
        die(json_encode($response));
    }
}
