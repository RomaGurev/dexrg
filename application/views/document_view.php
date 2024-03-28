<?
/*
Файл, подключаемый controller_document.php.
> содержит разметку страницы /document.
> используется для добаления документов
*/

if ($data["currentConscript"] != null) {
    $currentConscript = $data["currentConscript"];

    echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            select(" . $currentConscript['id'] . ", '" . $currentConscript['name'] . (!empty($currentConscript['birthDate']) ? ' [' . Helper::formatDateToView($currentConscript['birthDate']) . ']' : '') . "');
        });
        </script>";
}

if ($data["currentDocument"] != null) {
    $edit = true;
    $currentDocument = $data["currentDocument"];
}

$documentName = $edit ? "Редактирование документа - " . Config::getValue("documentType")[$_GET["documentType"]] : "Добавление документа - " . Config::getValue("documentType")[$_GET["documentType"]];
?>



<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 col m-0"><? echo $documentName ?></h3>
        <a href="/" class="btn btn-outline-secondary col-auto w-10">Назад</a>
    </div>

    <input class="d-none" type="text" id="conscriptID">
    <input class="d-none" type="text" id="documentType" value="<? echo $_GET['documentType'] ?>">
    <input id="showSelect" class="d-none" value="true">

    <div id="search" class="mt-3">
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
        <form <? echo $edit ? "id='editDocumentForm'" : "id='addDocumentForm'" ?> method="POST"
            class="mt-3 mb-0">

            <div class="mb-3 d-flex">
                <div class="col me-3">
                    <label for="resultName" class="form-label">Призывник</label>
                    <input type="text" id="resultName" class="form-control" disabled>
                </div>

                <div class="col me-3">
                    <label for="documentID" class="form-label">ID документа</label>
                    <input class="form-control" value="<? echo $edit ? $currentDocument['id'] : $data['nextDocumentID'] ?>" id="documentID" disabled>
                </div>

                <div class="col">
                    

                <label for="pattern" class="form-label">Шаблон</label>
                <select id="pattern" <? echo count($data["patternList"]) > 0 ? "style='cursor:pointer;' class='form-control form-select'" : "class='form-control' disabled" ?>>
                    <?
                    if (count($data["patternList"]) > 0)
                        echo "<option value=''>Выбрать шаблон</option>";
                    else
                        echo "<option value=''>Нет шаблонов</option>";

                    foreach ($data["patternList"] as $key => $value)
                        echo "<option value=" . $value["id"] . ">" . $value["name"] . "</option>";
                    ?>
                </select>

                </div>
            </div>

            <div class="mb-3 d-flex">
                <div class="me-3 w-50">
                    <label for="complaintTextarea" class="form-label">Жалобы</label>
                    <textarea class="form-control" id="complaintTextarea" maxlength="1000" rows="4"
                        placeholder="Пример: Головокружение при перемене положения, с тошнотой, головные боли в височно-теменной области, давящего характера, слабость, потливость."><? if (isset($currentDocument["complaint"]))
                            echo $currentDocument["complaint"]; ?></textarea>
                </div>
                <div class="w-50">
                    <label for="anamnezTextarea" class="form-label">Анамнез</label>
                    <textarea class="form-control" id="anamnezTextarea" maxlength="1000" rows="4"><? if (isset($currentDocument["anamnez"]))
                        echo $currentDocument["anamnez"]; ?></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label for="objectDataTextarea" class="form-label">Данные объективного исследования</label>
                <textarea class="form-control" id="objectDataTextarea" maxlength="2500" rows="6"><? if (isset($currentDocument["objectData"]))
                    echo $currentDocument["objectData"]; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="specialResultTextarea" class="form-label">Результаты специальных исследований</label>
                <textarea class="form-control" id="specialResultTextarea" maxlength="2500" rows="6"><? if (isset($currentDocument["specialResult"]))
                    echo $currentDocument["specialResult"]; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="diagnosisTextarea" class="form-label">Диагноз</label>
                <textarea class="form-control" id="diagnosisTextarea" maxlength="1500" rows="5"
                    placeholder="Пример: Отдалённые последствия черепно-мозговых травм"><? if (isset($currentDocument["diagnosis"]))
                        echo $currentDocument["diagnosis"]; ?></textarea>
            </div>

            <div class="mb-3 d-flex">
            <div class="col me-3">
                <label for="articleInput" class="form-label">Статья</label>
                <input type="text" class="form-control" id="articleInput" maxlength="10" <? if (isset($currentDocument["article"]))
            echo "value='" . $currentDocument["article"] . "'" ?>
                    placeholder="Пример: 23в">
            </div>

            <div class="col">
                <label for="healthCategorySelect" class="form-label">Категория годности</label>
                <select id="healthCategorySelect" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <? 
                    foreach (Helper::getHealthCategories($_GET['documentType']) as $key => $value) {
                        echo "<option value='$key'>«" . $key . "» - $value</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

            

            <div class="row">
            <div class="col-auto">
                <button id="saveButton" name="submit" type="submit" class="btn btn-outline-success"><? echo $edit ? "Сохранить изменения" : "Добавить документ"; ?></button>
            </div>
            <?
            if($edit) {
            ?>
            <div class="col"></div>
            <div class="col-auto">
                <button type="button" onclick="deleteDocument(<?echo $currentDocument['id']?>);" class="btn btn-outline-danger">Удалить документ</button>
            </div>
            <?
            }
            ?>
        </div>
        </form>
    </div>
</div>

<?
if ($edit) {
    echo "<script>
        document.getElementById('healthCategorySelect').value = '" . $currentDocument['healthCategory'] . "';
    </script>";
}