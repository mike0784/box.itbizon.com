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
$APPLICATION->SetTitle("Редактирование издательства");
?>
<form method="POST">
    <div>
        <div>
            <div class="ui-ctl ui-ctl-textbox">
                <label name="IDPUBLISHER">ID книги: <?= $component->id?></label>
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
