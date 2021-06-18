<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.select2');
Extension::load(['jquery', 'ui.alerts', 'itbizon.select2', 'itbizon.bootstrap4']);

//$APPLICATION->SetAdditionalCSS("/bitrix/components/main.ui.filter/templates/style.css");
//$APPLICATION->SetAdditionalCSS("/bitrix/components/bitrix/main.ui.filter/templates/.default/style.css");
//$APPLICATION->ShowCSS();


/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceNotifySettings $component */
$component = $this->getComponent();
$res = $component->getTable();
$usersList = $component->usersList;
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

    <div class="card" style='width:900px; '>
    <form method='POST'>
        <input type='hidden' name='select_from_user' value='true'>
        <div class="card-body">
        <div class="form-group">
            <h5 class="card-title"><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.LOAD_TITLE')?></h5>
            <select style="option:first {color: #999;}" name="FROM_USER_ID" id="FROM_USER_ID" class="form-control">
                <option style="color: grey" value="" disabled selected><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.SELECT_USER')?></option>
            <? foreach ($usersList as $key => $user): ?>
                <option value="<?= $user['ID'] ?>" <?= ($user['ID'] == $fromUser)?'selected':''  ?> ><?= $user['LAST_NAME']." ". $user['NAME']." ". $user['SECOND_NAME'] ?></option>
            <? endforeach; ?>
            </select>
        </div>
        <button class='btn btn-primary' type='submit'><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.LOAD_BUTTON')?></button>
        </div>
        
    </form>
    </div>

    <form method='POST'>
        <input type='hidden' name='select_to_user' value='true'>
        <div class="card" style='width:900px;'>
            <div class="card-body">
                <div class="form-group">
                    <h5 class="card-title"><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.SAVE_TITLE')?></h5>
                    <select name="TO_USER_ID[]" id="TO_USER_ID" class="form-control" multiple>
                        <? foreach ($usersList as $key => $user): ?>
                            <option value="<?= $user['ID'] ?>" <?= (in_array($user['ID'], $toUsers))?'selected':''  ?> ><?= $user['LAST_NAME']." ". $user['NAME']." ".$user['SECOND_NAME'] ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <button class='btn btn-primary' type='submit'><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.SAVE_BUTTON')?></button>
            </div>
        </div>


    <div class='bx-messenger-settings-contents' style='width:900px;'>
    <input type='hidden' name='form_notify_table' value='true'>
    <div style='display: block;' id='bx-messenger-settings-content-notify' class='bx-messenger-settings-content' >
    <table  class='bx-messenger-settings-table bx-messenger-settings-table-style-notify bx-messenger-settings-table-extra' >
    <tr>
        <th></th>
        <th><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.TABLE.SITE')?></th>
        <th><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.TABLE.EMAIL')?></th>
        <th><?= Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.TABLE.PUSH')?></th>
    </tr>

    <?
    foreach($res as $row_num => $row){
        if ($row['type'] == 'header'){
            echo "<tr><td class='bx-messenger-settings-table-sep bx-messenger-settings-table-extra' colspan='4'>".$row['title']."</td></tr>";
        } elseif  ($row['type'] == 'data') {
            echo "<tr><td>".$row['title']."</td> <td>".$row['site']."</td> <td>".$row['email']."</td> <td>".$row['push']."</td> </tr>";
        } else
            echo "<tr><td> DEBUG".print_r($row, True)."</td></tr>"; // fixme
    }
    ?>
    </table>
    </div>
    </form>
    </div>

<?php else: ?>

<?php endif; ?>