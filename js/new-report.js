$('#upload-files-button').on('click', function(event) {
    $('#report-form h6.error-msg').remove();
    $('#report-message-block').append(`<h6 class="info-msg">Загрузка данных, ожидайте</h6>`);
    
    let fileInputs = $('.file-input'), 
        formdata = new FormData();
        
    formdata.append('name', $('#name-input').val());
    formdata.append('speaker_info', $('#speaker-info-textarea').val());
    formdata.append('category', $('#category-selector').prop('selectedIndex'));
    formdata.append('description', $('#description-textarea').val());
    formdata.append('userfiles[]', fileInputs[0].files[0]);
    formdata.append('userfiles[]', fileInputs[1].files[0]);
    
    $.ajax({
        url: 'upload-action.php',
        data: formdata,
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(responce) {
            $('#report-form h6.info-msg').remove();
            if (responce.is_error === false) {
                $('#report-form').remove();
                $('main').append('<div class="action-result" id="report-action-result"></div>');
                $('#report-action-result').append('<h5>Заявка успешно добавлена</h5>');
                $('#report-action-result').append('<a href="index.php"><button class="btn btn-info">Вернуться</button></a>');
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
            $('#report-message-block h6.info-msg').remove();
            console.log("Неизвестная ошибка");
            $('#report-form').append(`<h6 class="error-msg">Непредвиденная ошибка: ${textStatus}</h6>`);
        }
    });
});