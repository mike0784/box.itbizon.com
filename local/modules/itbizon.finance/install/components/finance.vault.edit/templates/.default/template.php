<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');
Extension::load("ui.notification");

/**@var $APPLICATION CAllMain * */
/**@var $this CBitrixComponentTemplate * */
/**@var $component CITBFinanceVaultEdit * */

$component = $this->getComponent();
$vault = $component->getVault();
?>
<?php if($component->getError()) : ?>
    <div class="alert alert-danger">
        <?= $component->getError() ?>
    </div>
<?php endif; ?>
<?php if($vault) : ?>
    <div id="finance-vault-edit-container" class="container-fluid" data-action="<?= $component->getPathToAjax() ?>">
        <div class="row">
            <div class="col-6">
                <form>
                    <div class="card">
                        <div class="card-header"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.TITLE') ?></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="vault-name"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.NAME') ?></label>
                                <input id="vault-name" class="form-control" type="text" name="DATA[NAME]"
                                       value="<?= $vault->getName() ?>">
                            </div>
                            <div class="form-group">
                                <label for="vault-type"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.TYPE') ?></label>
                                <? if($vault->isVirtual()): ?>
                                    <select id="vault-type" class="form-control" name="DATA[TYPE]" disabled>
                                        <? foreach (Finance\Model\VaultTable::getTypes() as $id => $value) : ?>
                                            <? if($id !== Finance\Model\VaultTable::TYPE_VIRTUAL) continue; ?>
                                            <option value="<?= $id ?>" <?= ($id === $vault->getType()) ? 'selected' : '' ?> ><?= $value ?></option>
                                        <? endforeach; ?>
                                    </select>
                                <? else: ?>
                                    <select id="vault-type" class="form-control" name="DATA[TYPE]">
                                        <? foreach (Finance\Model\VaultTable::getTypes() as $id => $value) : ?>
                                            <? if($id === Finance\Model\VaultTable::TYPE_VIRTUAL) continue; ?>
                                            <option value="<?= $id ?>" <?= ($id === $vault->getType()) ? 'selected' : '' ?> ><?= $value ?></option>
                                        <? endforeach; ?>
                                    </select>
                                <? endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="vault-type"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.GROUP_ID') ?></label>
                                <select id="vault-type" class="form-control" name="DATA[GROUP_ID]">
                                    <option value="0">-</option>
                                    <? foreach (Finance\Model\VaultGroupTable::getList(['order' => ['NAME' => 'ASC']])->fetchCollection() as $group) : ?>
                                        <option value="<?= $group->getId() ?>" <?= ($group->getId() === $vault->getGroupId()) ? 'selected' : '' ?> ><?= $group->getName() ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vault-date-create"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.DATE_CREATE') ?></label>
                                <input id="vault-date-create" class="form-control" type="text" name="DATA[DATE_CREATE]"
                                       value="<?= $vault->getDateCreate() ?>" disabled>
                            </div>

                            <?php $APPLICATION->IncludeComponent(
                                'itbizon:field.userselect',
                                '',
                                [
                                    'CURRENT_USER' => $vault->getResponsibleId(),
                                    'FIELD_ID' => 'RESPONSIBLE_ID',
                                    'FIELD_NAME' => 'DATA[RESPONSIBLE_ID]',
                                    'TITLE' => Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.RESPONSIBLE')
                                ]
                            ); ?>

                            <div class="form-group">
                                <label for="vault-balance"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.BALANCE') ?></label>
                                <input id="vault-balance" class="form-control" type="text" name="DATA[BALANCE]"
                                       value="<?= $vault->getBalancePrint() ?>" <?= !$vault->isVirtual() ? 'disabled' : '' ?> >
                            </div>

                            <div class="form-check">
                                <input id="field-visible" class="form-check-input" type="checkbox" name="DATA[VISIBLE]" <?= $vault->getHideOnPlanning() ? 'checked' : ''?>>
                                <label for="field-visible" class="form-check-label"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.FIELD.VISIBLE') ?></label>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <button type="submit"
                                    class="btn btn-success"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.BUTTON_SAVE') ?></button>
                            <a class="btn btn-secondary text-right"
                               href="../../"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.BUTTON_BACK') ?></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link rounded-0 active" data-toggle="tab"
                                   href="#histor-tab"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.TITLE_HISTORY') ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-0" data-toggle="tab"
                                   href="#access-tab"><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.TEMPLATE.TITLE_PERMISSION") ?></a>
                            </li>
                        </ul>

                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane container active px-0" id="histor-tab">
                                <form id="history" class="p-1">
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
                                                    class="btn btn-primary"><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.TEMPLATE.BUTTON_SHOW') ?></button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="ID" value="<?= $vault->getId(); ?>">
                                </form>
                                <div id="vault-history" style="max-height: 500px; overflow-y: scroll"></div>
                            </div>
                            <div class="tab-pane container fade px-0" id="access-tab">
                                <form id="append-access-right" class="mb-1">
                                    <div class="form-group">
                                        <label for="field-user-id"><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.TEMPLATE.USER_ID") ?></label>
                                        <?php

                                        $APPLICATION->IncludeComponent(
                                            'bitrix:main.user.selector',
                                            '',
                                            [
                                                'ID' => 'user-id',
                                                'INPUT_NAME' => 'USER_ID',
                                                "LAZYLOAD" => 'N',
                                                "USE_SYMBOLIC_ID" => "Y",
                                                "API_VERSION" => 3,
                                                "SELECTOR_OPTIONS" => array(
                                                    'lazyLoad' => 'N',
                                                    'context' => 'GRATITUDE',
                                                    'contextCode' => '',
                                                    'enableSonetgroups' => 'N',
                                                    'departmentSelectDisable' => 'N',
                                                    'showVacations' => 'N',
                                                    'disableLast' => 'N',
                                                    'enableAll' => 'N',
                                                    'lheName' => ""
                                                )
                                            ]
                                        ); ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="field-action"><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.TEMPLATE.ACTION") ?></label>
                                        <select class="form-control form-control-sm" name="ACTION_ACCESS"
                                                id="field-action">
                                            <option value="" selected disabled><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.TEMPLATE.ACTION_SELECT_MESSAGE") ?></option>
                                            <?php foreach ($component->getActions() as $indexType => $nameType) : ?>
                                                <option value="<?= $indexType ?>"><?= $nameType ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group ">
                                        <button class="btn btn-success w-100" type="submit"><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.TEMPLATE.ISSUE_RIGHTS") ?></button>
                                    </div>
                                    <input type="hidden" name="ID" value="<?= $vault->getId(); ?>">
                                    <input type="hidden" name="ACTION" value="ADD_ACCESS">
                                </form>
                                <hr>
                                <div id="access-rights-list" data-vault-id="<?= $vault->getId(); ?>"
                                     style="max-height: 500px; overflow-y: scroll"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>