<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Itbizon\Service\Component\Form;
\Bitrix\Main\UI\Extension::load("ui.forms");
Loc::loadMessages(__FILE__);
/**
*@var CAllMain $APPLICATION
*@var CBitrixComponentTemplate $this
*@var array $arResult
*@var PublisherUpdate $component
 */

$component = $this->getComponent();
$APPLICATION->SetTitle(Loc::getMessage('ITB_MIKE_PUBLISHER_UPDATE_DEFAULT_TEMPLETE_TITLE'));
?>
<form method="POST">
    <div class="form-group row">
        <div class="col">
            <?
            $APPLICATION->IncludeComponent(
                'itbizon:service.form.fieldset',
                '',
                [
                    'FIELDS' => [
                        (new Form\StringField())->setName('NAMECOMPANY')
                            ->setTitle(Loc::getMessage('ITB_MIKE_PUBLISHER_UPDATE_DEFAULT_TEMPLETE_FIELD_TITLE'))
                            ->setValue($component->nameCompany),
                    ]
                ]
            );
            ?>
            <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                'BUTTONS' => [
                    'save',
                    'cancel' => $component->getRoute()->getUrl('view'),
                ]
            ]); ?>
        </div>
    </div>
</form>
