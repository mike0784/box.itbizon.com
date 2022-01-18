<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.select2');
Extension::load(['jquery', 'ui.alerts', 'itbizon.select2', 'itbizon.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceNotifySettings $component */
$component = $this->getComponent();

$filterId = 'some_unique_filter_id';

Toolbar::addFilter([
    'FILTER_ID' => $filterId,
    'FILTER' => $component->getFilter(),
    'ENABLE_LABEL' => true,
]);

foreach ($component->getLeads() as $lead) {
    echo ($lead['ID'].' - '.$lead['TITLE'].' (создан '.$lead['DATE_CREATE']->format('d.m.Y').') источник: '.$lead['CRM_LEAD_SOURCE_BY_NAME'].'<br>');
}

?>
<?php $APPLICATION->SetTitle(Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.PAGE_TITLE')); ?>
<?php foreach ($component->getErrors() as $error) : ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>