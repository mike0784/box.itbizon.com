<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;
use Itbizon\Finance\Permission;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var $APPLICATION CAllMain * */
/**@var $arResult array * */
/**@var CITBFinanceCategoryList $component * */
$component = $this->getComponent();
?>
<?php if ($component->getError()): ?>
    <div class="alert alert-danger"><?= $component->getError() ?></div>
<?php endif; ?>
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
            if(Permission::getInstance()->isAllowCategoryAdd()) {
                Toolbar::addButton(new Button([
                    "color" => Color::PRIMARY,
                    "click" => new JsCode(
                        'document.location.href="' . $component->makeAddLink() . '";'
                    ),
                    "text" => Loc::getMessage('ITB_FIN.CATEGORY_LIST.BUTTON.CREATE_CATEGORY'),
                ]));
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <?php
            $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
                'GRID_ID' => $arResult['GRID_ID'],
                'COLUMNS' => $arResult['COLUMNS'],
                'ROWS' => $arResult['ROWS'],
                'SHOW_ROW_CHECKBOXES' => false,
                'NAV_OBJECT' => $arResult['NAV'],
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
                'SHOW_PAGINATION' => true,
                'SHOW_SELECTED_COUNTER' => true,
                'SHOW_TOTAL_COUNTER' => true,
                'SHOW_PAGESIZE' => true,
                'SHOW_ACTION_PANEL' => true,
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