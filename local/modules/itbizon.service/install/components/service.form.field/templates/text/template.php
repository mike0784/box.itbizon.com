<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Itbizon\Service\Component\Form\StringField;

Extension::load(['ui.forms', 'itbizon.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceFormField $component */
/**@var StringField $field */
$component = $this->getComponent();
$field = $component->getField();
?>
<?php if ($field): ?>
    <label for="field-<?= $field->getCssName() ?>" class="text-muted m-0">
        <?= $field->getTitle() ?>
        <? if ($field->isRequired()): ?>
            <span class="text-danger">*</span>
        <? endif; ?>
    </label>
    <div class="ui-ctl ui-ctl-textarea ui-ctl-w100">
        <textarea id="field-<?= $field->getCssName() ?>"
                  class="ui-ctl-element"
                  name="DATA[<?= $field->getName() ?>]"
                  <?= $field->isDisabled() ? 'disabled' : '' ?>
            <?= $field->isRequired() ? 'required' : '' ?>
        ><?= $field->getValue() ?></textarea>
    </div>
    <small id="field-<?= $field->getCssName() ?>-help" class="form-text text-muted"><?= $field->getDescription() ?></small>
<?php endif; ?>