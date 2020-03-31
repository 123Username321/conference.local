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


$('#login-submit-button').on('click', function() {
    $('#login-form h6.error-msg').remove();
    
    $.ajax({
        url: '/actions/login-action.php',
        data: {
            email: $('#login-email-input').val(),
            password: $('#login-password-input').val()
        },
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.is_error === false) {
                location.reload();
            }
            else {
                if (response.error != null) {
                    $('#login-status').append(`<h6 class="error-msg">${response.error}</h6>`);
                }
                else {
                    for (var key in response.data) {
                        let parent = $(response.data[key].field).parent();
                        
                        for (var err_key in response.data[key].err_msg) {
                            parent.append(`<h6 class="error-msg">${response.data[key].err_msg[err_key]}</h6>`);
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
        url: '/actions/register-action.php',
        data: {
            name: $('#reg-name-input').val(),
            email: $('#reg-email-input').val(),
            password: $('#reg-password-input').val(),
            rep_password: $('#reg-rep-password-input').val(),
        },
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.is_error === false) {
                location.reload();
            }
            else {
                if (response.error != null) {
                    $('#register-status').append(`<h6 class="error-msg">${response.error}</h6>`);
                }
                else {
                    for (var key in response.data) {
                        let parent = $(response.data[key].field).parent();
                        
                        for (var err_key in response.data[key].err_msg) {
                            parent.append(`<h6 class="error-msg">${response.data[key].err_msg[err_key]}</h6>`);
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
