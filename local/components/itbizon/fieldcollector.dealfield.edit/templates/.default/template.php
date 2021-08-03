<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;

use Itbizon\Service\Component\Form;

Extension::load(['ui.alerts', 'ui.forms', 'itbizon.bootstrap4']); //Подключаем нужные для шаблона js расширения

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBFieldcollectorDealfieldEdit $component */
$component = $this->getComponent();
$item = $component->getItem();
?>
<?php $APPLICATION->SetTitle('Редактирование поля'); // Заголовок окна ?>
<?php foreach($component->getErrors()->getValues() as $error) : // Выводим ошибки если они есть ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php if ($item): ?>
    <!-- Формируем форму https://dev.1c-bitrix.ru/api_d7/bitrix/ui/forms/common.php -->
    <!-- Список типов полей https://dev.1c-bitrix.ru/api_d7/bitrix/ui/forms/fields_types.php -->
    <form method="post">


        <?php
        $APPLICATION->IncludeComponent(
            'itbizon:service.form.fieldset',
            '',
            [
                'FIELDS' => [
                    (new Form\SelectField())->setName('CATEGORY_ID')
                        ->setTitle('Направление')
                        ->setValue($component->dealCategoryId)
                        ->setOption([
                            //'required' => true,
                            'items' => $component->catList,
                            'use_empty' => true,
                        ]),

                    (new Form\SelectField())->setName('FIELD_ID')
                        ->setTitle('Поле')
                        ->setValue($component->dealFieldId)
                        ->setOption([
                            //'required' => true,
                            'items' => $component->fieldTypeList,
                            'use_empty' => true,
                        ]),

                ]
            ]
        );
        ?>


<!--
        <div class="form-group row">
            <div class="col">
                <label for="field-category-id">Категория</label>
                <select id="field-category-id" name="DATA[CATEGORY_ID]" class="form-control" required>
                    <? foreach($component->catList as $id => $name) : ?>
                        <option value="<?= $id ?>" <?if($id == $component->dealCategoryId) echo 'selected';?>><?= $name ?></option>
                    <? endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col">
                <label for="field-field-id">Поле</label>
                <select id="field-field-id" name="DATA[FIELD_ID]" class="form-control" required>
                    <? foreach($component->fieldTypeList as $id => $name) : ?>
                        <option value="<?= $id ?>" <?if($id == $component->dealFieldId) echo 'selected';?>><?= $name ?></option>
                    <? endforeach; ?>
                </select>
            </div>
        </div>
-->

        <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
            'BUTTONS' => [
                'save',
                'cancel' => $component->getRoute()->getUrl('list')
            ]
        ]); ?>
    </form>
<?php endif; ?>