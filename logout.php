<?php

session_start();
session_destroy();
header('Location: '.$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].'/index.php');

?>