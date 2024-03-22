<?
/*
Файл, подключаемый controller_changeCategory.php.
> содержит разметку страницы /changeCategory/editor.
> используется для изменения категории годности призывника
-- Пример: A1 -> B3 (причины)
*/

if($data['selectedConscript'] != null) {
    echo "<script>document.addEventListener('DOMContentLoaded', () => {
        select(" . $data['selectedConscript']['id'] . ", '" . $data['selectedConscript']['name'] . (!empty($data['selectedConscript']['birthDate']) ? ' [' . Helper::formatDateToView($data['selectedConscript']['birthDate']) . ']' : '') . "');
    });</script>";
}
?>

<script>
    function select(id, name) {
        $('#conscriptID').val(id);
        $('#resultName').val(name);
        $('#search').addClass('d-none');
        $('#creationForm').removeClass('d-none');
    }
</script>


<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col">Изменение категории</h3>
        <a href="/changeCategory" class="btn btn-outline-secondary mb-3 float-end col-auto">Назад</a>
    </div>

    <input class="d-none" type="text" id="conscriptID" placeholder="ID призывника">
    <input id="showSelect" class="d-none" value="true">

    <div id="search">
        <label for="searchInput" class="form-label">Призывник</label>

        <div class="d-flex">
            <input id="searchInput" class="form-control me-2" placeholder="Введите ФИО призывника...">
            <select id="searchType" class="form-control form-select" style="width: 80%;cursor:pointer;">
            <?
            foreach (Config::getValue("searchType") as $key => $value) {
                echo '<option value="' . $key . '">' . $value . '</option>';
            }
            ?>
            </select>
        </div>
        <div id="searchResult" style="overflow: hidden; height: 0px;" class="mt-2"></div>
    </div>


    <div id="creationForm" class="d-none">
        <form <? echo $edit ? "id='editChangeCategoryForm'" : "id='addChangeCategoryForm'" ?> method="POST"
            class="mt-3 mb-0">

            <div class="mb-3 d-flex">
                <div class="me-3 w-50">
                    <label for="resultName" class="form-label">Призывник</label>
                    <input type="text" id="resultName" class="form-control" disabled>
                </div>
                <div class="w-50">
                    <label for="documentID" class="form-label">ID документа</label>
                    <input class="form-control" id="documentID"></textarea>
                </div>
            </div>

            <div class="mb-3 d-flex">
                <div class="me-3 w-50">
                    <label for="complaintTextarea" class="form-label">Жалобы</label>
                    <textarea class="form-control" id="complaintTextarea" maxlength="1000" rows="4"
                        placeholder="Пример: Головокружение при перемене положения, с тошнотой, головные боли в височно-теменной области, давящего характера, слабость, потливость."><? if (isset($currentPattern["complaint"]))
                            echo $currentPattern["complaint"]; ?></textarea>
                </div>
                <div class="w-50">
                    <label for="anamnezTextarea" class="form-label">Анамнез</label>
                    <textarea class="form-control" id="anamnezTextarea" maxlength="1000" rows="4"><? if (isset($currentPattern["anamnez"]))
                        echo $currentPattern["anamnez"]; ?></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label for="objectDataTextarea" class="form-label">Данные объективного исследования</label>
                <textarea class="form-control" id="objectDataTextarea" maxlength="1500" rows="6"><? if (isset($currentPattern["objectData"]))
                    echo $currentPattern["objectData"]; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="specialResultTextarea" class="form-label">Результаты специальных исследований</label>
                <textarea class="form-control" id="specialResultTextarea" maxlength="1500" rows="6"><? if (isset($currentPattern["specialResult"]))
                    echo $currentPattern["specialResult"]; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="diagnosisTextarea" class="form-label">Диагноз</label>
                <textarea class="form-control" id="diagnosisTextarea" maxlength="1500" rows="5"
                    placeholder="Пример: Отдалённые последствия черепно-мозговых травм"><? if (isset($currentPattern["diagnosis"]))
                        echo $currentPattern["diagnosis"]; ?></textarea>
            </div>

            <button id="editorPatternButton" name="submit" type="submit" class="btn btn-outline-success w-25">
                <? echo $edit ? "Сохранить изменения" : "Изменить категорию"; ?>
            </button>
        </form>
    </div>
</div>