<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Model\RequestTemplateTable;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

Asset::getInstance()->addCss('/local/modules/itbizon.finance/lib/utils/select2/css/select2.min.css');
Asset::getInstance()->addJs('/local/modules/itbizon.finance/lib/utils/select2/js/select2.min.js');

/**@var $APPLICATION CAllMain */
/**@var $this CBitrixComponentTemplate */
/**@var $component CITBRequestTemplateEdit */

$isIframe = ($_REQUEST['IFRAME'] == "Y");
$component = $this->getComponent();
$category = $component->getCategory();
asort($category);
$data = $_REQUEST['DATA'];
$required = ' <span style="color: red"><b>*</b></span>';

?>
<div class="container-fluid">
    <div class="row <?= $isIframe ? "vh-100 overflow-auto" : ""?>">
        <div class="<?= $isIframe ? "col-12 p-0" : "col-6 m-auto"?>">
            <form id="form-module">
                <div class="card <?= $isIframe ? "rounded-0 border-bottom-0" : ""?>">
                    <div class="card-header">
                        <?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.TITLE') ?>
                    </div>
                    <div class="card-body">
                        <?php if($component->getError()) : ?>
                            <div class="alert alert-danger">
                                <strong><?= Loc::getMessage("ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.WARNING") ?></strong> <?= $component->getError() ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="field-name"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.NAME').$required ?></label>
                            <input id="field-name" class="form-control" type="text" name="DATA[NAME]" value="<?=$data['NAME']?>" required>
                        </div>
                        <div class="form-group">
                            <label for="field-category"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.CATEGORY').$required ?></label>
                            <select id="field-category" class="form-control" name="DATA[CATEGORY]" required>
                                <option value=""></option>
                                <?foreach ($category as $id => $item) : ?>
                                    <option value="<?= $id ?>" <?if($id == $data['CATEGORY']) echo 'selected';?>><?=$item?></option>
                                <? endforeach;?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field-amount"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.AMOUNT').$required ?></label>
                            <input id="field-amount" class="form-control" type="number" step="0.01" name="DATA[AMOUNT]" value="<?=$data['AMOUNT']?>" required>
                        </div>
                        <div class="form-group">
                            <label for="field-active"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.ACTIVE').$required ?></label>
                            <select id="field-active" class="form-control" name="DATA[ACTIVE]" required>
                                <option value="Y" <?if($data['ACTIVE'] == 'Y') echo 'selected';?>><?=RequestTemplateTable::getActiveName('Y')?></option>
                                <option value="N" <?if($data['ACTIVE'] == 'N') echo 'selected';?>><?=RequestTemplateTable::getActiveName('N')?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field-situation"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.SITUATION').$required ?></label>
                            <textarea id="field-situation" class="form-control" name="DATA[COMMENT_SITUATION]" required><?=$data['COMMENT_SITUATION']?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="field-data"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.DATA').$required ?></label>
                            <textarea id="field-data" class="form-control" name="DATA[COMMENT_DATA]" required><?=$data['COMMENT_DATA']?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="field-solution"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.SOLUTION').$required ?></label>
                            <textarea id="field-solution" class="form-control" name="DATA[COMMENT_SOLUTION]" required><?=$data['COMMENT_SOLUTION']?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="field-company"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.COMPANY') ?></label>
                            <select id="field-company" class="form-control" name="DATA[COMPANY]">
                                <option value=""></option>
                                <?foreach ($component->getCompany() as $id => $item) : ?>
                                    <option value="<?= $id ?>" <?if($id == $data['COMPANY']) echo 'selected';?>><?=$item?></option>
                                <? endforeach;?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field-vault"><?= Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.FIELD.VAULT_ID') ?></label>
                            <select id="field-vault" class="form-control" name="DATA[VAULT_ID]">
                                <option value=""></option>
                                <?foreach ($component->getVault() as $id => $item) : ?>
                                    <option value="<?= $id ?>" <?if($id == $data['VAULT_ID']) echo 'selected';?>><?=$item?></option>
                                <? endforeach;?>
                            </select>
                        </div>
                    </div>
                    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
                        'BUTTONS' => [
                            [
                                'TYPE' => 'custom',
                                'LAYOUT' => "<button class='ui-btn ui-btn-success'>" . Loc::getMessage("ITB_FIN.REQ_TEMPLATE.EDIT.TEMPLATE.BUTTON.ADD") . "</button>"
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
<script>
    $(document).ready(function() {
        $('#field-company').select2();
        $('#field-category').select2();
        $('#field-vault').select2();
    });
</script>