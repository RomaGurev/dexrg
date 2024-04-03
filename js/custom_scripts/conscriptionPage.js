/*
Файл, подключаемый custom-scripts.php на страницу /conscription
> содержит скрипты эмуляции POST запросов к postHandler'у
*/

//Выбор категории "Г" и появление поля "Срок отсрочки"
$("#healthCategory").on("change", function (event) {
    let valueSelected = $("#healthCategory").val();

    if(valueSelected == "Г") {
        $("#postPeriod").removeClass("d-none"); //class remove d-none
    } else {
        $("#postPeriod").addClass("d-none"); //class add d-none
        $("#postPeriodSelect").val("");
    }
});

//Обработка добавления призывника
$("#addConscriptForm").submit(function (event) {
    showLoading(true);
    $('#editorConscriptButton').attr('disabled', true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            addConscript: {
                creationDate: $('#creationDate').val(),
                creatorID: $('#creatorID').val(),
                fullName: $('#fullName').val(),
                rvkArticle: $('#rvkArticle').val(),
                rvkDiagnosis: $('#diagnosisTextarea').val(),
                birthDate: $('#birthDate').val(),
                healthCategory: $('#healthCategory').val(),
                vk: $('#vk').val(),
                adventTime: $('#adventTime').val(), 
                postPeriodSelect: $('#postPeriodSelect').val(),
                rvkProtocolDate: $('#rvkProtocolDate').val(),
                rvkProtocolNumber: $('#rvkProtocolNumber').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Призывник успешно зарегистрирован");
                setInterval(() =>
                    location.href = '/?conscript=' + $('#conscriptNumber').val(), 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
                $('#editorConscriptButton').attr('disabled', false);
            }
        }
    });
});

//Обработка изменения призывника
$("#editConscriptForm").submit(function (event) {
    showLoading(true);
    $('#editorConscriptButton').attr('disabled', true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            editConscript: {
                id: $('#conscriptNumber').val(),
                creationDate: $('#creationDate').val(),
                creatorID: $('#creatorID').val(),
                fullName: $('#fullName').val(),
                rvkArticle: $('#rvkArticle').val(),
                rvkDiagnosis: $('#diagnosisTextarea').val(),
                birthDate: $('#birthDate').val(),
                healthCategory: $('#healthCategory').val(),
                vk: $('#vk').val(),
                adventTime: $('#adventTime').val(),
                postPeriodSelect: $('#postPeriodSelect').val(),
                rvkProtocolDate: $('#rvkProtocolDate').val(),
                rvkProtocolNumber: $('#rvkProtocolNumber').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Изменения призывника успешно сохранены");

                setInterval(() =>
                    location.href = '/?conscript=' + $('#conscriptNumber').val(), 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
                $('#editorConscriptButton').attr('disabled', false);
            }
        }
    });


});