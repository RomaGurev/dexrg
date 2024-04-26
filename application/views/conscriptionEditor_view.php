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
        <h3 class="display-6 lh-1 col m-0"><? echo $edit ? "Редактирование призывника" : "Регистрация призывника"; ?></h3>
        <a onclick="history.back();"
            class="btn btn-outline-secondary col-auto w-10">Назад</a>
    </div>

    <form <? echo $edit ? "id='editConscriptForm'" : "id='addConscriptForm'" ?> method="POST" class="mt-3 mb-0">

        <div class="mb-3 d-flex">
                <input type="number" id="conscriptNumber" class="d-none" value="<? echo $currentConscript["id"]; ?>">

            <div class="col me-3">
            <label for="vk" class="form-label">Военный комиссариат</label>
                <select id="vk" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <? foreach ($data["vkList"] as $key => $value)
                        echo "<option value=" . $value["id"] . ">" . $value["name"] . "</option>";
                    ?>
                </select>
            </div>

            <div class="col">
                <label for="creationDate" class="form-label">Дата прибытия*</label>
                <input type="date" class="form-control" value="<? if (isset($currentConscript["creationDate"])): echo date('Y-m-d',strtotime($currentConscript["creationDate"])); else: echo date("Y-m-d"); endif ?>" id="creationDate"
                    required></input>
            </div>
        </div>

        <div class="mb-3 d-flex">
            <div class="col me-3">
                <label for="fullName" class="form-label">ФИО призывника*</label>
                <input type="text" class="form-control" id="fullName"
                <?  if (isset($currentConscript["name"])) echo "value='" . $currentConscript["name"] . "'" ?>
                maxlength="255" required>
            </div>

            <div class="col me-3">
                <label for="birthDate" class="form-label">Дата рождения</label>
                <input type="date" class="form-control" <?  if (isset($currentConscript["birthDate"])) echo "value='" .  date('Y-m-d', strtotime($currentConscript["birthDate"])) . "'" ?> id="birthDate">
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

        <div class="mb-3">
                <label for="diagnosisTextarea" class="form-label">Диагноз РВК</label>
                <textarea class="form-control autogrow" id="diagnosisTextarea" maxlength="2500" rows="5"><? if (isset($currentConscript["rvkDiagnosis"])) echo $currentConscript["rvkDiagnosis"]; ?></textarea>
        </div>

        <div class="mb-3 d-flex">
            <div class="col me-3">
                <label for="healthCategory" class="form-label">Категория годности РВК</label>
                <select id="healthCategory" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <? 
                    foreach (Helper::getHealthCategories("registration") as $key => $value) {
                        echo "<option value='$key'>«" . $key . "» - $value</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="postPeriod" class="col me-3 d-none">
                <label for="postPeriodSelect" class="form-label">Срок отсрочки</label>
                <select id="postPeriodSelect" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <? 
                    foreach (Config::getValue("postPeriod") as $key => $value) {
                        echo "<option value='$key'>$value</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col">
                <label for="rvkArticle" class="form-label">Статья РВК</label>
                <input type="text" class="form-control" id="rvkArticle" <?  if (isset($currentConscript["rvkArticle"])) echo "value='" . $currentConscript["rvkArticle"] . "'" ?> maxlength="300">
            </div>
        </div>

        <div class="mb-3 d-flex">
            <div class="col me-3">
                <label for="rvkProtocolDate" class="form-label">Дата протокола РВК</label>
                <input type="date" class="form-control" id="rvkProtocolDate" <?  if (!empty($currentConscript["rvkProtocolDate"])) echo "value='" . date('Y-m-d',strtotime($currentConscript["rvkProtocolDate"])) . "'" ?>>
            </div>
            <div class="col">
                <label for="rvkProtocolNumber" class="form-label">Номер протокола РВК</label>
                <input type="text" class="form-control" id="rvkProtocolNumber" <?  if (isset($currentConscript["rvkProtocolNumber"])) echo "value='" . $currentConscript["rvkProtocolNumber"] . "'" ?>>
            </div>
        </div>

        <div class="row">
            <?
            if($edit) {
            ?>
            <div class="col-auto">
                <button type="button" onclick="openAreYouSureModal('Вы уверены, что хотите удалить призывника и все связанные с ним документы?', deleteConscript, <?echo $currentConscript['id']?>);" class="btn btn-outline-danger">Удалить призывника</button>
            </div>
            <?
            }
            ?>
            <div class="col"></div>
            <div class="col-auto">
                <button name="submit" id="editorConscriptButton" type="submit" class="btn btn-outline-success"><? echo $edit ? "Сохранить изменения" : "Зарегистрировать призывника"; ?></button>
            </div>
        </div>
    </form>
</div>

<? 
if($edit) { 
?>
<script>
<?if (isset($currentConscript["healthCategory"])) echo "document.getElementById('healthCategory').value='" . $currentConscript["healthCategory"] . "';"?>
<?if (isset($currentConscript["postPeriod"])) echo "document.getElementById('postPeriodSelect').value='" . $currentConscript["postPeriod"] . "';"?>
<?if (isset($currentConscript["vk"])) echo "document.getElementById('vk').value='" . $currentConscript["vk"] . "';"?>
<?if (isset($currentConscript["adventPeriod"])) echo "document.getElementById('adventTime').value='" . $currentConscript["adventPeriod"] . "';"?>

document.addEventListener('DOMContentLoaded', () => {
    $('#healthCategory').trigger('change');
});
</script>
<? 
} 
?>