<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load("crm.entity-editor");
Extension::load('itbizon.finance.bootstrap4');  // fixme

/**@var CBitrixComponentTemplate $this * */
/**@var CITBScratchBoxAdd $component * */
$component = $this->getComponent();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-6 m-auto">
            <form>
                <div class="card">
                    <div class="card-header"><?= Loc::getMessage('ITB_SCRATCH.BOX_ADD.TEMPLATE.TITLE') ?></div>
                    <div class="card-body">

                        <?php if ($component->getError()) : ?>
                            <div class="alert alert-danger">
                                <?= $component->getError() ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="vault-name"><?= Loc::getMessage('ITB_SCRATCH.BOX_ADD.TEMPLATE.FIELD.TITLE') ?></label>
                            <input id="vault-name" class="form-control" type="text" name="DATA[TITLE]" value="">
                        </div>

                        <div class="form-group">
                            <label for="field-amount"><?= Loc::getMessage("ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.AMOUNT") ?></label>
                            <input id="field-amount" class="form-control" type="number" min="1" step="0.01"
                                   name="DATA[AMOUNT]" value="" required>
                        </div>

                        <div class="form-group">
                            <label for="field-count"><?= Loc::getMessage("ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.COUNT") ?></label>
                            <input id="field-count" class="form-control" type="number" min="1" step="1"
                                   name="DATA[COUNT]" value="" required>
                        </div>

                        <div class="form-group">
                            <label for="box-comment"><?= Loc::getMessage('ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.COMMENT') ?></label>
                            <input id="box-comment" class="form-control" type="text" name="DATA[COMMENT]"
                                   value="">
                        </div>

                    </div>

                    <div class="card-footer text-muted">
                        <button type="submit"
                                class="btn btn-success"><?= Loc::getMessage('ITB_SCRATCH.BOX_ADD.TEMPLATE.BUTTON_ADD') ?></button>
                        <a class="btn btn-secondary"
                           href="../"><?= Loc::getMessage('ITB_SCRATCH.BOX_ADD.TEMPLATE.BUTTON_BACK') ?></a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>