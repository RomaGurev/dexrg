<?
/*
Файл, подключаемый controller_complaint.php.
> содержит разметку страницы /complaint.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-8 col-lg-9">DEBUG-вывод</h3>
        <a href="/conscription/editor?back=complaint"
            class="btn btn-outline-success mb-3 float-end col-4 col-lg-3">Регистрация призывника</a>
    </div>

    <?
    $conscriptList = $data['conscriptList'];
    if (count($conscriptList) > 0) {
    ?>


    <div class="table-responsive" id="responsiveTable">
        <table class="table table-striped table-bordered table-hover">
            <thead class="text-center">
                <tr>
                    <th scope="col" class="lead">№</th>
                    <th scope="col" class="lead">ФИО</th>
                    <th scope="col" class="lead">Дата создания</th>
                    <th scope="col" class="lead">Статья РВК</th>
                    <th scope="col" class="lead">Военкомат</th>
                    <th scope="col" class="lead">Период призыва</th>
                    <th scope="col" class="lead">Категория годности</th>
                    <th scope="col" class="lead">В работе</th>
                    <th scope="col" class="lead">Действия</th>
                </tr>
            </thead>
            <tbody>
                <? 
                foreach ($conscriptList as $key => $value) {
                    echo "<tr>
                    <td class='lead fs-6 text-center'>" . $value["id"] . "</td>
                    <td class='lead fs-6'>" . $value["name"] . "</td>
                    <td class='lead fs-6'>" . Helper::formatDateToView($value["creationDate"]) . "</td>
                    <td class='lead fs-6'>" . $value["rvkArticle"] . "</td>
                    <td class='lead fs-6'>" . Helper::getVKNameById($value["vk"])["name"] . "</td>
                    <td class='lead fs-6'>" . Helper::convertAdventPeriodToString($value["adventPeriod"]) . "</td>
                    <td class='lead fs-6'>" . $value["healthCategory"] . "</td>
                    <td class='lead fs-6'>" . ($value["inProcess"] == 1 ? "TRUE" : "FALSE") . "</td>
                    <td class='lead fs-6 text-center'>" . 
                    "<button onclick='printComplaint(". $value['id'] . ")' class='btn btn-outline-dark w-75 mb-1'>Печать</button>" .
                    "<button onclick='editComplaint(". $value['id'] . ")' class='btn btn-outline-dark w-75 mb-1'>Редактировать</button>" . 
                    "<button onclick='deleteComplaint(". $value['id'] . ")' class='btn btn-outline-danger w-75 text-center'>Удалить</button>" .
                    "</td>
                    </tr>";
                }  
                ?>
            </tbody>
        </table>
    </div>

    <? 
    } else {
        echo '<p class="lead mb-3 text-center">Список жалоб пуст.</p>';
    }
    ?>

</div>