/*
Файл, подключаемый custom-scripts.php на страницу /pattern
> содержит скрипты эмуляции POST запросов к postHandler'у
*/

//Обработка добавления шаблона
$("#addPatternForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();
    $('#editorPatternButton').attr('disabled', true);

    let patternNameInput = $("#patternName");
    if (isEmpty(patternNameInput.val()) || patternNameInput.val().length < 3) {
        if (!patternNameInput.hasClass("is-invalid"))
            patternNameInput.addClass("is-invalid");
        $('#editorPatternButton').attr('disabled', false);
        showLoading(false);
    }
    else {

        $.post({
            url: '/application/core/postHandler.php',
            method: 'post',
            dataType: 'text',
            data: {
                addPattern: {
                    patternName: $('#patternName').val(),
                    complaintTextarea: $('#complaintTextarea').val(),
                    anamnezTextarea: $('#anamnezTextarea').val(),
                    objectDataTextarea: $('#objectDataTextarea').val(),
                    specialResultTextarea: $('#specialResultTextarea').val(),
                    diagnosisTextarea: $('#diagnosisTextarea').val(),
                    healthCategorySelect: $('#healthCategorySelect').val(),
                    articleInput: $('#articleInput').val(),
                    reasonForCancelTextarea: $('#reasonForCancelTextarea').val()
                }
            },
            success: function (data) {
                window.scrollTo(0, 0);
                if (data == "reloadPage") {
                    showAlert(true, "Шаблон успешно добавлен");
                    setInterval(() =>
                        location.href = '/pattern', 1000
                    );
                } else {
                    showAlert(true, data, "danger", "");
                    showLoading(false);
                }
            }
        });
    }
});

//Обработка изменения шаблона
$("#editPatternForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();
    $('#editorPatternButton').attr('disabled', true);

    let patternNameInput = $("#patternName");
    if (isEmpty(patternNameInput.val()) || patternNameInput.val().length < 3) {
        if (!patternNameInput.hasClass("is-invalid"))
            patternNameInput.addClass("is-invalid");
        $('#editorPatternButton').attr('disabled', false);
        showLoading(false);
    }
    else {

        $.post({
            url: '/application/core/postHandler.php',
            method: 'post',
            dataType: 'text',
            data: {
                editPattern: {
                    patternID: $('#patternID').val(),
                    patternName: $('#patternName').val(),
                    complaintTextarea: $('#complaintTextarea').val(),
                    anamnezTextarea: $('#anamnezTextarea').val(),
                    objectDataTextarea: $('#objectDataTextarea').val(),
                    specialResultTextarea: $('#specialResultTextarea').val(),
                    diagnosisTextarea: $('#diagnosisTextarea').val(),
                    healthCategorySelect: $('#healthCategorySelect').val(),
                    articleInput: $('#articleInput').val(),
                    reasonForCancelTextarea: $('#reasonForCancelTextarea').val()
                }
            },
            success: function (data) {
                window.scrollTo(0, 0);
                if (data == "reloadPage") {
                    showAlert(true, "Изменения шаблона успешно сохранены");
                    setInterval(() =>
                        location.href = '/pattern', 1000
                    );
                } else {
                    showAlert(true, data, "danger", "");
                    showLoading(false);
                }
            }
        });
    }
});


function editPattern(id) {
    location.href = "/pattern/editor?id=" + id;
}

function deletePattern(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            deletePattern: {
                patternID: id
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Шаблон успешно удален");
                setInterval(() =>
                    location.href = '/pattern', 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });
}