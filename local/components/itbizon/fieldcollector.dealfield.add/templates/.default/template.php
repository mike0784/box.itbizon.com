<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\UI\Extension;

use Itbizon\Service\Component\Form;

Extension::load(['ui.alerts', 'ui.forms', 'itbizon.bootstrap4']); //Подключаем нужные для шаблона js расширения

if (!Loader::includeModule('itbizon.service'))
    throw new Exception('Ошибка подключения модуля itbizon.service');


/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBFieldcollectorDealfieldAdd $component */
$component = $this->getComponent();
?>
<?php $APPLICATION->SetTitle('Добавление поля'); // Заголовок окна ?>
<?php foreach($component->getErrors()->getValues() as $error) : // Выводим ошибки если они есть ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php
$request = Application::getInstance()->getContext()->getRequest();
$data = $request->getPost('DATA');
?>
<!-- Формируем форму https://dev.1c-bitrix.ru/api_d7/bitrix/ui/forms/common.php -->
<!-- Список типов полей https://dev.1c-bitrix.ru/api_d7/bitrix/ui/forms/fields_types.php -->
<form method="post">
    <div class="form-group row">
        <div class="col">
            <?php
            $APPLICATION->IncludeComponent(
            'itbizon:service.form.fieldset',
            '',
                [
                    'FIELDS' => [
                        (new Form\SelectField())->setName('CATEGORY_ID')
                            ->setTitle('Направление')
                            ->setOption([
                                //'required' => true,
                                'items' => $component->catList,
                                'use_empty' => true,
                            ]),

                        (new Form\SelectField())->setName('FIELD_ID')
                            ->setTitle('Поле')
                            ->setOption([
                                //'required' => true,
                                'items' => $component->fieldTypeList,
                                'use_empty' => true,
                            ]),

                    ]
                ]
            );
            ?>
        </div>
    </div>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('list')
        ]
    ]); ?>
</form>
