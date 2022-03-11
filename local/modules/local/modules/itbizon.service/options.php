<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Service\Utils;

defined('B_PROLOG_INCLUDED') || die;

global $APPLICATION, $USER;

Loc::loadMessages(__FILE__);
$moduleId = $_GET['mid'];
if (!$USER->IsAdmin())
    return;

Loader::includeModule($moduleId);
$tabs = [
    [
        'DIV' => 'general',
        'TAB' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_GENERAL_NAME'),
        'TITLE' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_GENERAL_DESCRIPTION')
    ],
    [
        'DIV' => 'monitor',
        'TAB' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_MONITOR_NAME'),
        'TITLE' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_MONITOR_DESCRIPTION')
    ],
    [
        'DIV' => 'mail',
        'TAB' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_MAIL_NAME'),
        'TITLE' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_MAIL_DESCRIPTION')
    ],
    [
        'DIV' => 'check',
        'TAB' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_CHECK_NAME'),
        'TITLE' => Loc::getMessage('ITB_SERV_MODULE_OPTION_TAB_CHECK_DESCRIPTION')
    ],
];

$messages = [];
$checkNotes = [];
$checkEngine = Utils::checkEngine($messages);
foreach($messages as $message) {
    $checkNotes[] = ['note' => $message];
}

$options = [
    'general' => [
        Loc::getMessage('ITB_SERV_MODULE_OPTION_GENERAL_OPTIONS_STATISTIC'),
        ['general_send_statistic', Loc::getMessage('ITB_SERV_MODULE_OPTION_GENERAL_SEND_STATISTIC'), 'N', ['checkbox']],
        ['general_send_statistic_key', Loc::getMessage('ITB_SERV_MODULE_OPTION_GENERAL_SEND_STATISTIC_KEY'), '', ['text', '40']],
        ['note' => Loc::getMessage('ITB_SERV_MODULE_OPTION_GENERAL_NOTE_STATISTIC')]
    ],
    'monitor' => [
        Loc::getMessage('ITB_SERV_MODULE_OPTION_MONITOR_OPTIONS_EMAIL_NOTIFY'),
        ['monitor_notify_email_active', Loc::getMessage('ITB_SERV_MODULE_OPTION_MONITOR_NOTIFY_EMAIL_ACTIVE'), 'N', ['checkbox']],
        ['monitor_notify_email_list', Loc::getMessage('ITB_SERV_MODULE_OPTION_MONITOR_NOTIFY_EMAIL_LIST'), '', ['text', '40']],
        Loc::getMessage('ITB_SERV_MODULE_OPTION_MONITOR_OPTIONS_THRESHOLD'),
        ['monitor_threshold_hdd',  Loc::getMessage('ITB_SERV_MODULE_OPTION_MONITOR_THRESHOLD_HDD'), '1024', ['text', '10']],
    ],
    'mail' => [
        ['mail_send_active', Loc::getMessage('ITB_SERV_MODULE_OPTION_MAIL_SEND_ACTIVE'), 'N', ['checkbox']],
        ['mail_send_via_default', Loc::getMessage('ITB_SERV_MODULE_OPTION_MAIL_SEND_VIA_DEFAULT'), 'Y', ['checkbox']],
    ],
    'check' => $checkNotes,
];

if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($moduleId, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();
?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <? foreach ($options as $tab => $option) : ?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($moduleId, $options[$tab]); ?>
    <? endforeach; ?>
    <? $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]); ?>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>