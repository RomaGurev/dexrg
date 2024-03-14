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

        <form id="authForm" method="post" class="mb-0">
            <div class="mb-3">
                <label for="authPosition" class="form-label">Специальность</label>
                <select id="authPosition" class="form-control form-select" style="cursor:pointer;">
                    <option value="">Не выбрано</option>
                    <?
                    for ($i = 0; $i < count(Config::getValue("userType")); $i++) {
                        echo "<option value='$i'" . ($i == 0 ? "class='d-none'" : '') . "> [$i] " . Config::getValue("userType")[$i][0] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button name="submit" type="submit" class="btn btn-outline-dark w-100">Авторизация</button>
        </form>

    </div>
</div>


<button onclick="loginAdmin();" class="btn text-muted mx-auto d-block mt-3">Войти под учетной записью администратора</button>