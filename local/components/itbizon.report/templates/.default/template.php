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
            <tr>
                <td><?= $user["FULLNAME"] ?></td>
                <td><a href="#">2</a></td>
                <td><a href="#">11</a></td>
                <td><a href="#">15</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>