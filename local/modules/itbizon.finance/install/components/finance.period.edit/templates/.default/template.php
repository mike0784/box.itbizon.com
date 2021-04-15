<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Stock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
CUtil::InitJSCore( ['ajax', 'popup', 'window']);
Extension::load(['itbizon.finance.bootstrap4', 'itbizon.select2', 'ui.notification']);

/**@var $APPLICATION CAllMain */
/**@var $arResult array */
/**@var $this CBitrixComponentTemplate */
/**@var $component CITBFinancePeriodEdit */
$component = $this->getComponent();
$period = $component->getPeriod();
?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php if ($period): ?>
    <?php $errors = Stock::check(); ?>
    <?php foreach($errors->getValues() as $error): ?>
        <div class="ui-alert ui-alert-warning">
            <span class="ui-alert-message"><?= $error->getMessage() ?></span>
        </div>
    <?php endforeach; ?>

    <? if($period->getStatus() === PeriodTable::STATUS_DISTRIBUTION_PROCEEDS): ?>
        <div class="alert alert-warning">
            <strong>Требуется распределение выручки</strong>
        </div>
    <? else: ?>
        <div class="alert alert-success">
            <strong>Выручка распределена</strong>
        </div>
    <? endif; ?>
    <?php
    Toolbar::addFilter([
        'GRID_ID' => $component->getGrid()->getGridId(),
        'FILTER_ID' => $component->getGrid()->getFilterId(),
        'FILTER' => $component->getGrid()->getFilter(),
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true,
        'RESET_TO_DEFAULT_MODE' => true,
    ]);
    if($period->getStatus() === PeriodTable::STATUS_DISTRIBUTION_PROCEEDS) {
        Toolbar::addButton(new Button([
            "color" => Color::PRIMARY,
            "click" => new JsCode(
                'BX.SidePanel.Instance.open("' . $component->getRoute()->getUrl('income', ['ID' => $period->getId()]) . '", {
                            cacheable: false,
                            width: 800
                        });'
            ),
            "text" => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.BUTTON.DISTRIBUTE'),
        ]));
    }
    if($period->getStatus() === PeriodTable::STATUS_ALLOCATION_COSTS) {
        Toolbar::addButton(new Button([
            "color" => Color::PRIMARY,
            "click" => new JsCode(
                'closePeriod("'.$component->getAjaxPath().'", "'.$component->getGrid()->getGridId().'", '.$period->getId().');'
            ),
            "text" => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.BUTTON.CLOSE'),
        ]));
    }
    ?>
    <?
    $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $component->getGrid()->getGridId(),
        'COLUMNS' => $component->getGrid()->getColumns(),
        'ROWS' => $component->getGrid()->getRows(),
        'SHOW_ROW_CHECKBOXES' => false,
        'NAV_OBJECT' => $component->getGrid()->getNavigation(),
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => [
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'AJAX_OPTION_JUMP' => 'N',
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => false,
        'SHOW_SELECTED_COUNTER' => true,
        'SHOW_TOTAL_COUNTER' => true,
        'SHOW_PAGESIZE' => false,
        'SHOW_ACTION_PANEL' => true,
        'ALLOW_COLUMNS_SORT' => true,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => true,
        'ALLOW_PIN_HEADER' => true,
        'AJAX_OPTION_HISTORY' => 'N',
    ]);
    ?>
<?php endif; ?>