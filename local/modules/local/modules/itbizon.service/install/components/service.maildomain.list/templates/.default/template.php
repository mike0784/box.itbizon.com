<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Loc::loadMessages(__FILE__);
Extension::load('ui.alerts');

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceMailDomainList $component */
$component = $this->getComponent();
?>
<?php if ($component->getHelper()->getAction() == 'list') : ?>
    <?php $APPLICATION->SetTitle(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.PAGE_TITLE')); ?>
    <?php if ($component->getError()): ?>
        <div class="ui-alert ui-alert-danger">
            <span class="ui-alert-message"><?= $component->getError() ?></span>
        </div>
    <?php endif; ?>
    <?php
    Toolbar::addFilter([
        'GRID_ID' => $component->getGridHelper()->getGridId(),
        'FILTER_ID' => $component->getGridHelper()->getFilterId(),
        'FILTER' => $component->getGridHelper()->getFilter(),
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true,
        'RESET_TO_DEFAULT_MODE' => true,
    ]);
    Toolbar::addButton(new Button([
        'color' => Color::PRIMARY,
        'click' => new JsCode(
            'BX.SidePanel.Instance.open("' . $component->getHelper()->getUrl('add') . '", {
                            cacheable: false,
                            width: 450
                        });'
        ),
        'icon' => Icon::ADD,
        'text' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.BUTTON.ADD'),
    ]));
    ?>
    <?
    $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $component->getGridHelper()->getGridId(),
        'COLUMNS' => $component->getGridHelper()->getColumns(),
        'ROWS' => $component->getGridHelper()->getRows(),
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
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => true,
        'SHOW_SELECTED_COUNTER' => false,
        'SHOW_TOTAL_COUNTER' => false,
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
<?php else: ?>
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'itbizon:service.maildomain.' . $component->getHelper()->getAction(),
            'POPUP_COMPONENT_TEMPLATE_NAME' => '',
            'POPUP_COMPONENT_PARAMS' => [
                'HELPER' => $component->getHelper(),
            ],
            'CLOSE_AFTER_SAVE' => true,
            'RELOAD_GRID_AFTER_SAVE' => true,
            'RELOAD_PAGE_AFTER_SAVE' => false,
            'PAGE_MODE' => false,
            'PAGE_MODE_OFF_BACK_URL' => $component->getHelper()->getUrl('list'),
            'USE_PADDING' => true,
            'PLAIN_VIEW' => false,
        ]
    );
    ?>
<?php endif; ?>
