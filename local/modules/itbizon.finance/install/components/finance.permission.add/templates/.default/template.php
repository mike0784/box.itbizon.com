<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinancePermissionAdd $component * */
/**@var array $arResult * */

$isIframe = ($_REQUEST['IFRAME'] == "Y");
$component = $this->getComponent();
?>
<div class="container-fluid">
    <div class="row <?= $isIframe ? "vh-100 overflow-auto" : "" ?>">
        <div class="<?= $isIframe ? "col-12 p-0" : "col-6 m-auto" ?>">
            <form id="form-module" method="post">
                <div class="card <?= $isIframe ? "rounded-0 border-bottom-0" : "" ?>">
                    <div class="card-header">
                        <?= Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.TEMPLATE.TITLE') ?>
                    </div>
                    <div class="card-body">
                        <?php if($component->error) : ?>
                            <div class="alert alert-danger">
                                <strong><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.WARNING") ?></strong> <?= $component->error ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="field-entity-type-id"><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.ENTITY_TYPE") ?></label>
                            <select class="form-control" name="DATA[ENTITY_TYPE_ID]" id="field-entity-type-id" required>
                                <option value="" selected
                                        disabled><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.MESS_ENTITY_TYPE") ?></option>
                                <?php foreach ($component->getEntityTypes() as $index => $name) : ?>
                                    <option value="<?= $index ?>">
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="field-entity-id"><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.ENTITY") ?></label>
                            <select class="form-control" name="DATA[ENTITY_ID]" id="field-entity-id" required>
                                <option value="" selected
                                        disabled><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.MESS.ENTITY") ?></option>
                                <?php foreach ($component->getEntity() as $data) : ?>
                                    <option data-toggle="<?= $data['TYPE'] ?>" value="<?= $data['ID'] ?>">
                                        <?= $data['NAME'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="field-action"><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.ACTION") ?></label>
                            <select class="form-control" name="DATA[ACTION]" id="field-action" required>
                                <option value="" selected
                                        disabled><?= Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.MESS.ACTION") ?></option>
                                <?php foreach ($component->getActions() as $index => $name) : ?>
                                    <option value="<?= $index ?>">
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?
                        $APPLICATION->IncludeComponent(
                            'bitrix:main.user.selector',
                            ' ',
                            [
                                "ID" => "field-user",
                                "API_VERSION" => 3,
//                                "LIST" => array_keys($crmQueueSelected),
                                "INPUT_NAME" => "DATA[USER]",
                                "USE_SYMBOLIC_ID" => true,
                                "BUTTON_SELECT_CAPTION" => Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.FIELD.USER"),
                                "SELECTOR_OPTIONS" =>
                                    [
                                        "departmentSelectDisable" => "N",
                                        'enableDepartments' => 'Y',
                                        'enableAll' => 'N',
                                        'userSearchArea' => 'I'
                                    ]
                            ]
                        );
                        ?>

                    </div>
                    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                        'BUTTONS' => [
                            [
                                'TYPE' => 'custom',
                                'LAYOUT' => "<button id='ui-button-panel-save' name='save' value='Y' class='ui-btn ui-btn-success'>" . Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.TEMPLATE.BUTTON_ADD") . "</button>"
                            ],
                            'cancel' => '../'
                        ]
                    ]); ?>
                    <input type="hidden" name="IFRAME" value="<?= htmlspecialchars($_REQUEST['IFRAME']); ?>">
                    <input type="hidden" name="IFRAME_TYPE" value="<?= htmlspecialchars($_REQUEST['IFRAME_TYPE']); ?>">
                </div>
            </form>
        </div>
    </div>
</div>