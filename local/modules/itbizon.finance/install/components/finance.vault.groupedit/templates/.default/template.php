<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var $APPLICATION CAllMain * */
/**@var $this CBitrixComponentTemplate * */
/**@var $component CITBFinanceVaultGroupEdit * */

$component = $this->getComponent();
$group = $component->getGroup();
?>
<?php if ($component->getError()) : ?>
    <div class="alert alert-danger">
        <?= $component->getError() ?>
    </div>
<?php endif; ?>
<?php if ($group) : ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 m-auto">
                <form>
                    <div class="card">
                        <div class="card-header"><?= Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.TEMPLATE.TITLE') ?></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="vault-name"><?= Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.TEMPLATE.FIELD.NAME') ?></label>
                                <input id="vault-name" class="form-control" type="text" name="DATA[NAME]"
                                       value="<?= $group->getName() ?>">
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <button type="submit"
                                    class="btn btn-success"><?= Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.TEMPLATE.BUTTON_SAVE') ?></button>
                            <a class="btn btn-secondary text-right"
                               href="../../"><?= Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.TEMPLATE.BUTTON_BACK') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>