<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\UI\Extension::load("ui.forms");
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var PublisherView $component
 */

$component = $this->getComponent();
$arResult = $component->getResult();

$list = array();
foreach($arResult as $key=>$value)
{
    $list[] = array('data' => array(
        "ID" => $value['ID_PUBLISHER'],
        "PUBLISHER" => $value['NAME_COMPANY'],
        "CREATE_AT" => $value['CREATE_AT'],
        "UPDATE_AT" => $value['UPDATE_AT']
    ));
}

?>
<form method="POST">
    <div>
        <div>
            <div>
                <button class="ui-btn ui-btn-success ui-btn-sm ui-btn-dropdown" name="update">Обновить данные</button>
            </div>
        </div>
    </div>
</form>
<br>
<?php
$grid_options = new Bitrix\Main\Grid\Options('report_list');
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$nav = new Bitrix\Main\UI\PageNavigation('report_list');
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();
$APPLICATION->IncludeComponent(
'bitrix:main.ui.grid',
'',
    [
    'GRID_ID' => 'report_list',
    'COLUMNS' => [
        ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'PUBLISHER', 'name' => 'Наименование организации', 'sort' => 'DATE', 'default' => true],
        ['id' => 'CREATE_AT', 'name' => 'Дата создания', 'sort' => 'AMOUNT', 'default' => true],
        ['id' => 'UPDATE_AT', 'name' => 'Дата обнавления', 'sort' => 'PAYER_INN', 'default' => true],
    ],
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => true,
    'NAV_OBJECT' => $nav,
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
