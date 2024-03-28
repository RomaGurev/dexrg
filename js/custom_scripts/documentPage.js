/*
Файл, подключаемый custom-scripts.php на страницу /document
> содержит скрипты эмуляции POST запросов к postHandler'у
> содержит функции подгрузки шаблонов в runtime
*/

//Выбор призывника из поиска
function select(id, name) {
    $('#conscriptID').val(id);
    $('#resultName').val(name);
    $('#search').addClass('d-none');
    $('#creationForm').removeClass('d-none');
} 

//Применение шаблона
$("#pattern").on("change", function (event) {
    showLoading(true);
    event.preventDefault();
    let valueSelected = $("#pattern").val();

    if (isEmpty(valueSelected)) {
        showLoading(false);
    }
    else {
        $.post({
            url: '/application/core/postHandler.php',
            method: 'post',
            dataType: 'text',
            data: {
                getPatternByID: {
                    patternID: valueSelected,
                }
            },
            success: function (data) {
                showLoading(false);
                let dataPattern = JSON.parse(data);
                $('#complaintTextarea').val(dataPattern["complaint"]);
                $('#anamnezTextarea').val(dataPattern["anamnez"]);
                $('#objectDataTextarea').val(dataPattern["objectData"]);
                $('#specialResultTextarea').val(dataPattern["specialResult"]);
                $('#diagnosisTextarea').val(dataPattern["diagnosis"]);
                $('#healthCategorySelect').val(dataPattern["healthCategory"]);
                $('#articleInput').val(dataPattern["article"]);
            }
        });
    }
});


//Обработка добавления документа
$("#addDocumentForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();
    $('#saveButton').attr('disabled', true);

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            addDocument: {
                conscriptID: $('#conscriptID').val(),
                articleInput: $('#articleInput').val(),
                healthCategorySelect: $('#healthCategorySelect').val(),
                complaintTextarea: $('#complaintTextarea').val(),
                anamnezTextarea: $('#anamnezTextarea').val(),
                objectDataTextarea: $('#objectDataTextarea').val(),
                specialResultTextarea: $('#specialResultTextarea').val(),
                diagnosisTextarea: $('#diagnosisTextarea').val(),
                documentType: $('#documentType').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Документ успешно добавлен");
                setInterval(() =>
                    location.href = '/?conscript=' + $('#conscriptID').val(), 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                $('#saveButton').attr('disabled', false);
                showLoading(false);
            }
        }
    });
});

//Обработка изменения документа
$("#editDocumentForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();
    $('#saveButton').attr('disabled', true);

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            editDocument: {
                articleInput: $('#articleInput').val(),
                healthCategorySelect: $('#healthCategorySelect').val(),
                complaintTextarea: $('#complaintTextarea').val(),
                anamnezTextarea: $('#anamnezTextarea').val(),
                objectDataTextarea: $('#objectDataTextarea').val(),
                specialResultTextarea: $('#specialResultTextarea').val(),
                diagnosisTextarea: $('#diagnosisTextarea').val(),
                documentType: $('#documentType').val(),
                documentID: $('#documentID').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Изменения успешно сохранены");
                setInterval(() =>
                    location.href = '/?conscript=' + $('#conscriptID').val(), 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                $('#saveButton').attr('disabled', false);
                showLoading(false);
            }
        }
    });
});

//Обработка удаления документа
function deleteDocument(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            deleteDocument: {
                documentID: id
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Документ успешно удален");
                setInterval(() =>
                    location.href = '/?conscript=' + $('#conscriptID').val(), 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });
}