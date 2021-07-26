<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Itbizon\Service\Component\Form\FileField;

Extension::load(['ui.forms', 'itbizon.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceFormField $component */
/**@var FileField $field */
$component = $this->getComponent();
$field = $component->getField();
?>
<?php if ($field): ?>
    <label for="field-<?= $field->getCssName() ?>" class="text-muted m-0">
        <?= $field->getTitle() ?>
        <? if ($field->isRequired()): ?>
            <span class="text-danger">*</span>
        <? endif; ?>
    </label><br>
    <?php if ($field->getTheme() === $field::LINK): ?>
        <label class="ui-ctl ui-ctl-file-link">
            <input id="field-<?= $field->getCssName() ?>"
                   class="ui-ctl-element"
                   type="file"
                   name="DATA[<?= $field->getName() ?>]<?= ($field->isMultiple()) ? '[]' : '' ?>"
                <?= $field->isMultiple() ? 'multiple' : '' ?>
                <?= $field->isDisabled() ? 'disabled' : '' ?>
                <?= $field->isRequired() ? 'required' : '' ?>
            >
            <div class="ui-ctl-label-text"><?= $field->getButtonCaption() ?></div>
        </label>
    <?php elseif ($field->getTheme() === $field::DND): ?>
        <label class="ui-ctl ui-ctl-file-drop ui-ctl-w100">
            <div class="ui-ctl-label-text">
                <span><?= $field->getButtonCaption() ?></span>
                <small>Перетащить с помощью drag'n'drop</small>
            </div>
            <input id="field-<?= $field->getCssName() ?>"
                   class="ui-ctl-element"
                   type="file"
                   name="DATA[<?= $field->getName() ?>]<?= ($field->isMultiple()) ? '[]' : '' ?>"
                <?= $field->isMultiple() ? 'multiple' : '' ?>
                <?= $field->isDisabled() ? 'disabled' : '' ?>
                <?= $field->isRequired() ? 'required' : '' ?>
            >
        </label>
    <?php else: ?>
        <label class="ui-ctl ui-ctl-file-btn">
            <input id="field-<?= $field->getCssName() ?>"
                   class="ui-ctl-element"
                   type="file"
                   name="DATA[<?= $field->getName() ?>]<?= ($field->isMultiple()) ? '[]' : '' ?>"
                <?= $field->isMultiple() ? 'multiple' : '' ?>
                <?= $field->isDisabled() ? 'disabled' : '' ?>
                <?= $field->isRequired() ? 'required' : '' ?>
            >
            <div class="ui-ctl-label-text"><?= $field->getButtonCaption() ?></div>
        </label>
    <?php endif; ?>
    <small id="field-<?= $field->getCssName() ?>-help" class="form-text text-muted"><?= $field->getDescription() ?></small>
<?php endif; ?>