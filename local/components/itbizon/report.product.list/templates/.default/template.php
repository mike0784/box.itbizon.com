<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Extension::load(['itbizon.bootstrap4', 'ui.alerts']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBCRKReportProductList $component */
$component = $this->getComponent();
?>
<?php $APPLICATION->SetTitle('Тест'); ?>
<?php
Toolbar::addFilter([
    'GRID_ID' => $component->arResult['gridId'],
    'FILTER_ID' => $component->arResult['gridId'],
    'FILTER' => $component->arResult['filter'],
    'ENABLE_LIVE_SEARCH' => false,
    'ENABLE_LABEL' => true,
    'RESET_TO_DEFAULT_MODE' => true,
]);
?>
<?php
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $component->arResult['gridId'],
    'COLUMNS' => $component->arResult['columns'],
    'ROWS' => $component->arResult['rows'],
    'SHOW_ROW_CHECKBOXES' => false,
    'NAV_OBJECT' => $component->arResult['navigation'],
    'TOTAL_ROWS_COUNT' => $component->arResult['navigation']->getRecordCount(),
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'PAGE_SIZES' => [
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '100']
    ],
    'AJAX_OPTION_JUMP' => 'N',
    'SHOW_CHECK_ALL_CHECKBOXES' => false,
    'SHOW_ROW_ACTIONS_MENU' => false,
    'SHOW_GRID_SETTINGS_MENU' => true,
    'SHOW_NAVIGATION_PANEL' => false,
    'SHOW_PAGINATION' => false,
    'SHOW_SELECTED_COUNTER' => false,
    'SHOW_TOTAL_COUNTER' => false,
    'SHOW_PAGESIZE' => false,
    'SHOW_ACTION_PANEL' => false,
    'ALLOW_COLUMNS_SORT' => true,
    'ALLOW_COLUMNS_RESIZE' => true,
    'ALLOW_HORIZONTAL_SCROLL' => true,
    'ALLOW_SORT' => false,
    'ALLOW_PIN_HEADER' => true,
    'AJAX_OPTION_HISTORY' => 'N',
]);
?>
