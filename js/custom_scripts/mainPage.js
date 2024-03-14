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
    $.getJSON('/application/additions/statistic.php', function (result) {
        console.log(result);
        fillCharts(result);
    });
});


// Получение случайного цвета для bar'a из стандартной цветовой палитры Chart.js
function getRandomColorForBars(sizeOfDataSet) {
    let arrOfColors = [];
    let standartColors = ['rgb(255, 205, 86)', 'rgb(255, 64, 105)', 'rgb(255, 159, 64)', 'rgb(54, 162, 235)'];

    for (let i = 0; i < sizeOfDataSet; i++) {
        if (i < standartColors.length) {
            arrOfColors.push(standartColors[i]);
        } else {
            let colorComponents = standartColors[i % standartColors.length].match(/\d+/g);
            let newColorFromStandart = 'rgb(' + (colorComponents[0] - Math.floor(Math.random() * 100) % 50).toString() + ', ' +
                (colorComponents[1] - Math.floor(Math.random() * 100) % 50).toString() + ', ' +
                (colorComponents[2] - Math.floor(Math.random() * 100) % 50).toString() + ')';
            arrOfColors.push(newColorFromStandart);
        }
    }
    return arrOfColors;
}

function fillCharts(statisticObject) {


    new Chart(
        document.querySelector('.chartComplaint1'),
        {
            type: 'bar',
            data: {
                labels: statisticObject["chartComplaint"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartComplaint"]["data"],
                        backgroundColor: getRandomColorForBars(statisticObject["chartComplaint"]["data"].length)
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
                }
            }
        }
    );

    new Chart(
        document.querySelector('.chartComplaint2'),
        {
            type: 'bar',
            data: {
                labels: statisticObject["chartHealthCategory"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartHealthCategory"]["data"],
                        backgroundColor: getRandomColorForBars(statisticObject["chartHealthCategory"]["data"].length)
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
                        text: statisticObject["chartHealthCategory"]["titleText"]
                    },
                    colors: {
                        forceOverride: false
                    }
                }
            }
        }
    );

    new Chart(
        document.querySelector('.chartAdjustment'),
        {
            type: 'doughnut',
            data: {
                labels: statisticObject["chartAdjustment"]["labels"],
                datasets: [
                    {
                        data: statisticObject["chartAdjustment"]["data"]
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: statisticObject["chartAdjustment"]["titleText"]
                    },
                    colors: {
                        forceOverride: true
                    }
                }
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
