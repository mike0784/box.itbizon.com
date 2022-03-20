<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
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
$arResult = $component->getResult();

$APPLICATION->SetTitle("Список авторов");

?>
    <?php
    $APPLICATION->IncludeComponent(
        "bitrix:ui.button.panel",
        "",
        Array(
            "BUTTONS" => [
                ['TYPE'=>'save',
                    'CAPTION'=>'Добавить автора',
                    'NAME'=>'aurhorview',
                    'ID' => 'aurhorview',
                    'VALUE'=>'Y',
                    'onclick' =>  'BX.ready(function(){
                                    BX.SidePanel.Instance.open(
                                        "' . $component->getRoute()->getUrl('mike.author.add') . '",
                                        {
                                            cacheable: false,
                                            width: 600
                                        }
                                    );
                                })'
                ],
            ]
        )
    );
    ?>
    <?php
    $APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
        [
        'GRID_ID' => $component->gridId,
        'COLUMNS' => $component->gridColumns,
        'ROWS' => $component -> gridRows,
        'SHOW_ROW_CHECKBOXES' => true,
        'NAV_OBJECT' => $component->gridNav,
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