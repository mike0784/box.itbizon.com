<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;
use Itbizon\Finance\Permission;

Loc::loadMessages(__FILE__);
\CJSCore::init('sidepanel');
Extension::load(['itbizon.bootstrap4', 'ui.alerts']);

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceStockList $component * */
global $APPLICATION;
$component = $this->getComponent();
?>
<?php if ($component->getRoute()->getAction() === 'list') : ?>
    <?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.STOCK.LIST.PAGE_TITLE')); ?>
    <?php foreach($component->getErrors()->getValues() as $error) : ?>
        <div class="ui-alert ui-alert-danger">
            <span class="ui-alert-message"><?= $error->getMessage() ?></span>
        </div>
    <?php endforeach; ?>
    <?php
    if(Permission::getInstance()->isAllowStockAdd()) {
        Toolbar::addButton(new Button([
            "color" => Color::PRIMARY,
            "click" => new JsCode(
                "BX.SidePanel.Instance.open('{$component->getRoute()->getUrl('add')}', {
                            cacheable: false,
                            width: 450
                        });"
            ),
            "icon" => Icon::ADD,
            "text" => Loc::getMessage('ITB_FIN.STOCK.LIST.BUTTON.CREATE'),
        ]));
    }
    if(Permission::getInstance()->isAllowStockEdit()) {
        Toolbar::addButton(new Button([
            "color" => Color::PRIMARY,
            "click" => new JsCode(
                "BX.SidePanel.Instance.open('{$component->getRoute()->getUrl('category')}', {
                            cacheable: false,
                            width: 800
                        });"
            ),
            "text" => Loc::getMessage('ITB_FIN.STOCK.LIST.BUTTON.CATEGORY_BIND'),
        ]));
        Toolbar::addButton(new Button([
            "color" => Color::PRIMARY,
            "click" => new JsCode(
                "BX.SidePanel.Instance.open('{$component->getRoute()->getUrl('transfer')}', {
                            cacheable: false,
                            width: 400
                        });"
            ),
            "text" => Loc::getMessage('ITB_FIN.STOCK.LIST.BUTTON.TRANSFER'),
        ]));
    }
    ?>
    <?
    $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $component->getGrid()->getGridId(),
        'COLUMNS' => $component->getGrid()->getColumns(),
        'ROWS' => $component->getGrid()->getRows(),
        'SHOW_ROW_CHECKBOXES' => false,
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
        'SHOW_NAVIGATION_PANEL' => false,
        'SHOW_PAGINATION' => false,
        'SHOW_SELECTED_COUNTER' => false,
        'SHOW_TOTAL_COUNTER' => false,
        'SHOW_PAGESIZE' => false,
        'SHOW_ACTION_PANEL' => false,
        'ALLOW_COLUMNS_SORT' => false,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => false,
        'ALLOW_PIN_HEADER' => true,
        'AJAX_OPTION_HISTORY' => 'N',
    ]);
    ?>
<?php else: ?>
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'itbizon:finance.stock.' . $component->getRoute()->getAction(),
            'POPUP_COMPONENT_TEMPLATE_NAME' => '',
            'POPUP_COMPONENT_PARAMS' => [
                'HELPER' => $component->getRoute(),
            ],
            'CLOSE_AFTER_SAVE' => true,
            'RELOAD_GRID_AFTER_SAVE' => true,
            'RELOAD_PAGE_AFTER_SAVE' => false,
            'PAGE_MODE' => false,
            'PAGE_MODE_OFF_BACK_URL' => $component->getRoute()->getUrl('list'),
            'USE_PADDING' => true,
            'PLAIN_VIEW' => false,
        ]
    );
    ?>
<?php endif; ?>
