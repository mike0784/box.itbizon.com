<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Extension::load(['ui.alerts', 'ui.dialogs.messagebox', 'itbizon.bootstrap4']); //Подключаем нужные для шаблона js расширения

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBFieldcollectorDealfieldList $component */
$component = $this->getComponent();
?>
<?php if ($component->getRoute()->getAction() == 'list') : ?>
    <?php $APPLICATION->SetTitle('Список полей'); // Заголовок страницы ?>
    <?php foreach($component->getErrors()->getValues() as $error) : // Выводим ошибки если они есть ?>
        <div class="ui-alert ui-alert-danger">
            <span class="ui-alert-message"><?= $error->getMessage() ?></span>
        </div>
    <?php endforeach; ?>
    <?php
    // Формируем тулбар https://dev.1c-bitrix.ru/api_d7/bitrix/ui/toolbar/get_started.php
    Toolbar::addFilter([ // Добавляем на тулбар фильтр
        'GRID_ID' => $component->getGrid()->getGridId(),
        'FILTER_ID' => $component->getGrid()->getFilterId(),
        'FILTER' => $component->getGrid()->getFilter(),
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true,
        'RESET_TO_DEFAULT_MODE' => true,
    ]);
    Toolbar::addButton(new Button([ // Добавляем на тулбар кнопку
        'color' => Color::PRIMARY,
        'click' => new JsCode(
            'BX.SidePanel.Instance.open(
                "' . $component->getRoute()->getUrl('add') . '",
                {
                    cacheable: false,
                    width: 600
                }
            );'
        ),
        'icon' => Icon::ADD,
        'text' => 'Добавить',
    ]));
    ?>
    <?
    // Подключаем компонент грида https://dev.1c-bitrix.ru/api_d7/bitrix/main/systemcomponents/gridandfilter/mainuigrid.php
    $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $component->getGrid()->getGridId(),
        'COLUMNS' => $component->getGrid()->getColumns(),
        'ROWS' => $component->getGrid()->getRows(),
        'NAV_OBJECT' => $component->getGrid()->getNavigation(),
        'SHOW_ROW_CHECKBOXES' => false,
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => [
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'TOTAL_ROWS_COUNT' => $component->getGrid()->getNavigation()->getRecordCount(),
        'AJAX_OPTION_JUMP' => 'N',
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => true,
        'SHOW_SELECTED_COUNTER' => false,
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
<?php else: ?>
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'itbizon:fieldcollector.dealfield.' . $component->getRoute()->getAction(),
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
            'USE_UI_TOOLBAR'=>'Y',
        ]
    );
    ?>
<?php endif; ?>