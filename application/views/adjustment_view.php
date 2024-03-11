<?
/*
Файл, подключаемый controller_adjustment.php.
> содержит разметку страницы /adjustment.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-8 col-lg-9">Отработка</h3>
        <a href="/conscription/editor?back=adjustment"
            class="btn btn-outline-success mb-3 float-end col-4 col-lg-3">Регистрация призывника</a>
    </div>

    <!--<a href="/" class="btn btn-outline-dark col-6 col-lg-3">Вывести должников</a>-->
    <!--Тип документа: Контроль, отработка-->
    
    <?
    $adjustmentList = $data['adjustmentList'];
    if (count($adjustmentList) > 0) {
    ?>


    <div class="table-responsive" id="responsiveTable">
        <table class="table table-striped table-bordered table-hover">
            <thead class="text-center">
                <tr>
                    <th scope="col" class="lead">№</th>
                    <th scope="col" class="lead">ФИО</th>
                    <th scope="col" class="lead">Дата создания</th>
                    <th scope="col" class="lead">Статья РВК</th>
                    <th scope="col" class="lead">Тип документа</th>
                    <th scope="col" class="lead">Военкомат</th>
                    <th scope="col" class="lead">Период призыва</th>
                    <th scope="col" class="lead">Диагноз</th>
                    <th scope="col" class="lead">Категория годности</th>
                    <th scope="col" class="lead">Итоговая статья</th>
                    <th scope="col" class="lead">Действия</th>
                </tr>
            </thead>
            <tbody>
                <? 
                foreach ($adjustmentList as $key => $value) {
                    echo "<tr>
                    <td class='lead fs-6 text-center'>" . $value["documentNumber"] . "</td>
                    <td class='lead fs-6'>" . $value["name"] . "</td>
                    <td class='lead fs-6'>" . $value["creationDate"] . "</td>
                    <td class='lead fs-6'>" . $value["rvkArticle"] . "</td>
                    <td class='lead fs-6'>" . $value["documentType"] . "</td>
                    <td class='lead fs-6'>" . $value["vk"] . "</td>
                    <td class='lead fs-6'>" . $value["adventPeriod"] . "</td>
                    <td class='lead fs-6'>" . $value["diagnosis"] . "</td>
                    <td class='lead fs-6'>" . $value["healtCategory"] . "</td>
                    <td class='lead fs-6'>" . $value["article"] . "</td>
                    <td class='lead fs-6'>$actions</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <? 
    } else {
        echo '<p class="lead mb-3 text-center">Список отработки пуст.</p>';
    }
    ?>
</div>