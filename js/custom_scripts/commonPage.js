const isEmpty = str => !str.trim().length;

document.addEventListener('DOMContentLoaded', () => {
    $(".autogrow").autogrow();

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});

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
            resizeResultDiv();
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
                inProcess: $("#inProcess").val(),
                value: searchValue,
            }
        },
        success: function (data) {
            $("#searchResult").append(data);
            showLoading(false);
            resizeResultDiv();
        }
    });
});
//Поиск документа

//Поиск шаблона
$("#searchPatternInput").on("input", function () {
    let searchValue = $("#searchPatternInput").val().trim();
    $("#searchResult").empty();
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            searchPattern: {
                value: searchValue
            }
        },
        success: function (data) {
            $("#searchResult").append(data);
            showLoading(false);
            resizeResultDiv();
        }
    });
});
//Поиск шаблона

//Поиск
$("#searchType").on("change", function() {
    $("#searchInput").trigger("input");
    $("#searchDocumentInput").trigger("input");
});

changeProccessMode = function(value) {
    $("#inProcess").val(value);
    $("#searchDocumentInput").trigger("input");
};

$(window).on("resize", function() {
    resizeResultDiv();
});
//Поиск

resizeResultDiv = function () {
    let newheight = $('#resizeDiv').length ? $('#resizeDiv').height() : 0;
    $("#searchResult").stop().animate({ height: newheight });
}

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

            const url = new URL(document.location);
            const searchParams = url.searchParams;
            searchParams.set('conscript', conscriptID);
            window.history.pushState({}, '', url.toString());
            new bootstrap.Modal(document.getElementById('RGModal')).show();
        }
    });
}

//Всплывающиее сообщение
showToast = function (message, title) {
    let name = "toast" + Math.round(Math.random()*100);
    $("#toastContainer").append('<div id="' + name + '" class="toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-header"><svg width="18" height="18" class="me-2"><image xlink:href="/images/icons/info-circle.svg" width="18" height="18" /></svg><strong class="me-auto">' + title + '</strong><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button></div><div class="toast-body">' + message + '</div></div>');
    let rgToast = bootstrap.Toast.getOrCreateInstance($("#" + name));
    rgToast.show();

    $("#" + name).on('hidden.bs.toast', function () {
        $("#" + name).remove();
    })
}

$("#RGModal").on("hidden.bs.modal", function () {
    const url = new URL(document.location);
    const searchParams = url.searchParams;
    searchParams.delete('conscript');
    window.history.pushState({}, '', url.toString());
});

function openAreYouSureModal(contentText, callback, callbackParam) {
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

function addDocument(id) {
    let docType = document.querySelector("#addDocumentType");
    location.href = "/document?conscript=" + id + "&documentType=" + docType.value;
}

function subscribeCollapseObjects() {
    $('.collapse').on("hidden.bs.collapse", function() {
        resizeResultDiv();
    });
    $('.collapse').on("shown.bs.collapse", function() {
        resizeResultDiv();
    });
}

saveProtocolChanges = function(func) {
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            saveProtocolChanges: {
                conscriptID: $("#protocolConscriptID").val(),
                letterNumber: $("#letterNumber").val(),
                protocolDate: $("#protocolDate").val(),
                protocolNumber: $("#protocolNumber").val(),
            }
        },
        success: function (data) {
            if(data == "continue" && func != null)
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

//Блокировка УКП
function setInProcessFalse(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            setInProcessFalse: {
                conscriptID: id
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            showLoading(false);
        }
    });
}

//Разблокировка УКП после обследования
function unlockCard(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            unlockCard: {
                conscriptID: id
            }
        },
        success: function (data) {
            location.reload();
        }
    });
}