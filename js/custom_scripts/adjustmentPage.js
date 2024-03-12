$(document).ready(function() {
    $('#responsiveTable').doubleScroll({resetOnWindowResize: true});
 });

function printAdjustment(id) {
    location.href = "print?template=protocol&id=" + id;
}

function editAdjustment(id) {
    location.href = "/conscription/editor?back=adjustment&id=" + id;
}

function deleteAdjustment(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            deleteConscription: {
                adjustmentID: id
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Призывник успешно удален");
                setInterval(() =>
                    location.href = '/adjustment', 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });
}