/*
Файл, подключаемый custom-scripts.php на главную страницу 
> содержит скрипты графиков (визуал) и эмуляции POST запросов к postHandler'у
*/

//Функция для выбора должности при авторизации
authPosition = function (positionIndex) {
    $("#authPosition").val(positionIndex);
    $("#positionText").val(positions[positionIndex]);
}

//Вывод графиков на главную страницу авторизованного пользователя
document.addEventListener('DOMContentLoaded', () => {
    let clownClick = 0;
    $.getJSON('/application/additions/statistic.php', function (result) {
        fillCharts(result);
        fillText(result["statisticText"]);
    });

    resizeCharts();
    $(window).on( "resize", function() {
        resizeCharts();
    });

    $('#clown').on( "click", function() {
        clownClick++;

        if(clownClick == 10 )
            location.href="/main/clown?rgnsk=clown";
    });
});

//Функция для ресайза чартов
function resizeCharts() {
    for (let id in Chart.instances) 
            Chart.instances[id].resize();
}

// Получение случайного цвета для chart'a из заданной цветовой палитры
function getRandomColorForCharts(sizeOfDataSet) {
    let arrOfColors = [];
    let palette = ['#1abc9c', '#2ecc71', '#3498db', '#9b59b6', 
    '#ff8c69', '#e67e22', '#e74c3c', '#e15f41', '#c44569'];

    for (let i = 0; i < sizeOfDataSet; i++) {
        let indexToDelete = Math.floor(Math.random() * 100) % palette.length;
        let randomColor = palette[indexToDelete];
        palette.splice(indexToDelete, 1);
        arrOfColors.push(randomColor);
    }
    return arrOfColors;
}

//Заполнение текста статистики из AJAX запроса
function fillText(statisticText) {
    $('#statisticText').empty();
    $('#statisticText').append(statisticText);
}

//Заполнение чартов из AJAX запроса
function fillCharts(statisticObject) {
    new Chart(
        document.querySelector('.chartAdjustment'),
        {
            type: 'pie',
            data: {
                labels: statisticObject["chartAdjustment"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartAdjustment"]["data"],
                        backgroundColor: getRandomColorForCharts(statisticObject["chartAdjustment"]["data"].length)
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: "bottom"
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartAdjustment"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                },
                responsive: true
            }
        }
    );

    new Chart(
        document.querySelector('.chartConscripts'),
        {
            type: 'line',
            data: {
                labels: statisticObject["chartConscripts"]["labels"],
                datasets: [
                    {
                        label: '',
                        data: statisticObject["chartConscripts"]["data"],
                        backgroundColor: getRandomColorForCharts(statisticObject["chartConscripts"]["data"].length)
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartConscripts"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                },
                responsive: true
            }
        }
    );
    

    new Chart(
        document.querySelector('.chartChangeCategory'),
        {
            type: 'bar',
            data: {
                labels: statisticObject["chartChangeCategory"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartChangeCategory"]["data"],
                        backgroundColor: getRandomColorForCharts(statisticObject["chartChangeCategory"]["data"].length)
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartChangeCategory"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                },
                responsive: true
            }
        }
    );

    new Chart(
        document.querySelector('.chartControl'),
        {
            type: 'bar',
            data: {
                labels: statisticObject["chartControl"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartControl"]["data"],
                        backgroundColor: getRandomColorForCharts(statisticObject["chartControl"]["data"].length)
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartControl"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                },
                responsive: true
            }
        }
    );

    new Chart(
        document.querySelector('.chartComplaint'),
        {
            type: 'bar',
            data: {
                labels: statisticObject["chartComplaint"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartComplaint"]["data"],
                        backgroundColor: getRandomColorForCharts(statisticObject["chartComplaint"]["data"].length)
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartComplaint"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                },
                responsive: true
            }
        }
    );

    new Chart(
        document.querySelector('.chartReturn'),
        {
            type: 'bar',
            data: {
                labels: statisticObject["chartReturn"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartReturn"]["data"],
                        backgroundColor: getRandomColorForCharts(statisticObject["chartReturn"]["data"].length)
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartReturn"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                },
                responsive: true
            }
        }
    );
}

//Обработка формы авторизации
$("#authForm").submit(function (event) {
    showLoading(true);
    event.preventDefault();

    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            authUser: {
                position: $('#authPosition').val()
            }
        },
        success: function (data) {
            if (data == "reloadPage") {
                showAlert(true, "Успешная авторизация", "success", "info", "col-8 col-xl-4 mx-auto");
                location.reload();
            } else {
                showLoading(false);
                showAlert(true, data, "danger", "", "col-8 col-xl-4 mx-auto");
            }
        }
    });
});

//Вход под учетной записью администратора
function loginAdmin() {
    $('#authPosition').val(0);
    $("#authForm").submit();
}

//Обработка кнопки выхода
$("#logout").click(function () {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            outUser: {
                param: 0
            }
        },
        success: function (data) {
            if (data == "reloadPage") {
                location.reload();
            } else if (data = "reloadPageArchive"){
                
                const url = new URL(document.location);
                const searchParams = url.searchParams;
                searchParams.set('archive', "rgnsk");
                window.history.pushState({}, '', url.toString());

                location.reload();
            } else {
                showLoading(false);
            }
        }
    });
});

$("#changeNameButton").click(function () {
    let icon = $("#changeNameIcon");
    let inp = $("#changeNameInput");

    icon.removeClass("fa-pencil");
    icon.addClass("fa-save");

    inp.removeClass("changeNameInput");
    inp.addClass("changeNameActiveInput");
    inp.prop('disabled', false);
    inp.focus();

    $("#changeNameButton").on("click.rgnsk", function() {
        $("#changeNameButton").off("click.rgnsk");
        saveNameChanges();
    });
});

function saveNameChanges() {
    let icon = $("#changeNameIcon");
    let inp = $("#changeNameInput");
    icon.addClass("fa-pencil");
    icon.removeClass("fa-save");
    inp.addClass("changeNameInput");
    inp.removeClass("changeNameActiveInput");
    inp.prop('disabled', true);

    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            changeUserName: {
                name: $("#changeNameInput").val()
            }
        },
        success: function (data) {
            if (data == "reloadPage") {
                location.reload();
            } else {
                showToast("Изменения сохранены.", "Данные учетной записи");
                showLoading(false);
            }
        }
    });
}

let prevSelectVal;
$('#archiveModeSelect').on('focus', function() {
    prevSelectVal = this.value;
});

$("#archiveModeSelect").change(function() {
    openAreYouSureModal('Вы уверены, что хотите выбрать базу данных ' + $("#archiveModeSelect").val() + '?', selectUserDatabase, $("#archiveModeSelect").val());
    $("#archiveModeSelect").val(prevSelectVal);
});

function selectUserDatabase(database) {
    showLoading(true);
    $.post({
        url: '/application/core/postHandler.php',
        method: 'post',
        dataType: 'text',
        data: {
            selectUserDatabase: {
                database: database
            }
        },
        success: function (data) {
            if (data == "reloadPage") {
                location.reload();
            } else {
                showLoading(false);
            }
        }
    });
}