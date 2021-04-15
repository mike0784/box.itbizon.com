<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Utils\Money;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var $APPLICATION CAllMain */
/**@var $this CBitrixComponentTemplate */
/**@var $component CITBRequestEdit */

$isIframe = ($_REQUEST['IFRAME'] == "Y");
$component = $this->getComponent();
$request = $component->getRequest();
$category = $component->getCategory();
?>
<div class="container-fluid">
    <div class="row <?= $isIframe ? "vh-100 overflow-auto" : ""?>">
        <div class="<?= $isIframe ? "col-12 p-0" : "col-6 m-auto"?>">
            <form id="form-module">
                <div class="card <?= $isIframe ? "rounded-0 border-bottom-0" : ""?>">
                    <div class="card-header">
                        <?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.TITLE') ?>
                    </div>
                    <div class="card-body">
                        <?php if($component->getError()) : ?>
                            <div class="alert alert-danger">
                                <strong><?= Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.WARNING") ?></strong> <?= $component->getError() ?>
                            </div>
                        <?else:?>
                            <div class="form-group">
                                <label for="field-name"><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.NAME') ?></label>
                                <input id="field-name" class="form-control" type="text" name="DATA[NAME]" value="<?=$request->getName()?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="field-category"><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.CATEGORY') ?></label>
                                <input id="field-category" class="form-control" type="text" name="DATA[CATEGORY]" value="<?=$category[$request->getCategoryId()]?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="field-amount"><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.AMOUNT') ?></label>
                                <input id="field-amount" class="form-control" type="text" name="DATA[AMOUNT]" value="<?=Money::formatfromBase($request->getAmount())?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="field-situation"><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.SITUATION') ?></label>
                                <textarea id="field-situation" class="form-control" name="DATA[COMMENT_SITUATION]" readonly><?=$request->getCommentSituation()?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="field-data"><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.DATA') ?></label>
                                <textarea id="field-data" class="form-control" name="DATA[COMMENT_DATA]" readonly><?=$request->getCommentData()?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="field-company"><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.COMPANY') ?></label>
                                <input id="field-company" class="form-control" type="text" name="DATA[COMPANY]" value="<?=$request->getEntityName()?>" readonly>
                            </div>
                            <div class="form-group">
                                <label><?= Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.TEMPLATE.FIELD.FILE') ?></label><br>
                                <? if($request->getFileId()): ?>
                                    <a target="_blank" download href="<?= $request->getFileUrl() ?>">Скачать</a>
                                <? endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="IFRAME" value="<?= htmlspecialchars($_REQUEST['IFRAME']); ?>">
                    <input type="hidden" name="IFRAME_TYPE" value="<?= htmlspecialchars($_REQUEST['IFRAME_TYPE']); ?>">
                </div>
            </form>
        </div>
    </div>
</div>