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
        console.log(result);

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

function resizeCharts() {
    for (let id in Chart.instances) 
            Chart.instances[id].resize();
}

// Получение случайного цвета для chart'a из заданной цветовой палитры
function getRandomColorForCharts(sizeOfDataSet) {
    let arrOfColors = [];
    /*let palette = ['#f3a683', '#f7d794', '#778beb', '#e77f67', '#cf6a87', 
                    '#f19066', '#f5cd79', '#546de5', '#e15f41', '#c44569',
                    '#786fa6', '#f8a5c2', '#63cdda', '#ea8685',
                    '#574b90', '#f78fb3', '#3dc1d3', '#e66767']; */

    let palette = ['#1abc9c', '#2ecc71', '#3498db', '#9b59b6', 
    '#f1c40f', '#e67e22', '#e74c3c', '#e15f41', '#c44569'];

    for (let i = 0; i < sizeOfDataSet; i++) {
        let indexToDelete = Math.floor(Math.random() * 100) % palette.length;
        let randomColor = palette[indexToDelete];
        palette.splice(indexToDelete, 1);
        arrOfColors.push(randomColor);
    }
    return arrOfColors;
}

function fillText(statisticText) {
    $('#statisticText').empty();
    $('#statisticText').append(statisticText);
}

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
            } else {
                showLoading(false);
            }
        }
    });
});

//Нажатие на блок возвратов
$("#returnBlock").click(function () {
    location.href = "/return";
});
//Нажатие на блок обследований
$("#inspectionBlock").click(function () {
    location.href = "/inspection";
});
