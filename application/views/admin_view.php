<?
/*
Файл, подключаемый controller_admin.php.
> содержит разметку страницы /admin.
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

<div class="row row-cols-2">
    <div class="col-3">

        <div class="p-4 mb-3 rounded-3 border shadow">

            <h3 class="display-6 lh-1 fs-2">Добавить аккаунт</h3>
            <div class="pt-lg-3">


                <form id="addUserForm" method="POST" class="mb-0">

                    <div class="mb-3">
                        <label for="addAccount[name]" class="form-label">ФИО</label>
                        <input type="text" class="form-control" name="addAccount[name]" id="userAccountName" required>
                        <div class="invalid-feedback">
                            Введите ФИО.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label data-bs-toggle="dropdown" class="form-label">Должность</label>
                        <div class="d-flex">
                            <input type="text" id="positionText" class="form-control" placeholder="Не выбрано"
                                style="border-radius: var(--bs-border-radius) 0px 0px var(--bs-border-radius); border-right: 0;"
                                readonly required>

                            <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                style="border: var(--bs-border-width) solid var(--bs-border-color); border-radius: 0px var(--bs-border-radius) var(--bs-border-radius) 0px;">
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text text-muted">Выберите должность</span></li>
                                <?
                                for ($i = 1; $i < count(Config::getValue("userType")); $i++) {
                                    echo "<li><a class='dropdown-item' style='cursor: pointer;' onclick='selectPosition($i);'>" . Config::getValue('userType')[$i][0] . "</a></li>";
                                }
                                ?>
                            </ul>

                            <input type="text" name="addAccount[position]" id="userAccountPosition" value=""
                                style="display: none;" />
                        </div>
                    </div>
                    <button name="submit" type="submit" class="btn btn-outline-secondary w-100">Создать учетную
                        запись</button>
                </form>



            </div>

        </div>

        <div class="p-4 mb-3 rounded-3 border shadow">
            <h3 class="display-6 lh-1 fs-2">Выбор базы</h3>
            <div class="pt-lg-3">
                <form id="selectBaseForm" class="mb-0">
                    <div class="mb-3">
                        <label class="form-label">Призыв</label>
                        <div class="d-flex">
                            <input id="selectedBase" type="text" class="form-control" placeholder="<? echo Database::getCurrentBase() ?>"
                                style="border-radius: var(--bs-border-radius) 0px 0px var(--bs-border-radius); border-right: 0;"
                                readonly>
                            <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                style="border: var(--bs-border-width) solid var(--bs-border-color); border-radius: 0px var(--bs-border-radius) var(--bs-border-radius) 0px;">
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text text-muted">Выберите базу</span></li>
                                <?
                                $databases = Database::getDatabasesList();

                                for ($i = 0; $i < count($databases); $i++) {
                                    echo '<li><a class="dropdown-item" style="cursor: pointer;" onclick="selectBase(\'' . $databases[$i] . '\');">' . $databases[$i] . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>

                        <div id="selectBaseValidMessage" class="valid-feedback mt-1 order-1">
                            
                        </div>
                    </div>
                    <button type="submit" id="selectBaseButton" class="btn btn-outline-secondary w-100" disabled>Выбрать базу</button>
                </form>
            </div>
        </div>

        <div class="p-4 mb-0 rounded-3 border shadow">
            <h3 class="display-6 lh-1 fs-2">Создание базы</h3>
            <div class="pt-lg-3">
                <form id="createBaseForm" class="mb-0">
                    <div class="mb-3">
                        <label for="createBaseInput" class="form-label">Название базы</label>
                        <input type="text" id="createBaseInput" class="form-control" maxlength="6" placeholder="Пример: 2023-2">
                        <div class="invalid-feedback mt-1">
                            Пожалуйста, проверьте формат (Пример: 2023-2).
                        </div>
                        <div id="createBaseValidMessage" class="valid-feedback mt-1">
                        </div>
                    </div>
                    <button type="submit" id="createBaseButton" class="btn btn-outline-success w-100" disabled>Создать базу</button>
                </form>
            </div>
        </div>

    </div>


    <div class="col-9">

        <div class="p-4 mb-3 rounded-3 border shadow">
            <h3 class="display-6 lh-1 fs-2">Управление аккаунтами</h3>
            <div class="pt-lg-3">
                <p class="lead mb-3">
                    В разделе отображаются аккаунты сотрудников ВВК c возможностью редактирования должности и
                    деактивации аккаунта.
                </p>

                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="lead text-center">ID</th>
                            <th scope="col" class="lead">ФИО</th>
                            <th scope="col" class="lead">Специальность</th>
                            <th scope="col" class="lead col-1 text-center">Активна</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $accounts = $data['userAccounts'];

                        for ($i = 0; $i < count($accounts); $i++) {
                            $id = $accounts[$i]["id"];
                            $name = $accounts[$i]["name"];
                            $position = $accounts[$i]["position"] . " - " . Config::getValue('userType')[$accounts[$i]["position"]][0];
                            $isEmployed = ($accounts[$i]["isEmployed"] == 1) ? "checked" : "";

                            $output = "<tr>
                                   <th scope='row' class='text-center lead fs-6'>$id</td>
                                   <td class='lead fs-6'>$name</td>
                                   <td class='lead fs-6'>";
                            if ($accounts[$i]["position"] != 0) {
                                $output .= "<a class='nav-link dropdown-toggle' id='positionDropdown$id' role='button' data-bs-toggle='dropdown' aria-expanded='false'>$position</a>
                                        <ul class='dropdown-menu dropdown-menu-start'>
                                        <li><span class='dropdown-item-text text-muted'>Выберите должность</span></li>";
                                for ($k = 1; $k < count(Config::getValue("userType")); $k++) {
                                    if ($accounts[$i]["position"] == $k)
                                        $active = "active";
                                    else
                                        $active = "";

                                    $output .= "<li><a style='cursor:pointer' onclick='changePosition($id, $k)' class='lead fs-6 dropdown-item " . $active . "'>" . Config::getValue('userType')[$k][0] . "</a></li>";
                                }
                                $output .= "</ul>";
                            } else {
                                $output .= "<b class='lead fs-6'>Администратор</b>";
                            }

                            if ($i == 0)
                                $adminDisabled = "disabled";
                            else
                                $adminDisabled = "";

                            $output .= "</td>
                                    <th scope='row' class='text-center lead fs-6'><input class='form-check-input' type='checkbox' onchange='changeIsEmployed($id)' $isEmployed " . $adminDisabled . "></th>
                                    </tr>";
                            echo $output;
                        }
                        ?>
                    </tbody>
                </table>

                <div id="adminTableMessage"> </div>
            </div>
        </div>

        <div class="p-4 rounded-3 border shadow">
            <h3 class="display-6 lh-1 fs-2">Информация о базе данных</h3>
            <div class="pt-lg-3">
                <p class="lead mb-3">
                    В данном разделе отображается информация о выбранной базе данных.
                </p>
            </div>
        </div>

        <div class="p-4 rounded-3 border shadow mt-3">
            <h3 class="display-6 lh-1">Тестирование</h3>
            <div class="pt-lg-3">
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-lg-3">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#RGModal"
                        class="btn btn-primary px-4">Модалка Тест</button>
                    <button type="button" class="btn btn-outline-secondary px-4" id="liveToastBtn">Сообщение
                        тест</button>
                </div>
            </div>
        </div>

    </div>

</div>