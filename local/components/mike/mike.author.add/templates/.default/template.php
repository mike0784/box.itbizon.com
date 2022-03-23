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
 *@var AuthorAdd $component
 */

$component = $this->getComponent();


?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_MIKE_AUTHOR_ADD_TEMPLATE_TITLE')); ?>
<form method="POST">
    <div class="form-group row">
        <div class="col">
            <label for="field-name"><?= Loc::getMessage('ITB_MIKE_AUTHOR_ADD_TEMPLATE_LABEL')?></label>
            <div class="ui-ctl ui-ctl-textbox">
                <input type="text"  name="AUTHOR" class="ui-ctl-element" placeholder="<?= Loc::getMessage('ITB_MIKE_AUTHOR_ADD_TEMPLATE_INPUT_PLACEHOLDER')?>">
            </div>
            <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                'BUTTONS' => [
                    'save',
                    'caption' => Loc::getMessage('ITB_MIKE_AUTHOR_ADD_TEMPLATE_BUTTON_CAPTION'),
                    'cancel' => $component->getRoute()->getUrl('view')
                ]
            ]); ?>
        </div>
    </div>
</form>
