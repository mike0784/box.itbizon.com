<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance;
use \Bitrix\Main\UI\Extension;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');
Main\UI\Extension::load("crm.entity-editor");

/**@var $APPLICATION CAllMain * */
/**@var $this CBitrixComponentTemplate* */
/**@var $component CITBFinanceOperationAdd* */
$component = $this->getComponent();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-6 m-auto">
            <form method="post" enctype="multipart/form-data" data-ajax="<?= $component->getPathToAjax() ?>">
                <div class="card">
                    <div class="card-header"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.TITLE') ?></div>
                    <div id="field-list" class="card-body">

                        <?php if ($component->getError()) : ?>
                            <div class="alert alert-danger">
                                <?= $component->getError() ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="operation-name"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.NAME') ?></label>
                            <input id="operation-name" class="form-control" type="text" name="DATA[NAME]">
                        </div>

                        <div class="form-group">
                            <label for="operation-type"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.TYPE') ?></label>
                            <select class="form-control" name="DATA[TYPE]" id="operation-type">
                                <option disabled
                                        selected><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.DEFAULT_TYPE') ?></option>
                                <? foreach (Finance\Model\OperationTable::getType() as $id => $value) : ?>
                                    <option value="<?= $id ?>"><?= $value ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>

                        <!-- Если тип "Расход" или "Перевод" -->
                        <div class="form-group d-hidden d-none" id="operation-src-vault">
                            <label><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.SRC_VAULT') ?></label>
                            <select class="form-control vault" name="DATA[SRC_VAULT_ID]">
                            </select>
                        </div>

                        <!-- Если тип "Приход" или "Перевод" -->
                        <div class="form-group d-hidden d-none" id="operation-dst-vault">
                            <label><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.DST_VAULT') ?></label>
                            <select class="form-control vault" name="DATA[DST_VAULT_ID]">
                            </select>
                        </div>

                        <div class="form-group d-hidden d-none" id="operation-category">
                            <label><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.CATEGORY') ?></label>
                            <select class="form-control" name="DATA[CATEGORY_ID]">
                            </select>
                        </div>

                        <?php
                        $APPLICATION->IncludeComponent(
                            'itbizon:field.userselect',
                            '',
                            [
                                'CURRENT_USER' => $component->userId,
                                'FIELD_ID' => 'RESPONSIBLE_ID',
                                'FIELD_NAME' => 'DATA[RESPONSIBLE_ID]',
                                'TITLE' => Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.RESPONSIBLE'),
                                'CHANGE_ACTIVE' => false
                            ]
                        ); ?>

                        <div class="form-group">
                            <label for="operation-amount"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.AMOUNT') ?></label>
                            <input id="operation-amount" class="form-control" type="number" name="DATA[AMOUNT]"
                                   step="0.01">
                        </div>

                        <hr>

                        <div class="form-group" id="operation-entity-type">
                            <label><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.ENTITY_TYPE') ?>
                            </label>
                            <select class="form-control" name="DATA[ENTITY_TYPE_ID]">
                                <option disabled
                                        selected><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.DEFAULT_ENTITY_TYPE') ?></option>
                                <? foreach ($component->getCrmList() as $id => $value) : ?>
                                    <option value="<?= $id ?>"><?= $value ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group d-none" id="operation-entity">
                            <label for="operation-entity-input"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.ENTITY') ?></label>
                            <input id="operation-entity-input" class="form-control" type="number"
                                   name="DATA[ENTITY_ID]">
                        </div>

                        <div class="form-group">
                            <label for="operation-comment"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.COMMENT') ?></label>
                            <textarea id="operation-comment" class="form-control" name="DATA[COMMENT]"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="field-file"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.FIELD.FILE') ?></label>
                            <input id="field-file" class="form-control" type="file" name="FILE">
                        </div>
                        <?php if (count($component->getUserFields())) : ?>
                    </div>
                    <div class="card-footer p-0">
                    </div>
                    <div class="card-header"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.USER_FIELDS') ?></div>
                    <div class="card-body">
                        <?php foreach ($component->getUserFields() as $key => $userField) : ?>
                            <div class="form-group disabled">
                                <label for="<?= $key ?>"><?= $userField['EDIT_FORM_LABEL'] ?></label>
                                <?php $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $userField["USER_TYPE"]["USER_TYPE_ID"],
                                    [
                                        "bVarsFromForm" => false,
                                        "arUserField" => $userField
                                    ],
                                    null,
                                    array("HIDE_ICONS" => "Y"))
                                ?>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer text-muted">
                        <button type="submit"
                                class="btn btn-success"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.BUTTON_ADD') ?></button>
                        <a class="btn btn-secondary"
                           href="../"><?= Loc::getMessage('ITB_FIN.OPERATION_ADD.TEMPLATE.BUTTON_BACK') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
