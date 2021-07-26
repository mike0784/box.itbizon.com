<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Itbizon\Service\Component\Form\NumberField;

Extension::load(['ui.forms', 'itbizon.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceFormField $component */
/**@var NumberField $field */
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
    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
        <input id="field-<?= $field->getCssName() ?>"
               class="ui-ctl-element"
               type="number"
               name="DATA[<?= $field->getName() ?>]"
               value="<?= $field->getValue() ?>"
               min="<?= $field->getMin() ?>"
               max="<?= $field->getMax() ?>"
               step="<?= $field->getStep() ?>"
            <?= $field->isDisabled() ? 'disabled' : '' ?>
            <?= $field->isRequired() ? 'required' : '' ?>
        >
    </div>
    <small id="field-<?= $field->getCssName() ?>-help" class="form-text text-muted"><?= $field->getDescription() ?></small>
<?php endif; ?>