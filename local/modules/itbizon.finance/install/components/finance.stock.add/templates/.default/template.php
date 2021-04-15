<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Model\StockTable;

Loc::loadMessages(__FILE__);
Extension::load(['itbizon.bootstrap4', 'ui.forms', 'ui.alerts']);

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceStockAdd $component * */
global $APPLICATION;
$component = $this->getComponent();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.STOCK.ADD.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<form method="post">
    <label for="field-name"><?= Loc::getMessage('ITB_FIN.STOCK.ADD.TEMPLATE.FIELD.NAME') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-name" class="ui-ctl-element" type="text" name="DATA[NAME]" value="<?= $_POST['DATA']['NAME'] ?>" required>
        </div>
        <br>
    </label>
    <label for="field-stock-group-id"><?= Loc::getMessage('ITB_FIN.STOCK.ADD.TEMPLATE.FIELD.STOCK_GROUP_ID') ?>
        <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
            <div class="ui-ctl-after ui-ctl-icon-angle"></div>
            <select id="field-stock-group-id" class="ui-ctl-element" name="DATA[STOCK_GROUP_ID]" required>
                <option value=""></option>
                <? foreach(StockTable::getGroups() as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($_POST['DATA']['STOCK_GROUP_ID'] == $id) ? 'selected' : '' ?> ><?= $name ?></option>
                <? endforeach; ?>
            </select>
        </div>
        <br>
    </label>
    <?php
    $APPLICATION->IncludeComponent(
        'itbizon:field.userselect',
        '',
        [
            'CURRENT_USER' => CurrentUser::get()->getId(),
            'FIELD_ID' => 'RESPONSIBLE_ID',
            'FIELD_NAME' => 'DATA[RESPONSIBLE_ID]',
            'TITLE' => Loc::getMessage('ITB_FIN.STOCK.ADD.TEMPLATE.FIELD.RESPONSIBLE'),
            'CHANGE_ACTIVE' => true
        ]
    ); ?>
    <label for="field-percent"><?= Loc::getMessage('ITB_FIN.STOCK.ADD.TEMPLATE.FIELD.PERCENT') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-percent" class="ui-ctl-element" type="number" min="0" max="100" step="0.01" name="DATA[PERCENT]" value="<?= $_POST['DATA']['PERCENT'] ?>" required>
        </div>
        <br>
    </label>
    <label for="field-balance"><?= Loc::getMessage('ITB_FIN.STOCK.ADD.TEMPLATE.FIELD.BALANCE') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-balance" class="ui-ctl-element" type="number" min="0" step="0.01" name="DATA[BALANCE]" value="<?= $_POST['DATA']['BALANCE'] ?>" required>
        </div>
        <br>
    </label>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('list')
        ]
    ]); ?>
</form>
