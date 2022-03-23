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
*@var BookUpdate $component
 */

$component = $this->getComponent();
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
                        (new Form\StringField())->setName('TITLE')
                            ->setTitle("TITLE")
                            ->setValue($component->nameBook),

                        (new Form\SelectField())->setName('IDPUBLISHER')
                            ->setTitle("Издательство")
                            ->setValue($component->getValuePublisher())
                            ->setOption([
                                'items' => $component->getListPublisher(),
                                'use_empty' => true,
                            ]),
                        (new Form\SelectField())->setName('IDAUTHOR')
                            ->setTitle("Автор")
                            ->setValue($component->getValueAuthor())
                            ->setOption([
                                'items' => $component->getListAuthor(),
                                'use_empty' => true,
                            ]),
                    ]
                ]
            );
            ?>
            <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                'BUTTONS' => [
                    'save',
                    'cancel' => $component->getRoute()->getUrl('list'),
                ]
            ]); ?>
        </div>
    </div>
</form>