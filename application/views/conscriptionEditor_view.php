<?
/*
Файл, подключаемый controller_conscription.php.
> содержит разметку страницы /conscription/editor.
*/
$back = isset($_GET['back']) ? '/' . $_GET['back'] : '/';

if ($data["currentConscript"] != null) {
    $edit = true;
    $currentConscript = $data["currentConscript"][0];

    $data["documentType"] = $currentConscript["documentType"];
    $data["nextDocumentNumber"] = $currentConscript["documentNumber"];
}
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-9 col-lg-10"><? echo $edit ? "Редактирование призывника" : "Регистрация призывника"; ?></h3>
        <a onclick="location.href='<? echo $back ?>'"
            class="btn btn-outline-secondary mb-3 float-end col-3 col-lg-2">Назад</a>
    </div>

    <form <? echo $edit ? "id='editConscriptionForm'" : "id='addConscriptionForm'" ?> method="POST" class="mt-3 mb-0">

        <input type="text" id="documentType" value="<? echo $data["documentType"] ?>" class="d-none"></input>

        <?  
        if (isset($currentConscript["id"])) 
            echo "<input type='text' id='editID' value='" . $currentConscript["id"] . "' class='d-none'></input>";
        ?>

        <div class="mb-3 d-flex">
            <div class="me-3 w-25">
                <label for="docNumber" class="form-label">Номер документа*</label>
                <input type="number" class="form-control" id="docNumber" maxlength="10" value="<? echo $data["nextDocumentNumber"] ?>" required>
            </div>

            <div class="me-3 w-25">
                <label for="creationDate" class="form-label">Дата создания*</label>
                <input type="date" class="form-control" value="<? if (isset($currentConscript["creationDate"])): echo $currentConscript["creationDate"]; else: echo date("Y-m-d"); endif ?>" id="creationDate"
                    required></input>
            </div>

            <div class="w-50">
                <label for="pattern" class="form-label">Шаблон</label>
                <select id="pattern" <? echo count($data["patternList"]) > 0 ? "style='cursor:pointer;' class='form-control form-select'" : "class='form-control' disabled" ?>>
                    <?
                    if (count($data["patternList"]) > 0)
                        echo "<option value=''>Не выбрано</option>";
                    else
                        echo "<option value=''>Нет шаблонов</option>";

                    foreach ($data["patternList"] as $key => $value)
                        echo "<option value=" . $value["id"] . ">" . $value["name"] . "</option>";
                    ?>
                </select>
            </div>
        </div>


        <div class="mb-3">
            <label for="fullName" class="form-label">ФИО призывника*</label>
            <input type="text" class="form-control" id="fullName" placeholder="Пример: Иванов Иван Иванович"
                <?  if (isset($currentConscript["name"])) echo "value='" . $currentConscript["name"] . "'" ?>
                maxlength="255" required>
        </div>

        <div class="mb-3 d-flex">
            <div class="me-3 w-50">
                <label for="rvkArticle" class="form-label">Статья РВК</label>
                <input type="text" class="form-control" placeholder="Пример: 23в" id="rvkArticle" <?  if (isset($currentConscript["rvkArticle"])) echo "value='" . $currentConscript["rvkArticle"] . "'" ?> maxlength="10">
            </div>

            <div class="w-50">
                <label for="birthDate" class="form-label">Дата рождения</label>
                <input type="date" class="form-control" <?  if (isset($currentConscript["birthDate"])) echo "value='" . $currentConscript["birthDate"] . "'" ?> id="birthDate">
            </div>

        </div>


        <div class="mb-3">
            <label for="diagnosisTextarea" class="form-label">Диагноз</label>
            <textarea class="form-control" id="diagnosisTextarea" maxlength="1500" rows="5"
                placeholder="Пример: Отдалённые последствия черепно-мозговых травм"><?  if (isset($currentConscript["diagnosis"])) echo $currentConscript["diagnosis"] ?></textarea>
        </div>

        <div class="mb-3 d-flex">
            <div class="me-3 w-25">
                <label for="article" class="form-label">Статья</label>
                <input id="article" type="text" class="form-control" placeholder="Пример: 23в" <?  if (isset($currentConscript["article"])) echo "value='" . $currentConscript["article"] . "'" ?> maxlength="10">
            </div>

            <div class="me-3 w-25">
                <label for="healtCategory" class="form-label">Категория годности</label>
                <select id="healtCategory" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <option value="А">А - годен к военной службе</option>
                    <option value="Б">Б - годен к военной службе с незначительными ограничениями</option>
                    <option value="В">В - ограниченно годен к военной службе</option>
                    <option value="Г">Г - временно не годен к военной службе</option>
                    <option value="Д">Д - не годен к военной службе</option>
                </select>
            </div>

            <div class="me-3 w-25">
                <label for="vk" class="form-label">Военный комиссариат</label>
                <select id="vk" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <? foreach ($data["vkList"] as $key => $value)
                        echo "<option value=" . $key . ">" . $value["name"] . "</option>";
                    ?>
                </select>
            </div>

            <div class="w-25">
                <label for="adventTime" class="form-label">Период призыва</label>
                <select class="form-control form-select" id="adventTime" style="cursor:pointer;">
                    <?
                    $previousAdventPeriod = Helper::getPreviousAdventPeriod();
                    $currentBase = Database::getCurrentBase();
                    ?>
                    <option value="<? echo $currentBase ?>">
                        <? echo Helper::convertAdventPeriodToString($currentBase) ?>
                    </option>
                    <option value="<? echo $previousAdventPeriod ?>">
                        <? echo Helper::convertAdventPeriodToString($previousAdventPeriod) ?>
                    </option>
                </select>
            </div>

        </div>

        <button name="submit" type="submit" class="btn btn-outline-success w-25"><? echo $edit ? "Сохранить изменения" : "Зарегистрировать"; ?></button>
    </form>
</div>

<? 
$patternExistInPatternList = false;

for ($i=0; $i < count($data["patternList"]) && !$patternExistInPatternList; $i++) { 
    foreach ($data["patternList"][$i] as $key => $value) {
        if($key == "id") {
            if($value == $currentConscript["patternID"]) {
                $patternExistInPatternList = true;
                break;
            }
        }
    }
} 
    

if($edit) { 
?>
<script>
<?if (isset($currentConscript["patternID"]) && $patternExistInPatternList) echo "document.getElementById('pattern').value='" . $currentConscript["patternID"] . "';"?>
<?if (isset($currentConscript["healtCategory"])) echo "document.getElementById('healtCategory').value='" . $currentConscript["healtCategory"] . "';"?>
<?if (isset($currentConscript["vk"])) echo "document.getElementById('vk').value='" . $currentConscript["vk"] . "';"?>
<?if (isset($currentConscript["adventPeriod"])) echo "document.getElementById('adventTime').value='" . $currentConscript["adventPeriod"] . "';"?>
</script>
<? 
} 
?>