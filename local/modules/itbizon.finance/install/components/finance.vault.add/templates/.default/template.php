<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Main\UI\Extension::load("crm.entity-editor");
Extension::load('itbizon.finance.bootstrap4');

/**@var $APPLICATION CAllMain * */
/**@var $arResult array * */
/**@var $this CBitrixComponentTemplate * */
/**@var $component CITBFinanceVaultAdd * */
$component = $this->getComponent();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-6 m-auto">
            <form>
                <div class="card">
                    <div class="card-header"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.TITLE') ?></div>
                    <div class="card-body">
                        <?php if($component->getError()) : ?>
                            <div class="alert alert-danger">
                                <?= $component->getError() ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="vault-name"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.FIELD.NAME') ?></label>
                            <input id="vault-name" class="form-control" type="text" name="DATA[NAME]" value="">
                        </div>
                        <div class="form-group">
                            <label for="vault-type"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.FIELD.TYPE') ?></label>
                            <select id="vault-type" class="form-control" name="DATA[TYPE]">
                                <? foreach (Finance\Model\VaultTable::getTypes() as $id => $value) : ?>
                                    <option value="<?= $id ?>"><?= $value ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vault-type"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.FIELD.GROUP_ID') ?></label>
                            <select id="vault-type" class="form-control" name="DATA[GROUP_ID]">
                                <option value="0">-</option>
                                <? foreach (Finance\Model\VaultGroupTable::getList(['order' => ['NAME' => 'ASC']])->fetchCollection() as $group) : ?>
                                    <option value="<?= $group->getId() ?>"><?= $group->getName() ?></option>
                                <? endforeach; ?>
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
                                'TITLE' => Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.FIELD.RESPONSIBLE')
                            ]
                        ); ?>

                        <div class="form-group">
                            <label for="vault-responsible"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.FIELD.BALANCE') ?></label>
                            <input id="vault-responsible" class="form-control" type="number" name="DATA[BALANCE]"
                                   value="" step="0.01">
                        </div>

                        <div class="form-check">
                            <input id="field-visible" class="form-check-input" type="checkbox" name="DATA[VISIBLE]">
                            <label for="field-visible"
                                   class="form-check-label"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.FIELD.VISIBLE') ?></label>
                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <button type="submit"
                                class="btn btn-success"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.BUTTON_ADD') ?></button>
                        <a class="btn btn-secondary"
                           href="../"><?= Loc::getMessage('ITB_FIN.VAULT_ADD.TEMPLATE.BUTTON_BACK') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
