<?
/*
Файл, подключаемый controller_return.php.
> содержит разметку страницы /return.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 col m-0">Возвраты</h3>
        <? if (Profile::isHavePermission("canAdd") && !Profile::isArchiveMode()) { ?>
            <a href="/document?documentType=return" class="btn btn-outline-success col-auto">Добавить возврат</a>
        <? } ?>
    </div>

    <ul class='nav nav-pills mt-3 mb-3 lead' id='pills-tab' role='tablist'>
        <li class='nav-item' role='presentation'>
            <button onclick="changeProccessMode(1);" class='nav-link active' id='pills-control-tab' data-bs-toggle='pill' data-bs-target='#pills-control'
                type='button' role='tab' aria-controls='pills-control' aria-selected='true'>Возвраты в работе</button>
        </li>
        <li class='nav-item' role='presentation'>
            <button onclick="changeProccessMode(0);" class='nav-link' id='pills-complaint-tab' data-bs-toggle='pill' data-bs-target='#pills-complaint'
                type='button' role='tab' aria-controls='pills-complaint' aria-selected='false'>Завершенные возвраты</button>
        </li>
    </ul>
    <input id="inProcess" class="d-none" value="1">

    <div class="mt-3 d-flex">
        <input id="documentType" class="d-none" value="return">
        <input type="text" id="searchDocumentInput" class="form-control me-2" placeholder="Введите запрос...">

        <select id="searchType" class="form-control form-select" style="width: 80%;cursor:pointer;">
            <?
            foreach (Config::getValue("searchDocumentType") as $key => $value) {
                echo '<option value="' . $key . '">' . $value . '</option>';
            }
            ?>
        </select>
    </div>


    <div id="searchResult" style="overflow: hidden;" class="mt-2">
        <div id="resizeDiv" class="d-grid gap-2">
            <?
            if (count($data["return"]) > 0) {
                foreach ($data["return"] as $value) {
                    echo DocumentBuilder::getConscriptWithDocumentsCard($value);
                }
            } else {
                echo "<p class='lead mb-3'>Документы не найдены.</p>";
            }
            ?>
        </div>
    </div>
</div>

<?
if (isset($data["documentID"])) {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            $("#searchType").val('id');
            <? echo "$('#searchDocumentInput').val(" . $data["documentID"] . ");" ?>
            $("#searchDocumentInput").trigger("input");
        });
    </script>
<?
}
?>