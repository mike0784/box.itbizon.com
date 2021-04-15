<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance;
use Itbizon\Finance\Operation;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var $APPLICATION CAllMain * */
/**@var $this CBitrixComponentTemplate* */
/**@var $component CITBFinanceOperationEdit* */
/**@var $operation Operation* */
$component = $this->getComponent();
$operation = $component->getOperation();
$historyEnd = new DateTime();
?>
<?php if ($component->getError()) : ?>
    <div class="alert alert-danger">
        <?= $component->getError() ?>
    </div>
<?php endif; ?>
<?php if ($operation): ?>
    <?php
    $categories = $component->getCategories($operation->getType());
    $vaults = $component->getVaults();
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <form method="post" data-ajax="<?= $component->getPathToAjax() ?>">
                    <div class="card">
                        <div class="card-header"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.TITLE') ?></div>
                        <div id="field-list" class="card-body">
                            <div class="form-group">
                                <label for="operation-name"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.NAME') ?></label>
                                <input id="operation-name" class="form-control" type="text" name="DATA[NAME]" value="<?= $operation->getName() ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="operation-status"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.STATUS') ?></label>
                                <input id="operation-status" class="form-control" type="text"
                                       value="<?= $operation->getStatusName() ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label for="operation-type"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.TYPE') ?></label>
                                <select class="form-control" name="DATA[TYPE]" id="operation-type" disabled>
                                    <option disabled
                                            selected><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.DEFAULT_TYPE') ?></option>
                                    <? foreach (Finance\Model\OperationTable::getType() as $id => $value) : ?>
                                        <option value="<?= $id ?>" <?= $operation->getType() == $id ? "selected" : "" ?>><?= $value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>

                            <!-- Если тип "Расход" или "Перевод" -->
                            <?php if ($operation->getSrcVault()) : ?>
                                <div class="form-group" id="operation-src-vault">
                                    <label><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.SRC_VAULT') ?></label>
                                    <select class="form-control vault" name="DATA[SRC_VAULT_ID]" <?= ($operation->getStatus() !== Finance\Model\OperationTable::STATUS_NEW) ? 'disabled' : '' ?> required>
                                        <option value=""></option>
                                        <? foreach ($vaults as $vault) : ?>
                                            <option value="<?= $vault->getId() ?>" <?= $operation->getSrcVaultId() === $vault->getId() ? "selected" : "" ?>><?= $vault->getName() ?></option>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- Если тип "Приход" или "Перевод" -->
                            <?php if ($operation->getDstVault()) : ?>
                                <div class="form-group" id="operation-dst-vault">
                                    <label for="operation-dst-vault"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.DST_VAULT') ?></label>
                                    <select class="form-control vault" name="DATA[DST_VAULT_ID]" <?= ($operation->getStatus() !== Finance\Model\OperationTable::STATUS_NEW) ? 'disabled' : '' ?> required>
                                        <option value=""></option>
                                        <? foreach ($vaults as $vault) : ?>
                                            <option value="<?= $vault->getId() ?>" <?= $operation->getDstVaultId() === $vault->getId() ? "selected" : "" ?>><?= $vault->getName() ?></option>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="form-group" id="operation-category">
                                <label for="operation-category"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.CATEGORY') ?></label>
                                <select class="form-control" name="DATA[CATEGORY_ID]" <?= ($operation->getStatus() !== Finance\Model\OperationTable::STATUS_NEW) ? 'disabled' : '' ?> required>
                                    <? foreach ($categories as $category) : ?>
                                        <option value="<?= $category->getId() ?>" <?= $operation->getCategoryId() === $category->getId() ? "selected" : "" ?>><?= $category->getName() ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>

                            <?php
                            $APPLICATION->IncludeComponent(
                                'itbizon:field.userselect',
                                '',
                                [
                                    'CURRENT_USER' => $operation->getResponsible() ? $operation->getResponsibleId() : 0,
                                    'FIELD_ID' => 'RESPONSIBLE_ID',
                                    'FIELD_NAME' => 'DATA[RESPONSIBLE_ID]',
                                    'TITLE' => Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.RESPONSIBLE'),
                                    'CHANGE_ACTIVE' => true
                                ]
                            ); ?>

                            <div class="form-group">
                                <label for="operation-amount"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.AMOUNT') ?></label>
                                <input id="operation-amount" class="form-control" type="number" step="0.01" min="0.01" name="DATA[AMOUNT]"
                                       value="<?= $operation->getAmountPrint() ?>" <?= ($operation->getStatus() !== Finance\Model\OperationTable::STATUS_NEW) ? 'disabled' : '' ?> required>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="operation-entity-type"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.ENTITY_TYPE') ?></label>
                                <select id="operation-entity-type" class="form-control" name="DATA[ENTITY_TYPE_ID]"
                                    <?= ($operation->getStatus() !== Finance\Model\OperationTable::STATUS_NEW) ? 'disabled' : '' ?> >
                                    <? foreach ($component->getCrmList() as $id => $value) : ?>
                                        <option value="<?= $id ?>" <?= $operation->getEntityTypeId() == $id ? "selected" : "" ?>><?= $value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="operation-entity"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.ENTITY') ?></label>
                                <input id="operation-entity" class="form-control" type="number" name="DATA[ENTITY_ID]"
                                       value="<?= $operation->getEntityId() ?>" <?= ($operation->getStatus() !== Finance\Model\OperationTable::STATUS_NEW) ? 'disabled' : '' ?> >
                            </div>
                            <div class="form-group">
                                <label for="operation-comment"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.COMMENT') ?></label>
                                <textarea id="operation-comment" class="form-control" name="DATA[COMMENT]"><?= $operation->getComment() ?></textarea>
                            </div>
                            <div class="form-group">
                                <label><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.FIELD.FILE') ?></label><br>
                                <? if($operation->getFileId()): ?>
                                    <a target="_blank" download href="<?= $operation->getFileUrl() ?>">Скачать</a>
                                <? endif; ?>
                            </div>
                            <?php if (count($component->getUserFields())) : ?>
                        </div>
                        <div class="card-footer p-0">
                        </div>
                        <div class="card-header"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.USER_FIELDS') ?></div>
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
                            <button type="submit" class="btn btn-success" name="save" value="Y"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.BUTTON_SAVE') ?></button>
                            <a class="btn btn-secondary text-right"
                               href="../../"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.BUTTON_BACK') ?></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.TITLE_HISTORY') ?></div>
                    <div class="card-body">
                        <form id="history" class="p-1" action="<?= $component->getPathToAjax() ?>">
                            <div class="form-row">
                                <div class="col">
                                    <input class="form-control" type="text" name="FROM"
                                           value="<?= $component->getBeginHistory()->format('d.m.Y') ?>"
                                           onclick="BX.calendar({node: this, field: this, bTime: false});">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="text" name="TO"
                                           value="<?= $component->getEndHistory()->format('d.m.Y') ?>"
                                           onclick="BX.calendar({node: this, field: this, bTime: false});">
                                </div>
                                <div class="col">
                                    <button type="button"
                                            class="btn btn-primary"><?= Loc::getMessage('ITB_FIN.OPERATION_EDIT.TEMPLATE.BUTTON_SHOW') ?></button>
                                </div>
                            </div>
                            <input type="hidden" name="ID" value="<?= $operation->getId(); ?>">
                        </form>
                        <div id="vault-history" style="max-height: 500px; overflow-y: scroll">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.form-group.disabled input').addClass('form-control');
        $('.form-group.disabled select').addClass('form-control');
        $('.form-group.disabled textarea').addClass('form-control');
        $('.form-group.disabled input').prop('disabled', true);
        $('.form-group.disabled select').prop('disabled', true);
        $('.form-group.disabled textarea').prop('disabled', true);
    </script>
<?php endif; ?>