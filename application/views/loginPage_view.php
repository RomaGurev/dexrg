<?
/*
Файл, подключаемый controller_main.php.
> содержит разметку страницы авторизации.
*/
?>

<script type="text/javascript">
    let positions = [
        <?
        for ($i = 0; $i < count(Config::getValue("userType")); $i++) {
            print '"' . Config::getValue("userType")[$i][0] . '",';
        }
        ?>
    ];
</script>

<div class="col-8 col-xl-4 p-4 align-items-center mx-auto rounded-3 border shadow">
    <h3 class="display-6 lh-1 fs-2">Авторизация</h3>
    <div class="pt-3">
        <!--
        <form id="authForm" method="post" class="mb-0">
            <input type="text" id="authPosition" value="" style="display: none;" />
            <div class="mb-3">
                <label data-bs-toggle="dropdown" class="form-label">Специальность</label>
                <div class="d-flex">
                    <input type="text" id="positionText" class="form-control" data-bs-toggle="dropdown"
                        aria-expanded="false" placeholder="Не выбрано"
                        style="border-radius: var(--bs-border-radius) 0px 0px var(--bs-border-radius); border-right: 0; cursor: pointer;"
                        readonly required>
                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="border: var(--bs-border-width) solid var(--bs-border-color); border-radius: 0px var(--bs-border-radius) var(--bs-border-radius) 0px;">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted">Выберите специальность</span></li>
                        <?
                        //for ($i = 0; $i < count(Config::getValue("userType")); $i++) {
                          //  echo "<li><a class='dropdown-item' style='cursor: pointer;' onclick='authPosition($i);'>" . Config::getValue("userType")[$i][0] . "</a></li>";
                        //}
                        ?>
                    </ul>
                </div>
            </div>
            <button name="submit" type="submit" class="btn btn-outline-dark w-100">Авторизация</button>
        </form>

        authPosition
        -->

        <form id="authForm" method="post" class="mb-0">
            <div class="mb-3">
                <label for="authPosition" class="form-label">Специальность</label>
                <select id="authPosition" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <?
                    for ($i = 0; $i < count(Config::getValue("userType")); $i++) {
                        echo "<option value='$i'> [$i] " . Config::getValue("userType")[$i][0] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button name="submit" type="submit" class="btn btn-outline-dark w-100">Авторизация</button>
        </form>


    </div>
</div>