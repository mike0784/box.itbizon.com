<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Itbizon\Service\Component\Form\StringField;
\Bitrix\Main\UI\Extension::load("ui.forms");

Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var BookUpdate $component
 */

$component = $this->getComponent();
$arResult = $component->getResult();

?>
<form method="POST">
    <div>
        <div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="ID" class="ui-ctl-element" placeholder="id книги">
            </div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="ID_PUBLISHER" class="ui-ctl-element" placeholder="id издательства">
            </div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="ID_AUTHOR" class="ui-ctl-element" placeholder="id автора">
            </div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="TITLE" class="ui-ctl-element" placeholder="Название">
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
