$('#login-link').on('click', function() {
    $('#login-form').css({display: 'flex'});
});
$('#register-link').on('click', function() {
    $('#register-form').css({display: 'flex'});
});
$('#login-cancel-button').on('click', function() {
    $('#login-form').css({display: 'none'});
});
$('#reg-cancel-button').on('click', function() {
    $('#register-form').css({display: 'none'});
});

$('#person-agree-checkbox').on('change', function() {
    $('#reg-submit-button').prop("disabled", !($(this).is(':checked')));
});

// Переделать под контекст
$('#login-submit-button').on('click', function() {
    $('#login-form h6.error-msg').remove();
    
    $.ajax({
        url: 'login-action.php',
        data: {
            email: $('#login-email-input').val(),
            password: $('#login-password-input').val()
        },
        type: 'POST',
        dataType: 'json',
        success: function(responce) {
            if (responce.is_error === false) {
                location.reload();
            }
            else {
                if (responce.error != null) {
                    $('#login-status').append(`<h6 class="error-msg">${responce.error}</h6>`);
                }
                else {
                    for (var key in responce.data) {
                        let parent = $(responce.data[key].field).parent();
                        
                        for (var err_key in responce.data[key].err_msg) {
                            parent.append(`<h6 class="error-msg">${responce.data[key].err_msg[err_key]}</h6>`);
                        }
                    }
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Неизвестная ошибка");
        }
    });
});

$('#reg-submit-button').on('click', function() {
    $('#register-form h6.error-msg').remove();
    
    $.ajax({
        url: 'register-action.php',
        data: {
            name: $('#reg-name-input').val(),
            email: $('#reg-email-input').val(),
            password: $('#reg-password-input').val(),
            rep_password: $('#reg-rep-password-input').val(),
        },
        type: 'POST',
        dataType: 'json',
        success: function(responce) {
            if (responce.is_error === false) {
                location.reload();
            }
            else {
                if (responce.error != null) {
                    $('#register-status').append(`<h6 class="error-msg">${responce.error}</h6>`);
                }
                else {
                    for (var key in responce.data) {
                        let parent = $(responce.data[key].field).parent();
                        
                        for (var err_key in responce.data[key].err_msg) {
                            parent.append(`<h6 class="error-msg">${responce.data[key].err_msg[err_key]}</h6>`);
                        }
                    }
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Неизвестная ошибка");
        }
    });
});