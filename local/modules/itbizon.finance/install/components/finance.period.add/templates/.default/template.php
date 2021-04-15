<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBFinancePeriodAdd $component */

$component = $this->getComponent();
?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.TEMPLATE.TITLE')); ?>
<?php foreach($component->getErrors()->getValues() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<form method="post">
    <label for="field-from"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.TEMPLATE.FIELD.DATE_BEGIN') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-from" class="form-control" type="datetime-local" name="DATA[FROM]" value="<?= $component->getBegin()->format('Y-m-d\TH:i') ?>"
                <?= ($component->isFixed() ? 'readonly' : '') ?> required>
        </div>
        <br>
    </label>
    <label for="field-to"><?= Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.TEMPLATE.FIELD.DATE_END') ?>
        <div class="ui-ctl ui-ctl-textbox">
            <input id="field-to" class="form-control" type="datetime-local" name="DATA[TO]" value="<?= $component->getEnd()->format('Y-m-d\TH:i') ?>" readonly>
        </div>
        <br>
    </label>
    <? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
        'BUTTONS' => [
            'save',
            'cancel' => $component->getRoute()->getUrl('list')
        ]
    ]); ?>
</form>
