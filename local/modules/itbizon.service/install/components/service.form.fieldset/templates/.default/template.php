<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;

Extension::load(['ui.alerts', 'ui.forms', 'itbizon.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceFormFieldset $component */
$component = $this->getComponent();
?>
<?php foreach($component->getErrors()->getValues() as $error) :?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php
foreach ($component->getFields() as $field) {
    $APPLICATION->IncludeComponent('itbizon:service.form.field', '', ['FIELD' => $field]);
}
?>

