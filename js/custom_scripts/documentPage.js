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

//Выбор категории "Г" и появление поля "Срок отсрочки"
$("#healthCategorySelect").on("change", function (event) {
    let valueSelected = $("#healthCategorySelect").val();

    if(valueSelected == "Г") {
        $("#postPeriod").removeClass("d-none"); //class remove d-none
        $("#healthCategory").addClass("me-3"); //class add me-3
    } else {
        $("#postPeriod").addClass("d-none"); //class add d-none
        $("#healthCategory").removeClass("me-3") //class remove me-3
        $("#postPeriodSelect").val("");
    }
});

//Применение шаблона
$("#pattern").on("change", function (event) {
    showLoading(true);
    event.preventDefault();
    let valueSelected = $("#pattern").val();

    if (valueSelected == "") {
        $('#complaintTextarea').val("");
        $('#anamnezTextarea').val("");
        $('#objectDataTextarea').val("");
        $('#specialResultTextarea').val("");
        $('#diagnosisTextarea').val("");
        $('#healthCategorySelect').val("");
        $('#articleInput').val("");
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
                $('#reasonForCancelTextarea').val(dataPattern["reasonForCancel"]);

                $('#complaintTextarea').trigger("change");
                $('#anamnezTextarea').trigger("change");
                $('#objectDataTextarea').trigger("change");
                $('#specialResultTextarea').trigger("change");
                $('#diagnosisTextarea').trigger("change");
                $('#healthCategorySelect').trigger("change");
                $('#articleInput').trigger("change");
                $('#reasonForCancelTextarea').trigger("change");

                showToast('Шаблон ' + dataPattern["name"] + ' загружен.', 'Данные документа');
            }
        });
    }
});

//Копирование диагноза РВК
copyRvkDiagnosis = function() {
    showLoading(true);

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            getRvkDiagnosisByConscriptID: {
                conscriptID: $('#conscriptID').val()
            }
        },
        success: function (data) {
            showLoading(false);
            if(data.length > 0)
                $('#diagnosisTextarea').val(data);
            else
                showToast('Диагноз РВК пуст.', 'Данные документа');
        }
    });
};

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
                documentType: $('#documentType').val(),
                postPeriodSelect: $('#postPeriodSelect').val(),
                reasonForCancelTextarea: $('#reasonForCancelTextarea').val(),
                documentDate: $('#documentDate').val(),
                destinationPointsInput: $('#destinationPointsInput').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Документ успешно добавлен");

                if($('#documentType').val() == "confirmation")
                    setInProcessFalse($('#conscriptID').val());

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
                documentID: $('#documentID').val(),
                postPeriodSelect: $('#postPeriodSelect').val(),
                reasonForCancelTextarea: $('#reasonForCancelTextarea').val(),
                documentDate: $('#documentDate').val(),
                destinationPointsInput: $('#destinationPointsInput').val()
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