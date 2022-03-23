<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\UI\Extension;
use Itbizon\Service\Component\Form;
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
?>
<?php $APPLICATION->SetTitle("Добавление книги"); ?>
<form method="POST">
    <div class="form-group row">
        <div class="col">
            <?php
            $APPLICATION->IncludeComponent(
                'itbizon:service.form.fieldset',
                '',
                [
                    'FIELDS' => [
                        (new Form\StringField())->setName('TITLE')
                            ->setTitle("TITLE")
                            //->setValue("Введите название книги")
                            ->setOption([
                                'placeholder' => 'Введите название книги',
                            ]),
                        (new Form\SelectField())->setName('IDPUBLISHER')
                            ->setTitle("PUBLISHER")
                            //->setValue($data['PUBLISHER_ID'])
                            ->setOption([
                                'items' => $component->getListPublisher(),
                                'use_empty' => true,
                            ]),
                        (new Form\SelectField())->setName('IDAUTHOR')
                            ->setTitle("AUTHOR")
                            //->setValue($data['AUTHOR_ID'])
                            ->setOption([
                                'items' => $component->getListAuthor(),
                                'use_empty' => true,
                            ]),
                    ]
                ]
            ); ?>
        </div>
    </div>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('view')
        ]
    ]); ?>
</form>
<br>