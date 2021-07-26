<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('ui.alerts');
Extension::load('ui.forms');

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceMailDomainEdit $component */
$component = $this->getComponent();
$request = Application::getInstance()->getContext()->getRequest();
$data = $request->getPost('DATA');
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.PAGE_TITLE')); ?>
<?php if ($component->getError()): ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $component->getError() ?></span>
    </div>
<?php endif; ?>
<form method="post">
    <label for="field-domain"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.DOMAIN') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-domain" name="DATA[DOMAIN]" type="text" class="ui-ctl-element" value="<?= $data['DOMAIN'] ?>">
        </div>
        <br>
    </label>
    <label for="field-active"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.ACTIVE') ?>
        <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
            <div class="ui-ctl-after ui-ctl-icon-angle"></div>
            <select id="field-active" name="DATA[ACTIVE]" class="ui-ctl-element">
                <option value="Y" <?= ($data['ACTIVE'] === 'Y') ? 'selected' : '' ?> ><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.ACTIVE.YES') ?></option>
                <option value="N" <?= ($data['ACTIVE'] === 'N') ? 'selected' : '' ?> ><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.ACTIVE.NO') ?></option>
            </select>
        </div>
        <br>
    </label>
    <label for="field-server"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.SERVER') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-server" name="DATA[SERVER]" type="text" class="ui-ctl-element" value="<?= $data['SERVER'] ?>">
        </div>
        <br>
    </label>
    <label for="field-port"><?= Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.PORT') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-port" name="DATA[PORT]" type="number" class="ui-ctl-element" value="<?= $data['PORT'] ?>">
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