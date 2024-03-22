<?
/*
Файл, подключаемый controller_conscription.php.
> содержит разметку страницы /conscription/editor.
*/
$back = isset($_GET['back']) ? '/' . $_GET['back'] : '/';

if ($data["currentConscript"] != null) {
    $edit = true;
    $currentConscript = $data["currentConscript"][0];
}
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-9 col-lg-10"><? echo $edit ? "Редактирование призывника" : "Регистрация призывника"; ?></h3>
        <a onclick="location.href='<? echo $back ?>'"
            class="btn btn-outline-secondary mb-3 float-end col-3 col-lg-2">Назад</a>
    </div>

    <form <? echo $edit ? "id='editConscriptForm'" : "id='addConscriptForm'" ?> method="POST" class="mt-3 mb-0">

        <?  
        if (isset($currentConscript["id"])) 
            echo "<input type='text' id='editID' value='" . $currentConscript["id"] . "' class='d-none'></input>";
        ?>

        <div class="mb-3 d-flex">
            <div class="me-3 w-25">
                <label for="conscriptNumber" class="form-label">Уникальный номер призывника</label>
                <input type="number" class="form-control" id="conscriptNumber" maxlength="10" value="<? echo $edit ? $currentConscript["id"] : $data["nextConscriptID"];  ?>" disabled>
            </div>

            <div class="col">
                <label for="creationDate" class="form-label">Дата прибытия*</label>
                <input type="date" class="form-control" value="<? if (isset($currentConscript["creationDate"])): echo $currentConscript["creationDate"]; else: echo date("Y-m-d"); endif ?>" id="creationDate"
                    required></input>
            </div>
        </div>

        <div class="mb-3 d-flex">
            <div class="me-3 w-50">
                <label for="fullName" class="form-label">ФИО призывника*</label>
                <input type="text" class="form-control" id="fullName" placeholder="Пример: Иванов Иван Иванович"
                <?  if (isset($currentConscript["name"])) echo "value='" . $currentConscript["name"] . "'" ?>
                maxlength="255" required>
            </div>

            <div class="me-3 col">
                <label for="birthDate" class="form-label">Дата рождения</label>
                <input type="date" class="form-control" <?  if (isset($currentConscript["birthDate"])) echo "value='" . $currentConscript["birthDate"] . "'" ?> id="birthDate">
            </div>

            <div class="col">
                <label for="healthCategory" class="form-label">Категория годности</label>
                <select id="healthCategory" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <option value="А">А - годен к военной службе</option>
                    <option value="Б">Б - годен к военной службе с незначительными ограничениями</option>
                    <option value="В">В - ограниченно годен к военной службе</option>
                    <option value="Г">Г - временно не годен к военной службе</option>
                    <option value="Д">Д - не годен к военной службе</option>
                </select>
            </div>

        </div>

        <div class="mb-3 d-flex">
            <div class="me-3 col">
                <label for="rvkArticle" class="form-label">Статья РВК</label>
                <input type="text" class="form-control" placeholder="Пример: 23в" id="rvkArticle" <?  if (isset($currentConscript["rvkArticle"])) echo "value='" . $currentConscript["rvkArticle"] . "'" ?> maxlength="10">
            </div>

            <div class="me-3 col">
                <label for="vk" class="form-label">Военный комиссариат</label>
                <select id="vk" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <? foreach ($data["vkList"] as $key => $value)
                        echo "<option value=" . $key . ">" . $value["name"] . "</option>";
                    ?>
                </select>
            </div>

            <div class="col">
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

        <button name="submit" type="submit" class="btn btn-outline-success w-25"><? echo $edit ? "Сохранить изменения" : "Зарегистрировать призывника"; ?></button>
    </form>
</div>

<? 
if($edit) { 
?>
<script>
<?if (isset($currentConscript["healthCategory"])) echo "document.getElementById('healthCategory').value='" . $currentConscript["healthCategory"] . "';"?>
<?if (isset($currentConscript["vk"])) echo "document.getElementById('vk').value='" . $currentConscript["vk"] . "';"?>
<?if (isset($currentConscript["adventPeriod"])) echo "document.getElementById('adventTime').value='" . $currentConscript["adventPeriod"] . "';"?>
</script>
<? 
} 
?>