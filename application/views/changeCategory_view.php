<?
/*
Файл, подключаемый controller_changeCategory.php.
> содержит разметку страницы /changeCategory.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 col m-0">Изменение категории</h3>
        <? if (Profile::isHavePermission("canAdd")) { ?>
            <a href="/document?documentType=changeCategory" class="btn btn-outline-success col-auto">Изменить категорию</a>
        <? } ?>
    </div>

    <div class="mt-3 d-flex">
        <input id="documentType" class="d-none" value="changeCategory">
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
            if (count($data["changeCategory"]) > 0) {
                foreach ($data["changeCategory"] as $value) {
                    echo DocumentBuilder::getConscriptWithDocumentsCard($value);
                }
            } else {
                echo "<p class='lead mb-3 text-center'>Список изменений категорий пуст.</p>";
            }
            ?>
        </div>
    </div>
</div>

<? 
if (isset($data["documentID"])) {
?>
<script>
document.addEventListener("DOMContentLoaded", function(event) {
    $("#searchType").val('id');
    <? echo "$('#searchDocumentInput').val(" . $data["documentID"] . ");"?>
    $("#searchDocumentInput").trigger("input");
});
</script>
<? 
} 
?>