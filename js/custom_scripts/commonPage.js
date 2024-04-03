const isEmpty = str => !str.trim().length;

//Поиск призывника
$("#searchInput").on("input", function () {
    let searchValue = $("#searchInput").val().trim();
    $("#searchResult").empty();
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            searchConscript: {
                type: $("#searchType").val(),
                value: searchValue,
                showSelect: $("#showSelect").val()
            }
        },
        success: function (data) {
            $("#searchResult").append(data);
            showLoading(false);
            let newheight = data == "" ? 0 : $('#resizeDiv').height();
            $("#searchResult").stop().animate({ height: newheight });
        }
    });
});
//Поиск призывника

//Поиск документа
$("#searchDocumentInput").on("input", function () {
    let searchValue = $("#searchDocumentInput").val().trim();
    $("#searchResult").empty();
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            searchDocument: {
                type: $("#searchType").val(),
                documentType: $("#documentType").val(),
                value: searchValue,
            }
        },
        success: function (data) {
            $("#searchResult").append(data);
            showLoading(false);
            let newheight = data == "" ? 0 : $('#resizeDiv').height();
            $("#searchResult").stop().animate({ height: newheight });
        }
    });
});
//Поиск документа

//Поиск
$("#searchType").on("change", function() {
    $("#searchInput").trigger("input");
    $("#searchDocumentInput").trigger("input");
});

$(window).on("resize", function() {
    let newheight = $('#resizeDiv').length ? $('#resizeDiv').height() : 0;
    $("#searchResult").stop().animate({ height: newheight });
});
//Поиск

//Модальные окна
openConscriptModal = function (conscriptID) {
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            getConscriptInfoForModal: {
                conscriptID: conscriptID
            }
        },
        success: function (data) {
            $("#modalContent").empty();
            $("#modalContent").append(data);

            new bootstrap.Modal(document.getElementById('RGModal')).show();
        }
    });
    
}

openAreYouSureModal = function (contentText, callback, callbackParam) {
    $("#areYouSureModalContent").empty();
    $("#areYouSureModalContent").append(contentText);
    
    new bootstrap.Modal(document.getElementById('areYouSureModal')).show();

    $('#areYouSureModalConfirm').on("click", function() {
        callback(callbackParam);
        $('#openAreYouSureModal').modal().hide();
        $("#areYouSureModalClose").click()
    });
}
//Модальные окна

//Действия с УКП
function printProtocol(id) {  
    saveProtocolChanges(function() {
        location.href = "print?template=protocol&id=" + id;
    });
}
function printLetter(id) {
    saveProtocolChanges(function() {
        location.href = "print?template=letter&id=" + id;
    });
}
function printExtract(id) {
    saveProtocolChanges(function() {
        location.href = "print?template=extract&id=" + id;
    });
}
function editConscript(id) {
    location.href = "/conscription/editor?back=&id=" + id;
}
function deleteConscript(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            deleteConscript: {
                conscriptID: id
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Призывник успешно удален");
                setInterval(() =>
                    location.href = '/', 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });
}
function addChangeCategory(id) {
    location.href = "/document?conscript=" + id + "&documentType=changeCategory";
}
function addControl(id) {
    location.href = "/document?conscript=" + id + "&documentType=control";
}
function addReturn(id) {
    location.href = "/document?conscript=" + id + "&documentType=return";
}
function addComplaint(id) {
    location.href = "/document?conscript=" + id + "&documentType=complaint";
}

saveProtocolChanges = function(func) {
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            saveProtocolChanges: {
                conscriptID: $("#protocolConscriptID").val(),
                protocolDate: $("#protocolDate").val(),
                protocolNumber: $("#protocolNumber").val(),
            }
        },
        success: function (data) {
            if(data == "continue")
                func();
        }
    });
}
//Действия с УКП

//Загрузка
showLoading = function (state) {
    let spinner = $("#spinner");
    if (state) {
        if (spinner.hasClass("d-none"))
            spinner.removeClass("d-none");
    }
    else {
        if (!spinner.hasClass("d-none"))
            spinner.addClass("d-none");
    }
}
//Загрузка

//Alert
showAlert = function (state, data = "", type = "success", icon = "info", size = "") {
    let alert = $('#alertResult');
    let iconResult = "";

    switch (icon) {
        case "info":
            iconResult = "<svg style='width: 16px; height: 16px;' class='me-3'><image xlink:href='/images/icons/info-circle.svg'></image></svg>";
            break;
    }

    if (state) {
        if (alert.hasClass("d-none"))
            alert.removeClass("d-none");

        alert.html("<div class='" + size + " alert alert-" + type + " d-flex align-items-center'>" + iconResult + data + "</div>");
    }
    else {
        if (!alert.hasClass("d-none"))
            alert.addClass("d-none");
    }
}
//Alert