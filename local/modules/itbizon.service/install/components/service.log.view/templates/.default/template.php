<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load(['ui.alerts']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceLogView $component */
$component = $this->getComponent();
?>
<?php $APPLICATION->SetTitle(str_replace('#FILE_NAME#', $component->getHelper()->getVariable('FILE_NAME'), Loc::getMessage('ITB_SERVICE.LOG.VIEW.PAGE_TITLE'))); ?>
<?php if ($component->getError()): ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $component->getError() ?></span>
    </div>
<?php endif; ?>
<?php if($component->getLog()): ?>
    <pre class="itb-log-content"><?= $component->getLog() ?></pre>
<?php endif; ?>
<? $APPLICATION->IncludeComponent('bitrix:ui.button.panel', '', [
    'BUTTONS' => [
        'cancel' => $component->getHelper()->getUrl('list')
    ]
]); ?>
