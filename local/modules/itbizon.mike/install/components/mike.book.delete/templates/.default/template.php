<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\UI\Extension::load("ui.forms");
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
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
        <div class="ui-ctl ui-ctl-textbox">
            <input type="text" name="ID" class="ui-ctl-element" placeholder="Введите id книги">
        </div>
        <div>
            <?$APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                'BUTTONS' => [
                    [
                        'TYPE' => 'save', // тип - обязательный
                        'CAPTION' => 'Удалить', // название - не обязательный
                        'NAME' => 'delete', // атрибут `name` инпута - не обязательный
                        //'ID' => 'my-save-id', // атрибут `id` инпута - не обязательный
                        'VALUE' => 'Y', // атрибут `value` инпута - не обязательный
                        //'ONCLICK' => '', // атрибут `onclick` инпута - не обязательный
                    ],
                ]
            ]);?>
        </div>
    </div>
</form>
<br>

