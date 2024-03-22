<?
/*
Файл, подключаемый controller_pattern.php.
> содержит разметку страницы /pattern/editor (страница добавления и редактирования шаблона).
*/

if ($data["currentPattern"] != null) {
    $edit = true;
    $currentPattern = $data["currentPattern"][0];
}
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col">
            <? echo $edit ? "Редактирование шаблона" : "Добавление шаблона"; ?>
        </h3>
        <a href="/pattern" class="btn btn-outline-secondary mb-3 col-auto" style="min-width: 200px">Назад</a>
    </div>

    <form <? echo $edit ? "id='editPatternForm'" : "id='addPatternForm'" ?> method="POST" class="mt-3 mb-0">
        <input type="text" style="display: none;" <? if (isset($currentPattern["id"]))
            echo "value='" . $currentPattern["id"] . "'" ?> id="patternID" maxlength="100">
            <div class="mb-3">
                <label for="patternName" class="form-label">Название шаблона</label>
                <input type="text" class="form-control" id="patternName" maxlength="100" <? if (isset($currentPattern["name"]))
            echo "value='" . $currentPattern["name"] . "'" ?>
                    placeholder="Пример: пониженное питание">
                <div class="invalid-feedback mt-1">
                    Неверное название шаблона
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
                <textarea class="form-control" id="anamnezTextarea" maxlength="1000"
                    rows="4"><? if (isset($currentPattern["anamnez"]))
                        echo $currentPattern["anamnez"]; ?></textarea>
            </div>
        </div>

        <div class="mb-3">
            <label for="objectDataTextarea" class="form-label">Данные объективного исследования</label>
            <textarea class="form-control" id="objectDataTextarea" maxlength="1500"
                rows="6"><? if (isset($currentPattern["objectData"]))
                    echo $currentPattern["objectData"]; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="specialResultTextarea" class="form-label">Результаты специальных исследований</label>
            <textarea class="form-control" id="specialResultTextarea" maxlength="1500"
                rows="6"><? if (isset($currentPattern["specialResult"]))
                    echo $currentPattern["specialResult"]; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="diagnosisTextarea" class="form-label">Диагноз</label>
            <textarea class="form-control" id="diagnosisTextarea" maxlength="1500" rows="5"
                placeholder="Пример: Отдалённые последствия черепно-мозговых травм"><? if (isset($currentPattern["diagnosis"]))
                    echo $currentPattern["diagnosis"]; ?></textarea>
        </div>

        <button id="editorPatternButton" name="submit" type="submit" class="btn btn-outline-success w-25">
            <? echo $edit ? "Сохранить изменения" : "Добавить шаблон"; ?>
        </button>
    </form>
</div>