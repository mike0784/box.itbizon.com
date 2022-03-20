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
*@var BookView $component
 */
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
CJSCore::Init(array('ajax')); CJSCore::Init(array("popup"));
$this->addExternalJS(__DIR__ . "\script.js");

$component = $this->getComponent();
?>

<?

        $url ='/local/components/mike/mike.book.view/read.book.php';
        //$url ='/read.book.php';
        $currentUrl= ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        echo $currentUrl;
        $APPLICATION->IncludeComponent(
            "bitrix:ui.button.panel",
            "",
            Array(
                "BUTTONS" => [
                    ['TYPE'=>'save','CAPTION'=>'Обновить','NAME'=>'update', 'ID' => 'updateBook', 'VALUE'=>'Y', 'onclick' => 'koll("updateBook")'],
                    ['TYPE'=>'save',
                        'CAPTION'=>'Редактировать книгу',
                        'NAME'=>'readBook',
                        'ID' => 'readBook',
                        'VALUE'=>'Y',
                        'ONCLICK' =>
                            'BX.ready(function(){
                                console.log("Редактировать книгу");
                                var pop = new BX.CDialog({
                                        "title": "Редактирование книги",
                                        "content_url": "'.$url. '",
                                        "draggable": true,
                                        "resizable": true,
                                        "buttons": [BX.CDialog.btnClose,]
                                    });
                                    BX.addCustomEvent(pop, "onWindowRegister",function(){console.log(this)});
                                    pop.Show();
                            })'

                    ],
                    ['TYPE'=>'save','CAPTION'=>'Удалить книгу','ID' => 'deleteBook', 'NAME'=>'deleteBook','VALUE'=>'Y', 'ONCLICK' => new JsCode('console.log("Удалить книгу")')],
                    ['TYPE'=>'save','CAPTION'=>'Добавить книгу', 'ID' => 'addBook', 'NAME'=>'addBook','VALUE'=>'Y', 'onclick' => 'koll("addBook")'],
                    ]
            )
        );?>

         <br>
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
        );
        ?>

<?php
$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'POPUP_COMPONENT_NAME' => 'mike:mike.book.add' ,
        'POPUP_COMPONENT_TEMPLATE_NAME' => '',
        'POPUP_COMPONENT_PARAMS' => [],
        'CLOSE_AFTER_SAVE' => true,
        'RELOAD_GRID_AFTER_SAVE' => true,
        'RELOAD_PAGE_AFTER_SAVE' => false,
        'PAGE_MODE' => false,
        'PAGE_MODE_OFF_BACK_URL' => $currentUrl,
        'USE_PADDING' => true,
        'PLAIN_VIEW' => false,
        'USE_UI_TOOLBAR'=>'Y',
    ]
);
?>


