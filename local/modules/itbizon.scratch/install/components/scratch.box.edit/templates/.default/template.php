<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4'); // fixme

/**@var CBitrixComponentTemplate $this * */
/**@var CITBScratchBoxEdit $component * */
$component = $this->getComponent();
$box = $component->getBox();

?>
<?php if ($component->getError()): ?>
    <div class="alert alert-danger"><?= $component->getError() ?></div>
<?php endif; ?>
<?php if ($box) : ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 m-auto">
                <form>
                    <div class="card">
                        <div class="card-header"><?= Loc::getMessage('ITB_SCRATCH.BOX_EDIT.TEMPLATE.TITLE') ?></div>
                        <div class="card-body">

                            <div class="form-group">
                                <label for="field-title"><?= Loc::getMessage('ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.TITLE') ?></label>
                                <input id="field-title" class="form-control" type="text" name="DATA[TITLE]"
                                       value="<?=$box->getTitle()?>">
                            </div>

                            <div class="form-group">
                                <label for="field-amount"><?= Loc::getMessage("ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.AMOUNT") ?></label>
                                <input id="field-amount" class="form-control" type="number" min="1" step="0.01"
                                       name="DATA[AMOUNT]" value="<?=$box->getAmount()?>" required>
                            </div>

                            <div class="form-group">
                                <label for="field-count"><?= Loc::getMessage("ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.COUNT") ?></label>
                                <input id="field-count" class="form-control" type="number" min="1" step="1"
                                       name="DATA[COUNT]" value="<?=$box->getCount()?>" required>
                            </div>

                            <div class="form-group">
                                <label for="box-comment"><?= Loc::getMessage('ITB_SCRATCH.BOX_EDIT.TEMPLATE.FIELD.COMMENT') ?></label>
                                <input id="box-comment" class="form-control" type="text" name="DATA[COMMENT]"
                                       value="<?=$box->getComment()?>">
                            </div>


                        </div>
                        <div class="card-footer text-muted">
                            <button type="submit"
                                    class="btn btn-success"><?= Loc::getMessage('ITB_SCRATCH.BOX_EDIT.TEMPLATE.BUTTON_SAVE') ?></button>
                            <a class="btn btn-secondary text-right"
                               href="../../"><?= Loc::getMessage('ITB_SCRATCH.BOX_EDIT.TEMPLATE.BUTTON_BACK') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<? endif; ?>