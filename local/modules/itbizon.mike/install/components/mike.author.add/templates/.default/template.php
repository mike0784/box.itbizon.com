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
 */

/*$component = $this->getComponent();
$arResult = $component->getResult();*/

?>
<form method="POST">
    <div>
        <div>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text"  name="AUTHOR" class="ui-ctl-element" placeholder="Имя автора">
            </div>
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
</form>