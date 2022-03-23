<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;
\Bitrix\Main\UI\Extension::load("ui.forms");
\Bitrix\Main\UI\Extension::load("ui.buttons");
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var AuthorView $component
 */

$component = $this->getComponent();

$APPLICATION->SetTitle("Список авторов");

?>
<?php
Toolbar::addButton(new Button([
    'color' => Color::PRIMARY,
    'click' => new JsCode(
        'BX.SidePanel.Instance.open(
                "' . $component->getRoute()->getUrl("") .'author.add/'. '",
                {
                cacheable: false,
                width: 450
                }
                );'
    ),
    'icon' => Icon::ADD,
    'text' => "Добавить автора",
]));
//$component->getRoute()->getUrl('mike.author.add')
?>
<?php
    $APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
        [
        'GRID_ID' => $component->getGrid()->getGridId(),
        'COLUMNS' => $component->getGrid()->getColumns(),
        'ROWS' => $component -> getGrid() -> getRows(),
        'SHOW_ROW_CHECKBOXES' => false,
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
                            ['VALUE' => 'minus', 'NAME' => 'Обновить']
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
    );?>