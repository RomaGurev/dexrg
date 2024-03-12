/*
Файл, подключаемый custom-scripts.php на страницу /conscription
> содержит скрипты эмуляции POST запросов к postHandler'у
*/

//Обработка добавления призывника
$("#addConscriptionForm").submit(function (event) {

    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            addConscription: {
                docNumber: $('#docNumber').val(),
                creationDate: $('#creationDate').val(),
                pattern: $('#pattern').val(),
                fullName: $('#fullName').val(),
                rvkArticle: $('#rvkArticle').val(),
                birthDate: $('#birthDate').val(),
                diagnosisTextarea: $('#diagnosisTextarea').val(),
                article: $('#article').val(),
                healtCategory: $('#healtCategory').val(),
                vk: $('#vk').val(),
                adventTime: $('#adventTime').val(),
                documentType: $('#documentType').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Призывник успешно зарегистрирован");
                setInterval(() =>
                    location.href = '/' + $('#documentType').val(), 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });
});

//Обработка изменения призывника
$("#editConscriptionForm").submit(function (event) {

    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            editConscription: {
                id: $('#editID').val(),
                docNumber: $('#docNumber').val(),
                creationDate: $('#creationDate').val(),
                pattern: $('#pattern').val(),
                fullName: $('#fullName').val(),
                rvkArticle: $('#rvkArticle').val(),
                birthDate: $('#birthDate').val(),
                diagnosisTextarea: $('#diagnosisTextarea').val(),
                article: $('#article').val(),
                healtCategory: $('#healtCategory').val(),
                vk: $('#vk').val(),
                adventTime: $('#adventTime').val(),
                documentType: $('#documentType').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Изменения призывника успешно сохранены");
                let url = new URL(location.href);
                let backParam = url.searchParams.get("back"); 
                setInterval(() =>
                    location.href = '/' + backParam, 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });


});