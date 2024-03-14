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
                value: searchValue
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


$("#searchType").on("change", function() {
    $("#searchInput").trigger("input");
});

$(window).on( "resize", function() {
    let newheight = $('#resizeDiv').length ? $('#resizeDiv').height() : 0;
    $("#searchResult").stop().animate({ height: newheight });
});
//Поиск призывника

//Открытие модального окна с УКП
openConscriptModal = function (conscriptID) {
    alert(conscriptID);
}
//Открытие модального окна с УКП

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