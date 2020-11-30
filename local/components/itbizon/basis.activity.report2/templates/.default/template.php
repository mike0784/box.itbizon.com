<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Extension::load('ui.bootstrap4');

?>
<? if ($arResult['ERROR_MESSAGE']): ?>
    <div class="alert alert-danger"><?= $arResult['ERROR_MESSAGE'] ?></div>
<? else: ?>
    <div class="container-fluid">
        <div class="row p-1">
            <div class="col">
                <?php
                Toolbar::addFilter([
                    'GRID_ID' => $arResult['GRID_ID'],
                    'FILTER_ID' => $arResult['GRID_ID'],
                    'FILTER' => $arResult['FILTER'],
                    'ENABLE_LIVE_SEARCH' => true,
                    'ENABLE_LABEL' => true,
                    'RESET_TO_DEFAULT_MODE' => true,
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?
                $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
                    'GRID_ID' => $arResult['GRID_ID'],
                    'COLUMNS' => $arResult['COLUMNS'],
                    'ROWS' => $arResult['ROWS'],
                    'SHOW_ROW_CHECKBOXES' => false,
                    'NAV_OBJECT' => $arResult['NAV'],
                    'TOTAL_ROWS_COUNT' => $arResult['NAV']->getRecordCount(),
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
                    'SHOW_ROW_ACTIONS_MENU' => false,
                    'SHOW_GRID_SETTINGS_MENU' => true,
                    'SHOW_NAVIGATION_PANEL' => true,
                    'SHOW_PAGINATION' => true,
                    'SHOW_SELECTED_COUNTER' => true,
                    'SHOW_TOTAL_COUNTER' => true,
                    'SHOW_PAGESIZE' => true,
                    'SHOW_ACTION_PANEL' => false,
                    'ALLOW_COLUMNS_SORT' => true,
                    'ALLOW_COLUMNS_RESIZE' => true,
                    'ALLOW_HORIZONTAL_SCROLL' => true,
                    'ALLOW_SORT' => true,
                    'ALLOW_PIN_HEADER' => true,
                    'AJAX_OPTION_HISTORY' => 'N',
                ]);
                ?>
            </div>
        </div>
    </div>
<? endif; ?>