<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceCategoryEdit $component * */
$component = $this->getComponent();
$category = $component->getCategory();

?>
<?php if ($component->getError()): ?>
    <div class="alert alert-danger"><?= $component->getError() ?></div>
<?php endif; ?>
<?php if ($category) : ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 m-auto">
                <form>
                    <div class="card">
                        <div class="card-header"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.TITLE') ?></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="vault-name"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.FIELD.NAME') ?></label>
                                <input id="vault-name" class="form-control" type="text" name="DATA[NAME]"
                                       value="<?= $category->getName() ?>">
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="DATA[ALLOW_INCOME]"
                                       id="INCOME" <?= $category->getAllowIncome() ? "checked" : "" ?>>
                                <label class="form-check-label"
                                       for="INCOME"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.FIELD.INCOME') ?></label>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="DATA[ALLOW_OUTGO]"
                                       id="OUTGO" <?= $category->getAllowOutgo() ? "checked" : "" ?>>
                                <label class="form-check-label"
                                       for="OUTGO"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.FIELD.OUTGO') ?></label>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="DATA[ALLOW_TRANSFER]"
                                       id="TRANSFER" <?= $category->getAllowTransfer() ? "checked" : "" ?>>
                                <label class="form-check-label"
                                       for="TRANSFER"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.FIELD.TRANSFER') ?></label>
                            </div>

                        </div>
                        <div class="card-footer text-muted">
                            <button type="submit"
                                    class="btn btn-success"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.BUTTON_SAVE') ?></button>
                            <a class="btn btn-secondary text-right"
                               href="../../"><?= Loc::getMessage('ITB_FIN.CATEGORY_EDIT.TEMPLATE.BUTTON_BACK') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<? endif; ?>