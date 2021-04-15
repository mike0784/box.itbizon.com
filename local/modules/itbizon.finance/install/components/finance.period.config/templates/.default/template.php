<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Model\StockTable;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load(['itbizon.finance.bootstrap4', 'ui.alerts']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBFinancePeriodConfig $component */
$component = $this->getComponent();
$options = $component->getOptions();
$stocks = [];
$result = StockTable::getList([
    'select' => ['ID', 'NAME'],
    'order' => ['NAME' => 'ASC']
]);
while($row = $result->fetchObject()) {
    $stocks[$row->getId()] = $row->getName();
}
$incomeCategories = [];
$result = OperationCategoryTable::getList([
    'order' => ['NAME' => 'ASC']
]);
while($row = $result->fetchObject()) {
    if($row->getAllowIncome()) {
        $incomeCategories[$row->getId()] = $row->getName();
    }
}
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<form method="post">
    <div class="form-group">
        <label for="field-start-week"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.TEMPLATE.FIELD.START_WEEK') ?></label>
        <select id="field-start-week" class="form-control" name="DATA['startWeek']" required>
            <? foreach(PeriodTable::getWeekDayNamesRu() as $index => $value): ?>
                <option value="<?= $index ?>" <?= ($index == $options['startWeek']) ? 'selected' : '' ?> ><?= $value ?></option>
            <? endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="field-start-time"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.TEMPLATE.FIELD.START_TIME') ?></label>
        <input id="field-start-time" class="form-control" type="time" name="DATA[startTime]"
               value="<?= $options['startTime'] ?>" required>
    </div>
    <div class="form-group">
        <label for="field-reserve-stock-id"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.TEMPLATE.FIELD.RESERVE_STOCK_ID') ?></label>
        <select id="field-reserve-stock-id" class="form-control" name="DATA[reserveStockId]" required>
            <option value=""></option>
            <? foreach($stocks as $id => $name): ?>
                <option value="<?= $id ?>" <?= ($id == $options['reserveStockId']) ? 'selected' : '' ?> ><?= $name ?></option>
            <? endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="field-income-category-id"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.TEMPLATE.FIELD.INCOME_CATEGORY_ID') ?></label>
        <select id="field-income-category-id" class="form-control" name="DATA[incomeCategoryId]" required>
            <option value=""></option>
            <? foreach($incomeCategories as $id => $name): ?>
                <option value="<?= $id ?>" <?= ($id == $options['incomeCategoryId']) ? 'selected' : '' ?> ><?= $name ?></option>
            <? endforeach; ?>
        </select>
    </div>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('list')
        ]
    ]); ?>
</form>
