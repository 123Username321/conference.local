<?php

require_once 'user.php';

session_start();

//header('HTTP/1.0 403 Forbidden');
//die('You are forbidden!');

?>


<!DOCTYPE html>
<html>
<head>
    <?php require_once('chunks/head.html'); ?>
</head>
<body>
    <?php if (isUser()): ?>
        <h4>Вы уже в системе</4h>
        <a href="index.php">Вернуться на главную</a>

    <?php else: ?>
        <script>
            $(document).ready(function() { 
                $('#submit-button').on('click', function() {
                    $.ajax({
                        type: 'POST',
                        url: 'register_action.php',
                        dataType: 'json',
                        data: { 
                            name: $('#name-input').val(),
                            email: $('#email-input').val(),
                            password: $('#password-input').val()
                        },
                        success: function(data) {
                            if (data.isSuccess === true) window.location = data.message;
                            else {
                                $('body').append('<h5 style="color: red;">' + data.message + '</h5>');
                            }
                        },
                        error: function(data) {
                            $('body').append('<h5 style="color: red;">' + 'Неопознанная ошибка' + '</h5>');
                        }
                    });
                });
            });
        </script>

        <form>
            <input type='text' placeholder='Имя' id='name-input' name='name'>
            <input type='text' placeholder='E-mail' id='email-input' name='email'>
            <input type='text' placeholder='Пароль' id='password-input' name='password'>
            <input type='text' placeholder='Повторите пароль' id='password-repeat-input' name='password-repeat'>
        </form>
        <button id='submit-button'>Submit</button>

    <?php endif; ?>
</body>
</html>
