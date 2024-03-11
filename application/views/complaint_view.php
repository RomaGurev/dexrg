<?
/*
Файл, подключаемый controller_complaint.php.
> содержит разметку страницы /complaint.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-8 col-lg-9">Жалобы</h3>
        <a href="/conscription/editor?back=complaint"
            class="btn btn-outline-success mb-3 float-end col-4 col-lg-3">Регистрация призывника</a>
    </div>

    <!--Тип документа: жалоба-->

    <p class="lead mb-3 text-center">Список жалоб пуст.</p>

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
                    <th scope="col" class="lead">Время призыва</th>
                    <th scope="col" class="lead">Диагноз</th>
                    <th scope="col" class="lead">Категория годности</th>
                    <th scope="col" class="lead">Итоговая статья</th>
                    <th scope="col" class="lead">Действия</th>
                </tr>
            </thead>
            <tbody>

                <? for ($i = 0; $i < 25; $i++) { ?>


                    <tr>
                        <td class='lead fs-6 text-center'>$id</td>
                        <td class='lead fs-6'>$name</td>
                        <td class='lead fs-6'>$creationDate</td>
                        <td class='lead fs-6'>-</td>
                        <td class='lead fs-6'>$documentType</td>
                        <td class='lead fs-6'>$voenkomat</td>
                        <td class='lead fs-6'>$adventTime</td>
                        <td class='lead fs-6'>$diagnosis</td>
                        <td class='lead fs-6'>$healtCategory</td>
                        <td class='lead fs-6'>$finalArticle</td>
                        <td class='lead fs-6'>$actions</td>
                    </tr>

                <? } ?>

            </tbody>
        </table>
    </div>

</div>