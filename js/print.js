var input = document.querySelectorAll('input');
input.forEach(element => {
    element.addEventListener('input', resizeInput)
});

document.addEventListener('DOMContentLoaded', () => {
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

	document.querySelectorAll(".contenteditable").forEach(el => {
        el.addEventListener("keydown", function (el) {
            if (el.keyCode == 8 || el.keyCode == 46) {
                if (this.innerText.length < 1) {
                    el.preventDefault();
                    this.innerText = "";
                }
            }
        });
    });
});

function resizeInput() {
    this.style.width = this.value.length + "ch";

    if (this.value.length < 1)
        this.style.width = "50px";
}

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


function saveProtocolValuesChanges(id) {
    showLoading(true);

    $('#protocolChanges').removeClass('d-none');
    $('#print_content').addClass('protocol-padding');
    
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            saveProtocolValuesChanges: {
                conscriptID: id,
                name: $('#name').text(),
                birthDate: $('#birthDate').text(),
                rvkDiagnosis: $('#rvkDiagnosis').text(),
                complaint: $('#complaint').text(),
                anamnez: $('#anamnez').text(),
                objectData: $('#objectData').html(),
                specialResult: $('#specialResult').html(),
                diagnosis: $('#diagnosis').text()
            }
        },
        success: function (data) {
            showLoading(false);
        }
    });
}

function deleteProtocolValuesChanges(id) {
    showLoading(true);

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            deleteProtocolValuesChanges: {
                conscriptID: id
            }
        },
        success: function (data) {
            showLoading(false);
            location.reload();
        }
    });
}