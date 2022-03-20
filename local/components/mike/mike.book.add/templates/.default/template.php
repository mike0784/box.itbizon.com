<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\UI\Extension::load("ui.forms");
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var BookAdd $component
 */

$component = $this->getComponent();
$arResult = $component->getResult();
?>
<?php $APPLICATION->SetTitle("Добавление книг"); ?>
<form method="POST">
    <div>
        <div>
            <div class="ui-ctl ui-ctl-textbox">
                <select name="Publisher">
                    <?php foreach($component->listPublisher as $value):?>
                        <option value="<?= $value['IDPUBLISHER'] ?>"><?= $value['NAMECOMPANY']==null? 'Название не задано': $value['NAMECOMPANY'] ?></option>
                    <?endforeach;?>
                </select>
            </div>
            <br>
            <div class="ui-ctl ui-ctl-textbox">
                <select name="Author">
                    <?php foreach($component->listAuthor as $value):?>
                        <option value="<?= $value['IDAUTHOR'] ?>"><?= $value['NAME']==null? 'Имя не задано': $value['NAME'] ?></option>
                    <? endforeach;?>
                </select>
            </div>
            <br>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text" name="TITLE" class="ui-ctl-element" placeholder="Название книги">
            </div>
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
</form>
<br>