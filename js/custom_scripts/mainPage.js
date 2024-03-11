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
    new Chart(
        document.querySelector('.chartComplaint1'),
        {
            type: 'bar',
            data: {
                labels: ['Жалобы и консультации'],
                datasets: [
                    { label: 'Жалобы', data: [8] }, { label: 'Консультации', data: [5] }, { label: 'Не назначено', data: [3] }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Всего жалоб и консультаций: 16'
                    },
                    colors: {
                        forceOverride: true
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
                labels: ['Категории годности'],
                datasets: [
                    { label: 'А', data: [3] }, { label: 'Б', data: [6] }, { label: 'В', data: [12] }, { label: 'Г', data: [4] }, { label: 'Д', data: [1] }, { label: 'Обследования', data: [2] }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Категорий годности по жалобам: 28'
                    },
                    colors: {
                        forceOverride: true
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
                labels: ['Прибыло', 'Не прибыло', 'Утверждено', 'Отработка'],
                datasets: [
                    { label: 'Контроль', data: [3, 12, 16, 9] }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Контроль - всего: 22'
                    },
                    colors: {
                        forceOverride: true
                    }
                }
            }
        }
    );
});

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
                setInterval(() => location.reload(), 1000);
            } else {
                showLoading(false);
                showAlert(true, data, "danger", "", "col-8 col-xl-4 mx-auto");
            }
        }
    });
});

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
