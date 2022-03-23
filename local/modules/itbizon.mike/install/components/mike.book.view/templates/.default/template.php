<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
\Bitrix\Main\UI\Extension::load("ui.forms");
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Toolbar\Facade\Toolbar;
Extension::load(['ui.alerts', 'ui.dialogs.messagebox', 'itbizon.bootstrap4', "ui.buttons"]);
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var BookViewPage $component
 */

$component = $this->getComponent();
?>
<?php $APPLICATION->SetTitle("Список книг"); ?>
<? if($component->getRoute()!=null): ?>
    <?php if ($component->getRoute()->getAction() == 'view') : ?>
        <?php
            Toolbar::addFilter([ // Добавляем на тулбар фильтр
                'GRID_ID' => $component->getGrid()->getGridId(),
                'FILTER_ID' => $component->getGrid()->getFilterId(),
                'FILTER' => $component->getGrid()->getFilter(),
                'ENABLE_LIVE_SEARCH' => true,
                'ENABLE_LABEL' => true,
                'RESET_TO_DEFAULT_MODE' => true,
            ]);

            Toolbar::addButton(
                new Button([
                        'color' => Color::PRIMARY,
                        'click' => new JsCode(
                            'BX.SidePanel.Instance.open(
                                "' . $component->getRoute()->getUrl('book.add') . '",
                                    {
                                        cacheable: false,
                                        width: 600
                                    }
                                );'
                        ),
                        'icon' => Icon::ADD,
                        'text' => Loc::getMessage('ITB_MIKE_TEMPLATE_BUTTON_ADD'),
                    ]
                )
            );

            Toolbar::addButton(
                new Button([
                        'color' => Color::PRIMARY,
                        'click' => new JsCode(
                            'BX.SidePanel.Instance.open(
                                    "' . $component->getRoute()->getUrl('publisher.view') . '",
                                        {
                                            cacheable: false,
                                            width: 600
                                        }
                                    );'
                        ),
                        'icon' => Icon::ADD,
                        'text' => Loc::getMessage('ITB_MIKE_TEMPLATE_BUTTON_VIEW_PUBLISHER'),
                    ]
                )
            );

            Toolbar::addButton(
                new Button([
                        'color' => Color::PRIMARY,
                        'click' => new JsCode(
                            'BX.SidePanel.Instance.open(
                                        "' . $component->getRoute()->getUrl('author.view') . '",
                                            {
                                                cacheable: false,
                                                width: 600
                                            }
                                        );'
                        ),
                        'icon' => Icon::ADD,
                        'text' => Loc::getMessage('ITB_MIKE_TEMPLATE_BUTTON_VIEW_AUTHOR'),
                    ]
                )
            )
        ?>
        <?php
        $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
            [
            'GRID_ID' => $component->getGrid()->getGridId(),
            'COLUMNS' => $component->getGrid()->getColumns(),
            'ROWS' => $component -> getGrid() -> getRows(),
            'SHOW_ROW_CHECKBOXES' => true,
            'NAV_OBJECT' => $component->getGrid()->getNavigation(),
            'AJAX_MODE' => 'N',
            'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
            'PAGE_SIZES' => [
                ['NAME' => "5", 'VALUE' => '5'],
                ['NAME' => '10', 'VALUE' => '10'],
                ['NAME' => '20', 'VALUE' => '20'],
                ['NAME' => '50', 'VALUE' => '50'],
                ['NAME' => '100', 'VALUE' => '100']
            ],
            'AJAX_OPTION_JUMP'          => 'N',
            'SHOW_CHECK_ALL_CHECKBOXES' => true,
            'SHOW_ROW_ACTIONS_MENU'     => true,
            'SHOW_GRID_SETTINGS_MENU'   => true,
            'SHOW_NAVIGATION_PANEL'     => true,
            'SHOW_PAGINATION'           => true,
            'SHOW_SELECTED_COUNTER'     => true,
            'SHOW_TOTAL_COUNTER'        => true,
            'SHOW_PAGESIZE'             => true,
            'SHOW_ACTION_PANEL'         => true,
                'ACTION_PANEL'              => [
                    'GROUPS' => [
                        'TYPE' => [
                            'ITEMS' => [
                                [
                                    'ID'    => 'set-type',
                                    'TYPE'  => 'DROPDOWN',
                                    'ITEMS' => [
                                        ['VALUE' => '', 'NAME' => '- Выбрать -'],
                                        ['VALUE' => 'plus', 'NAME' => 'Удалить'],
                                        ['VALUE' => 'minus', 'NAME' => 'Редактировать']
                                    ]
                                ],
                            ],
                        ]
                    ],
                ],
            'ALLOW_COLUMNS_SORT'        => true,
            'ALLOW_COLUMNS_RESIZE'      => true,
            'ALLOW_HORIZONTAL_SCROLL'   => true,
            'ALLOW_SORT'                => true,
            'ALLOW_PIN_HEADER'          => true,
            'AJAX_OPTION_HISTORY'       => 'N'
            ]
        );
        ?>
    <?php else: ?>
        <?php
        $APPLICATION->IncludeComponent(
            'bitrix:ui.sidepanel.wrapper',
            '',
            [
                'POPUP_COMPONENT_NAME' => 'mike:mike.' . $component->getRoute()->getAction(),
                'POPUP_COMPONENT_TEMPLATE_NAME' => '',
                'POPUP_COMPONENT_PARAMS' => ['HELPER' => $component->getRoute(),],
                'CLOSE_AFTER_SAVE' => true,
                'RELOAD_GRID_AFTER_SAVE' => true,
                'RELOAD_PAGE_AFTER_SAVE' => false,
                'PAGE_MODE' => false,
                'PAGE_MODE_OFF_BACK_URL' => $component->getRoute()->getUrl('view'),
                'USE_PADDING' => true,
                'PLAIN_VIEW' => false,
                'USE_UI_TOOLBAR'=>'Y',
            ]
        );
        ?>
    <?php endif; ?>
<?php endif; ?>