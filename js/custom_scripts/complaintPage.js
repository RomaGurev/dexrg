$(document).ready(function() {
    $('#responsiveTable').doubleScroll({resetOnWindowResize: true});
 });

function printComplaint(id) {
    location.href = "print?template=protocol&id=" + id;
}

function editComplaint(id) {
    location.href = "/conscription/editor?back=complaint&id=" + id;
}

function deleteComplaint(id) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            deleteConscript: {
                complaintID: id
            }
        },
        success: function (data) {
            window.scrollTo(0, 0);
            if (data == "reloadPage") {
                showAlert(true, "Призывник успешно удален");
                setInterval(() =>
                    location.href = '/complaint', 1000
                );
            } else {
                showAlert(true, data, "danger", "");
                showLoading(false);
            }
        }
    });
}