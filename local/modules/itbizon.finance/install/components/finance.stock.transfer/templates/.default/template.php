<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
\CJSCore::init('sidepanel');
Extension::load(['itbizon.bootstrap4', 'ui.alerts', 'ui.forms']);

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceStockTransfer $component * */
global $APPLICATION;
$component = $this->getComponent();
$stockList = $component->getStockList();
$categoryList = $component->getCategories();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.STOCK.TRANSFER.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<form method="post">
    <label for="field-stock-src"><?= Loc::getMessage('ITB_FIN.STOCK.TRANSFER.TEMPLATE.SRC_STOCK') ?>
        <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
            <div class="ui-ctl-after ui-ctl-icon-angle"></div>
            <select id="field-stock-src" class="ui-ctl-element" name="DATA[SRC_STOCK]" required>
                <option value=""></option>
                <? foreach($stockList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($_POST['DATA']['SRC_STOCK'] == $id) ? 'selected' : '' ?> ><?= $name ?></option>
                <? endforeach; ?>
            </select>
        </div>
        <br>
    </label>
    <label for="field-stock-dst"><?= Loc::getMessage('ITB_FIN.STOCK.TRANSFER.TEMPLATE.DST_STOCK') ?>
        <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
            <div class="ui-ctl-after ui-ctl-icon-angle"></div>
            <select id="field-stock-dst" class="ui-ctl-element" name="DATA[DST_STOCK]" required>
                <option value=""></option>
                <? foreach($stockList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($_POST['DATA']['DST_STOCK'] == $id) ? 'selected' : '' ?> ><?= $name ?></option>
                <? endforeach; ?>
            </select>
        </div>
        <br>
    </label>
    <label for="field-category"><?= Loc::getMessage('ITB_FIN.STOCK.TRANSFER.TEMPLATE.CATEGORY') ?>
        <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
            <div class="ui-ctl-after ui-ctl-icon-angle"></div>
            <select id="field-stock-dst" class="ui-ctl-element" name="DATA[CATEGORY]" required>
                <option value=""></option>
                <? foreach($categoryList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($_POST['DATA']['CATEGORY'] == $id) ? 'selected' : '' ?> ><?= $name ?></option>
                <? endforeach; ?>
            </select>
        </div>
        <br>
    </label>
    <label for="field-amount"><?= Loc::getMessage('ITB_FIN.STOCK.TRANSFER.TEMPLATE.FIELD.AMOUNT') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-amount" class="ui-ctl-element" type="number" min="0" step="0.01" name="DATA[AMOUNT]" value="<?= $_POST['DATA']['AMOUNT'] ?>" required>
        </div>
        <br>
    </label>
    <label for="field-comment"><?= Loc::getMessage('ITB_FIN.STOCK.TRANSFER.TEMPLATE.COMMENT') ?>
        <div class="ui-ctl ui-ctl-textarea">
            <textarea name="DATA[COMMENT]" style="width: 320px;" class="ui-ctl-element"><?= $_POST['DATA']['COMMENT'] ?></textarea>
        </div>
    </label>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('list')
        ]
    ]); ?>
</form>
