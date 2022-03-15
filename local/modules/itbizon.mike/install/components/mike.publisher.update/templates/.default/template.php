<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\UI\Extension::load("ui.forms");
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var PublisherUpdate $component
 */

$component = $this->getComponent();
$arResult = $component->getResult();

$list = array();
foreach($arResult as $key=>$value)
{
    $list[] = array('data' => array(
        "ID" => $value['ID_PUBLISHER'],
        "PUBLISHER" => $value['NAME_COMPANY'],
        "CREATE_AT" => "Ничего",
        "UPDATE_AT" => "НИЧЕГО"
    ));
}

?>
<form method="POST">
    <div>
        <div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="ID" class="ui-ctl-element" placeholder="Введите id издательства">
            </div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="PUBLISHER" class="ui-ctl-element" placeholder="Введите новое название издательства">
            </div>
            <div>
                <?$APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                    'BUTTONS' => [
                        [
                            'TYPE' => 'save', // тип - обязательный
                            'CAPTION' => 'Обновить', // название - не обязательный
                            'NAME' => 'update', // атрибут `name` инпута - не обязательный
                            //'ID' => 'my-save-id', // атрибут `id` инпута - не обязательный
                            'VALUE' => 'Y', // атрибут `value` инпута - не обязательный
                            //'ONCLICK' => '', // атрибут `onclick` инпута - не обязательный
                        ],
                    ]
                ]);?>
            </div>
        </div>
    </div>
</form>
<br>
