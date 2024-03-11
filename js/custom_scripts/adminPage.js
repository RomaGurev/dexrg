/*
Файл, подключаемый custom-scripts.php на страницу /admin
> содержит скрипты упрощающие работу в панели администратора.
*/

//Функция для выбора должности при добавлении аккаунта
selectPosition = function (positionIndex) {
    $("#userAccountPosition").val(positionIndex);
    $("#positionText").val(positions[positionIndex]);
}

//Изменение поля "Активна" учетной записи
changeIsEmployed = function (userID) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            changeIsEmployed: {
                userID: userID
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            $('#adminTableMessage').html(data);
            showLoading(false);
        }
    });
}


//Изменение специальности учетной записи
changePosition = function (userID, newPosition) {
    showLoading(true);
    $("#positionDropdown" + userID).text(newPosition + " - " + positions[newPosition]);

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            changePosition: {
                userID: userID,
                userPosition: newPosition
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            $('#adminTableMessage').html(data);
            showLoading(false);
        }
    });
}

selectBase = function (dbName) {
    let input = $("#selectedBase");
    input.val(dbName);

    if (!input.hasClass("is-valid"))
        input.addClass("is-valid");
    
    let dbname = dbName.split('-');
    let outstring = dbname[1] == 1 ? "Весна" : "Осень";

    $('#selectBaseValidMessage').css("display", "block");
    $('#selectBaseValidMessage').text('Выбор базы: ' + outstring + ' ' + dbname[0]);
    $('#selectBaseButton').attr('disabled', false);
}

//Обработка формы добавления аккаунта
$("#addUserForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            addAccount: {
                name: $('#userAccountName').val(),
                position: $('#userAccountPosition').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if(data == "success") 
                showAlert(true, "Аккаунт добавлен");
            else 
                showAlert(true, data, "danger", "");
            showLoading(false);
        }
    });
});

//Валидация ввода (создания базы)
$("#createBaseInput").on("input", function (event) {
    let input = $("#createBaseInput");
    let value = $("#createBaseInput").val();
    let regExp = /20\d{2}-[1-2]/;
    if (input.hasClass("is-valid"))
        input.removeClass("is-valid");
    if (input.hasClass("is-invalid"))
        input.removeClass("is-invalid");
    $('#createBaseButton').attr('disabled', true);
    
    if (value.length == 6) {
        if (regExp.test(value)) {
            input.addClass("is-valid");

            let dbname = value.split('-');
            let outstring = dbname[1] == 1 ? "Весна" : "Осень";

            $('#createBaseValidMessage').text('Создание базы: ' + outstring + ' ' + dbname[0]);
            $('#createBaseButton').attr('disabled', false);
        } else {
            input.addClass("is-invalid");
        }
    }
});

//Создание базы
$("#createBaseForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            createBase: {
                baseName: $('#createBaseInput').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if(data == "success")
                showAlert(true, "Новая база данных успешно инициализирована");
            else 
                showAlert(true, data, "danger", "")
            showLoading(false);
        }
    });
});

//Выбор актуальной базы
$("#selectBaseForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            selectCurrentBase: {
                baseName: $('#selectedBase').val()
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if(data == "success")
                showAlert(true, "База данных выбрана для работы");
            else 
                showAlert(true, data, "danger", "")
            showLoading(false);
        }
    });
});