<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Utils\Money;

Loc::loadMessages(__FILE__);
Extension::load(['itbizon.bootstrap4', 'ui.forms', 'ui.alerts']);

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceStockEdit $component * */
global $APPLICATION;
$component = $this->getComponent();
$stock = $component->getStock();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.STOCK.EDIT.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php if($stock): ?>
    <form method="post">
        <label for="field-name"><?= Loc::getMessage('ITB_FIN.STOCK.EDIT.TEMPLATE.FIELD.NAME') ?>
            <div class="ui-ctl ui-ctl-textbox">
                <input id="field-name" class="ui-ctl-element" type="text" name="DATA[NAME]" value="<?= $stock->getName() ?>" required>
            </div>
            <br>
        </label>
        <label for="field-stock-group-id"><?= Loc::getMessage('ITB_FIN.STOCK.EDIT.TEMPLATE.FIELD.STOCK_GROUP_ID') ?>
            <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
                <div class="ui-ctl-after ui-ctl-icon-angle"></div>
                <select id="field-stock-group-id" class="ui-ctl-element" name="DATA[STOCK_GROUP_ID]" required>
                    <option value=""></option>
                    <? foreach(StockTable::getGroups() as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($stock->getStockGroupId() == $id) ? 'selected' : '' ?> ><?= $name ?></option>
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
                'CURRENT_USER' => $stock->getResponsibleId(),
                'FIELD_ID' => 'RESPONSIBLE_ID',
                'FIELD_NAME' => 'DATA[RESPONSIBLE_ID]',
                'TITLE' => Loc::getMessage('ITB_FIN.STOCK.EDIT.TEMPLATE.FIELD.RESPONSIBLE'),
                'CHANGE_ACTIVE' => true
            ]
        ); ?>
        <label for="field-percent"><?= Loc::getMessage('ITB_FIN.STOCK.EDIT.TEMPLATE.FIELD.PERCENT') ?>
            <div class="ui-ctl ui-ctl-textbox">
                <input id="field-percent" class="ui-ctl-element" type="number" min="0" max="100" step="0.01" name="DATA[PERCENT]" value="<?= Money::fromBase($stock->getPercent()) ?>" required>
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
<?php endif; ?>
