<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('ui.alerts');
Extension::load('ui.forms');

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceMailDomainEdit $component */
$component = $this->getComponent();
$maildomain = $component->getMaildomain();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.PAGE_TITLE')); ?>
<?php if ($component->getError()): ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $component->getError() ?></span>
    </div>
<?php endif; ?>
<?php if($maildomain): ?>
<form method="post">
    <label for="field-domain"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.DOMAIN') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-domain" name="DATA[DOMAIN]" type="text" class="ui-ctl-element" value="<?= $maildomain->getDomain() ?>">
        </div>
        <br>
    </label>
    <label for="field-active"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ACTIVE') ?>
        <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
            <div class="ui-ctl-after ui-ctl-icon-angle"></div>
            <select id="field-active" name="DATA[ACTIVE]" class="ui-ctl-element">
                <option value="Y" <?= ($maildomain->getActive()) ? 'selected' : '' ?> ><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ACTIVE.YES') ?></option>
                <option value="N" <?= (!$maildomain->getActive()) ? 'selected' : '' ?> ><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ACTIVE.NO') ?></option>
            </select>
        </div>
        <br>
    </label>
    <label for="field-server"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.SERVER') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-server" name="DATA[SERVER]" type="text" class="ui-ctl-element" value="<?= $maildomain->getServer() ?>">
        </div>
        <br>
    </label>
    <label for="field-port"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.PORT') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-port" name="DATA[PORT]" type="number" class="ui-ctl-element" value="<?= $maildomain->getPort() ?>">
        </div>
        <br>
    </label>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getHelper()->getUrl('list')
        ]
    ]); ?>
</form>
<?php endif; ?>