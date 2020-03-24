<?php

require_once 'user.php';

session_start();

$title = 'Войти в систему';

?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('chunks/head.html'); ?>

    <style>
    .flex-baseline {
        
    }
    </style>
</head>
<body>
    <?php require_once 'chunks/header.html'; ?>

    <?php if (isUser()): ?>
        <h3>Вы уже в системе</h3>
        <a href=index.php>Вернуться на главную</a>

    <?php else: ?>
        <script>
            $(document).ready(function() { 
                $('#checkbox-agree').change(function() {
                    if (this.checked) {
                        $('#submit-button').prop('disabled', false);
                    }
                    else $('#submit-button').prop('disabled', true);
                });


                $('#submit-button').on('click', function() {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: 'login_action.php',
                        data: { 
                            email: $('#email-input').val(),
                            password: $('#password-input').val()
                        },
                        success: function(data) { 
                            if (data.isSuccess === true) window.location = data.message;
                            else {
                                $('body').append('<p class="error">' + data.message + '</p>');
                            }
                        },
                        error: function(data) {
                            $('body').append('<h5 style="color: red;">' + 'Неопознанная ошибка' + '</h5>');
                        }
                    });
                });
            });
        </script>

        <div class="back-container">
            <div class="login-container">
                <div class="input-block">
                    <h6>E-mail:</h6>
                    <input type="text" name="email" id="email-input" autocomplete="on">
                </div>
                <div class="input-block">
                    <h6>Пароль:</h6>
                    <input type="text" name="password" id="password-input" autocomplete="on">
                </div>
                <div class="flex-baseline">
                    <input type="checkbox" id="checkbox-agree">
                    <label>Запомнить на этом устройстве</label>
                </div>
                <div class="send-actions">
                    <button class="btn btn-info" id='submit-button' disabled>Войти</button>
                    <a href="index.php"><button class="btn btn-secondary">Отмена</button></a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</body>
</html>
