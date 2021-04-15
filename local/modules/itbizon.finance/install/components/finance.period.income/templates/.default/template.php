<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Utils\Money;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load(['itbizon.finance.bootstrap4', 'ui.alerts', 'ui.forms']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBFinancePeriodIncome $component */
$component = $this->getComponent();
$operations = $component->getOperations();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php if($operations): ?>
    <form method="post">
        <label class="ui-ctl ui-ctl-radio">
            <input id="itb-type-auto" name="DATA[TYPE]" type="radio" class="ui-ctl-element itb-type-selector" value="AUTO" checked>
            <div class="ui-ctl-label-text"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.TEMPLATE.AUTO') ?></div>
        </label>
        <label class="ui-ctl ui-ctl-radio">
            <input id="itb-type-manual" name="DATA[TYPE]" type="radio" class="ui-ctl-element itb-type-selector" value="MANUAL">
            <div class="ui-ctl-label-text"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.TEMPLATE.MANUAL') ?></div>
        </label>
        <label for="field-value"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.TEMPLATE.VALUE') ?>
            <div class="ui-ctl ui-ctl-textbox">
                <input id="field-value" class="ui-ctl-element" type="number" name="DATA[VALUE]" value="0.00" required readonly>
            </div>
            <br>
        </label>
        <table class="table table-sm table-striped table-hover">
            <thead class="thead-dark">
            <tr>
                <th></th>
                <th>Название</th>
                <th>Дата проведения</th>
                <th>Сумма</th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($operations as $operation): ?>
                <tr>
                    <td><input class="itb-operation-selector" type="checkbox" data-value="<?= $operation->getAmount() ?>" checked></td>
                    <td><?= $operation->getName() ?></td>
                    <td><?= $operation->getDateCommit() ?></td>
                    <td><?= Money::formatFromBase($operation->getAmount()) ?></td>
                </tr>
            <? endforeach; ?>
            </tbody>
        </table>
        <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
            'BUTTONS' => [
                'save',
                'cancel' => $component->getRoute()->getUrl('list')
            ]
        ]); ?>
    </form>
<?php endif; ?>