<?php
use Bitrix\Main\UI\Extension;
Extension::load('ui.bootstrap4');
?>

<div class="container">
    <div class="time-interval mb-2">
        <span>Дата с</span>
        <span><input id="from" class="date" type="date" value="<?= $arResult['INTERVAL']['FROM'] ?>"></span>
        <span>по</span>
        <span><input id="to" class="date" type="date"  value="<?= $arResult['INTERVAL']['TO'] ?>"></span>
    </div>
    <div class="departament mb-2">
        <span>Отдел</span>
        <span>
            <select id="group" class="p-1">
                <?php foreach ($arResult['DEP_LIST'] as $group) : ?>
                    <option value="<?= $group['ID']; ?>" <?= ($arResult['DEP_ID'] == $group['ID']) ? "selected" : "" ?>><?= $group['NAME']; ?></option>
                <?php endforeach; ?>
            </select>
        </span>
    </div>
    <div class="row mb-1">
        <a id="prev" href="#" class="change-date col">< Предыдущая</a>
        <a id="next" href="#" class="change-date col text-right">Следующая ></a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>ФИО сотрудника</th>
                <th>Звонки</th>
                <th>Выполненные задачи</th>
                <th>Планируемые задачи</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($arResult['USERS'] as $user) : ?>
            <tr id="<?= $user["ID"] ?>">
                <td><?= $user["FULLNAME"] ?></td>
                <td><a class="call-list" href="#"><?= $user["CALL_NUM"] ?></a></td>
                <td><a class="task-done-list" href="#"><?= $user["TASK_DONE_NUM"] ?></a></td>
                <td><a class="task-list" href="#"><?= $user["TASK_NUM"] ?></a></td>
            </tr>
<!--            <script>-->
<!--                const userTaskDoneList <?//= $user["ID"] ?> <?//= json_encode($user["TASKS"]) ?> ;-->
<!--//            </script>-->
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    var pathAjax = '<?= $arResult["AJAX_PATH"]?>';
</script>