<?
/*
Файл, подключаемый controller_conscription.php.
> содержит разметку страницы /conscription/editor.
*/
$back = isset($_GET['back']) ? '/' . $_GET['back'] : '/';
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-9 col-lg-10">Регистрация призывника</h3>
        <a onclick="location.href='<? echo $back ?>'"
            class="btn btn-outline-secondary mb-3 float-end col-3 col-lg-2">Назад</a>
    </div>

    <form method="POST" class="mt-3 mb-0">

        <div class="mb-3 d-flex">
            <div class="me-3 w-25">
                <label for="docNumber" class="form-label">Номер документа</label>
                <input type="number" class="form-control" id="docNumber" maxlength="10" required>
            </div>

            <div class="me-3 w-25">
                <label for="creationDate" class="form-label">Дата создания</label>
                <input type="date" class="form-control" value="<? echo date("Y-m-d") ?>" id="creationDate"
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
                        echo "<option value=" . $key . ">" . $value["name"] . "</option>";
                    ?>
                </select>
            </div>
        </div>


        <div class="mb-3">
            <label for="fullName" class="form-label">ФИО призывника</label>
            <input type="text" class="form-control" id="fullName" placeholder="Пример: Иванов Иван Иванович"
                maxlength="10" required>
        </div>

        <div class="mb-3 d-flex">
            <div class="me-3 w-50">
                <label for="rvkArticle" class="form-label">Статья РВК</label>
                <input type="text" class="form-control" placeholder="Пример: 23в" id="rvkArticle" maxlength="10"
                    required>
            </div>

            <div class="w-50">
                <label for="birthDate" class="form-label">Дата рождения</label>
                <input type="date" class="form-control" id="birthDate" required>
            </div>

        </div>


        <div class="mb-3">
            <label for="diagnosisTextarea" class="form-label">Диагноз</label>
            <textarea class="form-control" id="diagnosisTextarea" maxlength="1500" rows="5"
                placeholder="Пример: Отдалённые последствия черепно-мозговых травм"></textarea>
        </div>



        <!--
        <div class="mb-3 d-flex">

            <div class="me-3 w-75">
                <label for="diagnosisTextarea" class="form-label">Диагноз</label>
                <textarea class="form-control" id="diagnosisTextarea" maxlength="1500" rows="5"
                    placeholder="Пример: Отдалённые последствия черепно-мозговых травм"></textarea>
            </div>

            <div class="w-25">
                <div class="mb-4 w-100">
                    <label for="rvkArticle" class="form-label">Статья РВК</label>
                    <input type="text" class="form-control" placeholder="Пример: 23в" id="rvkArticle" maxlength="10"
                        required>
                </div>

                <div class="w-100">
                    <label for="birthDate" class="form-label">Дата рождения</label>
                    <input type="date" class="form-control" id="birthDate" required>
                </div>
            </div>


        </div>
-->

        <div class="mb-3 d-flex">
            <div class="me-3 w-25">
                <label for="article" class="form-label">Статья</label>
                <input id="article" type="text" class="form-control" placeholder="Пример: 23в" maxlength="10">
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
                <label for="adventTime" class="form-label">Время призыва</label>
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

        <button name="submit" type="submit" class="btn btn-outline-success w-25">Зарегистрировать</button>
    </form>
</div>