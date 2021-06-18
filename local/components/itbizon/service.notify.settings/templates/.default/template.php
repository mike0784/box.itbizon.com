<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');
Extension::load(['jquery', 'ui.alerts']);

//$APPLICATION->SetAdditionalCSS("/bitrix/components/main.ui.filter/templates/style.css");
//$APPLICATION->SetAdditionalCSS("/bitrix/components/bitrix/main.ui.filter/templates/.default/style.css");
//$APPLICATION->ShowCSS();


/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceNotifySettings $component */
$component = $this->getComponent();
$res = $component->getTable();
$users = $component->users;
$fromUser = $component->fromUser;
$toUsers = $component->toUsers;
?>
<?php if ($component->getHelper()->getAction() == 'list') : ?>
    <?php $APPLICATION->SetTitle(Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.PAGE_TITLE')); ?>
    <?php if ($component->getError()): ?>
        <div class="ui-alert ui-alert-danger">
            <span class="ui-alert-message"><?= $component->getError() ?></span>
        </div>
    <?php endif; ?>

    <form method='POST'>
        <input type='hidden' name='select_from_user' value='true'>
        <div class="card">
        <div class="card-body">
        <div class="form-group">
            <label for="FROM_USER_ID" class="">Выбрать исходного пользователя</label>
            <select name="FROM_USER_ID" id="FROM_USER_ID" class="form-control is-valid ">
            <? foreach ($users as $key => $user): ?>
                <? if ($user['ID'] == $fromUser): ?>
                    <option selected value="<?= $user['ID'] ?>"><?= $user['NAME']." ". $user['SECOND_NAME']." ".$user['LAST_NAME'] ?></option>
                <? else: ?>
                    <option value="<?= $user['ID'] ?>"><?= $user['NAME']." ". $user['SECOND_NAME']." ". $user['LAST_NAME'] ?></option>
                <? endif; ?>
            <? endforeach; ?>
            </select>
        </div>
        <button class='btn ui-btn ui-btn-success' type='submit'>Прочитать настройки</button>
        </div>
        </div>
        
    </form>

    <form method='POST'>
        <input type='hidden' name='select_to_user' value='true'>
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="TO_USER_ID" class="">Выбрать пользователя для записи настроек</label>
                    <select name="TO_USER_ID" id="TO_USER_ID" class="form-control is-valid ">
                        <? foreach ($users as $key => $user): ?>
                            <? if (in_array($user['ID'], $toUsers)): ?>
                                <option selected value="<?= $user['ID'] ?>"><?= $user['NAME']." ". $user['SECOND_NAME']." ".$user['LAST_NAME'] ?></option>
                            <? else: ?>
                                <option value="<?= $user['ID'] ?>"><?= $user['NAME']." ". $user['SECOND_NAME']." ". $user['LAST_NAME']." (".$user['ID'].")" ?></option>
                            <? endif; ?>
                        <? endforeach; ?>
                    </select>
                </div>
                <button class='btn ui-btn ui-btn-success' type='submit'>Записать настройки</button>
            </div>
        </div>

<!--
    </form>
-->

    <div class='bx-messenger-settings-contents' style='width:800px;'>
<!--
    <form method='POST'>
-->
    <input type='hidden' name='form_notify_table' value='true'>
    <div style='display: block;' id='bx-messenger-settings-content-notify' class='bx-messenger-settings-content' >
<!--
    <div class='form-group'>
        <button class='btn btn-primary ui-btn ui-btn-lg ui-btn-primary' type='submit'>Сохранить</button>
    </div>
-->
    
    <table  class='bx-messenger-settings-table bx-messenger-settings-table-style-notify bx-messenger-settings-table-extra' >
    <tr><th></th><th>Сайт и<br>приложения</th><th>Электронная почта</th><th>Push уведомления</th></tr>

    <?
    foreach($res as $row_num => $row){
        if ($row['type'] == 'header'){
            echo "<tr><td class='bx-messenger-settings-table-sep bx-messenger-settings-table-extra' colspan='4'>".$row['title']."</td></tr>";
        } elseif  ($row['type'] == 'data') {
            echo "<tr><td>".$row['title']."</td> <td>".$row['site']."</td> <td>".$row['email']."</td> <td>".$row['push']."</td> </tr>";
        } else
            echo "<tr><td> DEBUG".print_r($row, True)."</td></tr>";
    }
    ?>
    </table>
    </div>
    </form>
    </div>

<?php else: ?>

<?php endif; ?>