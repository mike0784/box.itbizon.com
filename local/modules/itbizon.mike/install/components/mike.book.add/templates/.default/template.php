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
        "CREATE_AT" => "Ничего",
        "UPDATE_AT" => "НИЧЕГО"
    ));
}

?>
<form method="POST">
    <div>
        <div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="PUBLISHER" class="ui-ctl-element" placeholder="id издательства">
            </div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="AUTHOR" class="ui-ctl-element" placeholder="id автора">
            </div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="TITLE" class="ui-ctl-element" placeholder="Название книги">
            </div>
            <div>
                <?$APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                    'BUTTONS' => [
                        [
                            'TYPE' => 'save', // тип - обязательный
                            'CAPTION' => 'Добавить', // название - не обязательный
                            'NAME' => 'add', // атрибут `name` инпута - не обязательный
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