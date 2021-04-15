<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Loc::loadMessages(__FILE__);
\CJSCore::init("sidepanel");
Extension::load('itbizon.finance.bootstrap4');
/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceRequestList $component * */
$component = $this->getComponent();
/**
 * @var array $arResult
 */
?>
<?php if ($arResult['PAGE'] == 'list') : ?>
    <div class="container-fluid">
        <input type="hidden" id="filter__data" value='<?= json_encode($arResult['CURRENT_FILTER']) ?>'>
        <input type="hidden" id="ajax__path" value='<?= $component->getAjaxPath() ?>'>
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
                $linkButton = new Button([
                    "color" => Color::PRIMARY,
                    "click" => new JsCode(
                        "BX.SidePanel.Instance.open('{$arResult['PATH_ADD']}', {
                            cacheable: false,
                            width: 450
                        });"
                    ),
                    "text" => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.BUTTON.CREATE'),
                ]);
                $importExcel = new Button([
                    "color" => Color::SUCCESS,
                    'onclick' => "downloadExcel",
                    'data' => $arResult['IMPORT_PATH'],
                    "text" => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.BUTTON.IMPORT_EXCEL'),
                ]);
                Toolbar::addButton($importExcel);
                Toolbar::addButton($linkButton);
                //Filter
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div id="alert-wrapper">
                </div>
                <?
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
<?php else: ?>
    <?php
    if (isset($_REQUEST['IFRAME']) && $_REQUEST['IFRAME'] == 'Y') {
        $APPLICATION->IncludeComponent(
            'bitrix:ui.sidepanel.wrapper',
            '',
            array(
                'POPUP_COMPONENT_NAME' => 'itbizon:finance.request.' . $arResult['VARIABLES']['ACTION'],
                'POPUP_COMPONENT_TEMPLATE_NAME' => '',
                'POPUP_COMPONENT_PARAMS' => [
                    'FOLDER' => $arResult['FOLDER'],
                    'TEMPLATE_LIST' => $arResult['URL_TEMPLATES']['list'],
                    'TEMPLATE_ADD' => $arResult['URL_TEMPLATES']['add'],
                    'TEMPLATE_EDIT' => $arResult['URL_TEMPLATES']['edit'],
                    'VARIABLES' => $arResult['VARIABLES'],
                ],
                "USE_PADDING" => false,
                "PLAIN_VIEW" => true,
            )
        );
    } else {
        $APPLICATION->IncludeComponent(
        /** @var array $arResult */
            'itbizon:finance.request.' . $arResult['VARIABLES']['ACTION'],
            '',
            [
                'FOLDER' => $arResult['FOLDER'],
                'TEMPLATE_LIST' => $arResult['URL_TEMPLATES']['list'],
                'TEMPLATE_ADD' => $arResult['URL_TEMPLATES']['add'],
                'TEMPLATE_EDIT' => $arResult['URL_TEMPLATES']['edit'],
                'VARIABLES' => $arResult['VARIABLES'],
            ]
        );
    }
    ?>
<?php endif; ?>
