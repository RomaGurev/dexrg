<?
/*
Файл, подключаемый controller_control.php.
> содержит разметку страницы /control.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 col m-0">Контроль</h3>
        <? if (Profile::isHavePermission("canAdd")) { ?>
            <a href="/document?documentType=control" class="btn btn-outline-success col-auto">Добавить контроль</a>
        <? } ?>
    </div>

    <div class="mt-3 d-flex">
        <input id="documentType" class="d-none" value="control">
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
        <div id="resizeDiv">
            <?
            if (count($data["control"]) > 0) {
                foreach ($data["control"] as $value) {
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