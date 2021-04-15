<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');
Extension::load('itbizon.finance.select2');
Extension::load("ui.notification");

/**@var $APPLICATION CAllMain */
/**@var $this CBitrixComponentTemplate */
/**@var $component CITBFinanceIncome */

$isIframe = ($_REQUEST['IFRAME'] == "Y");
$component = $this->getComponent();
?>
<div class="container-fluid">
    <div class="row <?= $isIframe ? "vh-100 overflow-auto" : "" ?>">
        <div class="<?= $isIframe ? "col-12 p-0" : "col-6 m-auto" ?>">
            <form id="form-module">
                <div class="card <?= $isIframe ? "rounded-0 border-bottom-0" : "" ?>">
                    <div class="card-header">
                        <?= Loc::getMessage('ITB_FIN.INCOME_TEMPLATE.TEMPLATE.TITLE') ?>
                    </div>
                    <div class="card-body">
                        <?php if($component->getError()) : ?>
                            <div class="alert alert-danger">
                                <strong><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.WARNING") ?></strong> <?= $component->getError() ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="field-title"><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.NAME") ?></label>
                            <input id="field-title" class="form-control" type="text" name="DATA[NAME]" required>
                        </div>

                        <div class="form-group">
                            <label for="field-amount"><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.AMOUNT") ?></label>
                            <input id="field-amount" class="form-control" type="number" min="1" step="0.01"
                                   name="DATA[AMOUNT]" required>
                        </div>

                        <div class="form-group">
                            <label for="field-vault"><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.VAULT") ?></label>
                            <select id="field-vault" class="form-control vault" name="DATA[DST_VAULT_ID]" required>
                                <? foreach ($component->getVaults() as $indexVault => $arrVault) : ?>
                                    <option value="<?= $indexVault ?>"><?= $arrVault['NAME'] ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="field-category"><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.CATEGORY") ?></label>
                            <select id="field-category" class="form-control" name="DATA[CATEGORY_ID]" required>
                                <? foreach ($component->getCategories() as $indexCategory => $category) : ?>
                                    <option value="<?= $indexCategory ?>"><?= $category['NAME'] ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="field-comment"><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.COMMENT") ?></label>
                            <textarea id="field-comment" class="form-control" name="DATA[COMMENT]"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="field-company"><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.COMPANY") ?></label>
                            <select id="field-company" class="form-control" name="DATA[ENTITY_ID]">
                                <option value="" selected disabled><?= Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.FIELD.COMPANY_MESSAGE") ?></option>
                                <? foreach ($component->getCompanies() as $indexCompany => $titleCompany) : ?>
                                    <option value="<?= $indexCompany ?>"><?= $titleCompany ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>

                    </div>
                    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                        'BUTTONS' => [
                            [
                                'TYPE' => 'custom',
                                'LAYOUT' => "<button class='ui-btn ui-btn-success'>" . Loc::getMessage("ITB_FIN.INCOME_TEMPLATE.TEMPLATE.BUTTON.ADD") . "</button>"
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
