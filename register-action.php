<?php

require_once 'psql_config.php';
require_once 'user.php';

session_start();
header('Content-Type: application/json; charset=UTF-8');
// if (isUser()) die(json_encode([ 'status' => 'restrict' ]));


$responce = [
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
    if (!isset($responce['data'][$i]['value']) || strlen($responce['data'][$i]['value']) === 0) {
        $responce['is_error'] = true;
        array_push($responce['data'][$i]['err_msg'], 'Данное поле обязательно для заполнения');
    }
    else if (strlen($responce['data'][$i]['value']) > $responce['data'][$i]['max_length']) {
        $responce['is_error'] = true;
        array_push($responce['data'][$i]['err_msg'], 'Поле должно содержать не более '.$responce['data'][$i]['max_length'].' символов');
    }
}

if (strcmp($responce['data'][3]['value'], $responce['data'][2]['value']) != 0) {
    $responce['is_error'] = true;
    array_push($responce['data'][3]['err_msg'], 'Повтор пароля не совпадает');
}

if (preg_match("/[^а-яА-Я \-]{1,}/u", $responce['data'][0]['value']) == 1) {
    $responce['is_error'] = true;
    array_push($responce['data'][0]['err_msg'], 'Допустимы только русские буквы, пробелы и дефисы');
}

if (filter_var($responce['data'][1]['value'], FILTER_VALIDATE_EMAIL) == false) {
    $responce['is_error'] = true;
    array_push($responce['data'][1]['err_msg'], 'Указан невалидный email');
}

if (strlen($responce['data'][2]['value']) < 6) {
    $responce['is_error'] = true;
    array_push($responce['data'][2]['err_msg'], 'Пароль должен содержать как минимум 6 символов');
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

    $query = $connection->prepare("SELECT id FROM users WHERE email = ?");

    if ($query->execute([$_POST['email']])) {
        $result = $query->fetchAll();

        if (count($result) > 0) {
            $responce['is_error'] = true;
            $responce['error'] = 'Данный email уже зарегистрирован';
            die (json_encode($responce));
        }
        else {
            $query = $connection->prepare("INSERT INTO users VALUES (DEFAULT, ?, ?, ?, 1) RETURNING id");
            if ($query->execute([$_POST['name'], $_POST['email'], $_POST['password']])) {
                $result = $query->fetchAll();

                if (!is_dir("users\\".$_POST['email'])) mkdir("users\\".$_POST['email']);

                if (is_dir("users\\".$_POST['email'])) {
                    $_SESSION['user'] = new User($result[0]['id'], $_POST['name'], $_POST['email'], 1);
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
                $responce['error'] = 'Не удалось выполнить запрос, попробуйте позднее';
                die (json_encode($responce));
            }
        }
    }
    else {
        $responce['is_error'] = true;
        $responce['error'] = 'Не удалось выполнить запрос, попробуйте позднее';
        die (json_encode($responce));
    }

    $connection = null;
    $result = null;
}

?>