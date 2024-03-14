/*
Файл, подключаемый custom-scripts.php на страницу /conscription
> содержит скрипты эмуляции POST запросов к postHandler'у
*/

//Обработка добавления призывника
$("#addConscriptForm").submit(function (event) {
    showLoading(true);
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
                birthDate: $('#birthDate').val(),
                healthCategory: $('#healthCategory').val(),
                vk: $('#vk').val(),
                adventTime: $('#adventTime').val(),
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Призывник успешно зарегистрирован");
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

//Обработка изменения призывника
$("#editConscriptForm").submit(function (event) {

    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            editConscript: {
                id: $('#editID').val(),
                creationDate: $('#creationDate').val(),
                creatorID: $('#creatorID').val(),
                fullName: $('#fullName').val(),
                rvkArticle: $('#rvkArticle').val(),
                birthDate: $('#birthDate').val(),
                healthCategory: $('#healthCategory').val(),
                vk: $('#vk').val(),
                adventTime: $('#adventTime').val(),
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