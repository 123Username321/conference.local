<?php

require_once 'psql_config.php';
require_once 'user.php';

session_start();
header('Content-Type: application/json; charset=UTF-8');

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
        echo json_encode(array('message' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/index.php', 'isSuccess' => true));
    }
}
else {
    echo json_encode(array('message' => 'Ошибка извлечения', 'isSuccess' => false));
}

$connection = null;
$result = null;

?>