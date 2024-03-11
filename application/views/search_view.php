<?
/*
Файл, подключаемый controller_search.php.
> содержит разметку страницы /search.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <h3 class="display-6 lh-1 mb-3">Поиск</h3>



    <div class="d-flex mb-3">
        <div class="me-3 w-50">
            <label for="searchValue" class="form-label">Поисковой запрос</label>
            <input type="text" class="form-control" id="searchValue"
                value='<? echo isset($_GET) ? $_GET["value"] : "" ?>' placeholder="Введите запрос..." required>
        </div>


        <div class="me-3 w-25">
            <label for="creationDate" class="form-label">Параметры</label>
            <select id="healtCategory" class="form-control form-select" style="cursor:pointer;">
                <?
                foreach (Config::getValue("searchType") as $key => $value) {
                    echo '<option value="' . $key . '">' . $value . '</option>';
                }
                ?>
            </select>
        </div>


        <div class="w-25 d-flex">
            <a href="" class="btn btn-outline-secondary w-100 align-self-end">Поиск</a>
        </div>
    </div>

    <script>
        document.getElementById('healtCategory').value = <? echo '"' . $_GET["type"] . '"' ?>;
    </script>

    <p class="lead mb-3 text-center">Список результатов пуст.</p>

</div>