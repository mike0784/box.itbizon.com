<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Model\StockTable;

Loc::loadMessages(__FILE__);
Extension::load(['itbizon.bootstrap4', 'ui.forms', 'ui.alerts']);

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceStockCategory $component * */
global $APPLICATION;
$component = $this->getComponent();
$data = $component->getData();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.STOCK.CATEGORY.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php if($component->getStocks() && $component->getCategories()): ?>
<form method="post">
    <? foreach($component->getCategories() as $category): ?>
        <label for="field-category-<?= $category->getId() ?>-stock-id">
            <?= $category->getName() ?>
            <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
                <div class="ui-ctl-after ui-ctl-icon-angle"></div>
                <select id="field-category-<?= $category->getId() ?>-stock-id" class="ui-ctl-element" name="DATA[<?= $category->getId() ?>]" required>
                    <option value="0"> - </option>
                    <? foreach($component->getStocks() as $stock): ?>
                        <option value="<?= $stock->getId() ?>" <?= ($stock->getId() == $data[$category->getId()]) ? 'selected' : '' ?> ><?= $stock->getName() ?></option>
                    <? endforeach; ?>
                </select>
            </div>
            <br>
        </label>
    <? endforeach; ?>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('list')
        ]
    ]); ?>
</form>
<?php endif; ?>