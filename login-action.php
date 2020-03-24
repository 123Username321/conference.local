<?php

require_once 'psql_config.php';
require_once 'user.php';

session_start();
if (isUser()) die ('Действие запрещено');
header('Content-Type: application/json; charset=UTF-8');


$responce = [
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


for ($i = 0; $i < 2; $i++) {
    $error_array = [];
    if (!isset($responce['data'][$i]['value']) || strlen($responce['data'][$i]['value']) === 0) {
        $responce['is_error'] = true;
        array_push($responce['data'][$i]['err_msg'], 'Данное поле обязательно для заполнения');
    }
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

    $_POST['password'] = md5($_POST['password']);

    $query = $connection->prepare("SELECT * FROM users WHERE email = ?");
        
    if ($query->execute([$_POST['email']]) === true) {
        $result = $query->fetchAll();

        if (count($result) === 0 || $result[0]['password'] !== $_POST['password']) {
            $responce['is_error'] = true;
            $responce['error'] = 'Неверный логин или пароль';
            die (json_encode($responce));
        }
        else {
            $_SESSION['user'] = new User($result[0]['id'], $result[0]['name'], $result[0]['email'], $result[0]['access_level']);
            echo json_encode($responce);
        }
    }
    else {
        $responce['is_error'] = true;
        $responce['error'] = 'Не удалось выполнить запрос, попробуйте позднее';
        die (json_encode($responce));
    }
}

$connection = null;
$result = null;





















/*
foreach ($responce['data'] as $elem) {
    $error_array = [];
    if (!isset($elem['value']) || strlen($elem['value']) === 0) {
        $responce['is_error'] = true;
        array_push($error_array, 'Данное поле обязательно для заполнения'); 
    }
    //if (count($error_array) > 0) $elem['err_msg'] = $error_array;
    $elem['err_msg'] = "12345";
}
*/

/*
if ($responce['is_error'] === true) die(json_encode($responce));
else {

    try {
        $connection = new PDO("pgsql:host=$host;port=$port;dbname=$db;user=$user");
    } 
    catch (PDOException $e) {
        echo json_encode(array('message' => 'Не удалось подключиться к базе данных.', 'isSuccess' => false));
    }

    $_POST['password'] = md5($_POST['password']);

    $query = $connection->prepare("SELECT * FROM users WHERE email = ?");
        
    if ($query->execute([$_POST['email']]) === true) {
        $result = $query->fetchAll();

        if (count($result) === 0 || $result[0]['password'] !== $_POST['password']) {
            echo json_encode(array('message' => 'Неверный логин или пароль', 'isSuccess' => false));
        }
        else {
            $_SESSION['user'] = new User($result[0]['id'], $result[0]['name'], $result[0]['email'], $result[0]['access_level']);
            //echo json_encode(array('message' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/index.php', 'isSuccess' => true));
            echo json_encode(array('message' => 'Успешный вход', 'isSuccess' => true));
        }
    }
    else {
        echo json_encode(array('message' => 'Ошибка извлечения', 'isSuccess' => false));
    }

    $connection = null;
    $result = null;
}
*/

?>