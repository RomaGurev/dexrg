const isEmpty = str => !str.trim().length;

changeSearchType = function (type, value) {
    $("#searchInput").attr("placeholder", value + "...");
    $("#searchType").val(type);
}

search = function () {
    let searchType = $("#searchType").val();
    let searchInput = $("#searchInput");

    if (isEmpty(searchInput.val())) {
        if (!searchInput.hasClass("is-invalid"))
            searchInput.addClass("is-invalid");
    }
    else
        location.href = "/search?type=" + searchType + "&value=" + searchInput.val();
}

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