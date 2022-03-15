<?php
/*Параметры модуля*/
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

defined('B_PROLOG_INCLUDED') || die();

global $APPLICATION, $USER;

Loc::loadMessages(__FILE__);

//Получение id модуля
$moduleId = $_GET['mid'];
//Подключение модуля
Loader::includeModule($moduleId);

$tabs = [
    [
        'DIV' => 'general',
        'TAB' => Loc::getMessage('ITB_MIKE_MODULE_OPTION_TAB_GENERAL_NAME'),    //
        'TITLE' => Loc::getMessage('ITB_MIKE_MODULE_OPTION_TAB_GENERAL_DESCRIPTION'),
    ],
    [
        'DIV' => 'smtp',
        'TAB' => Loc::getMessage('ITB_MIKE_MODULE_OPTION_TAB_SMTP_NAME'),
        'TITLE' => Loc::getMessage('ITB_MIKE_MODULE_OPTION_TAB_SMTP_DESCRIPTION'),
    ],
];

$options = [
    'general' => [
        Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_OPTIONS_MODULE'),   //$MESS ['ITB_MIKE_MODULE_OPTION_GENERAL_OPTIONS_MODULE'] = "Настройки параметров модуля";
        ['general_save_src', Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_SAVE_SRC'), 'N', ['checkbox']],
        ['general_save_attachments', Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_SAVE_ATTACHMENTS'), 'N', ['checkbox']],
        ['general_timeout', Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_TIMEOUT'), '', ['text', '40']],
        ['general_checkspam', Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_CHECKSPAM'), 'N', ['checkbox']],
        ['general_log_save', Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_LOG_SAVE'), '', ['text', '40']],
        ['general_sync_old_limit', Loc::getMessage('ITB_MIKE_MODULE_OPTION_GENERAL_SYNC_OLD_LIMIT'), '', ['text', '40']],
    ],
    'smtp' => [
        Loc::getMessage('ITB_MIKE_MODULE_OPTION_SMTP_OPTIONS_MODULE'),
        ['smtp_php_line', Loc::getMessage('ITB_MIKE_MODULE_OPTION_SMTP_PHP_LINE'), '', ['text', '40']],
        ['smtp_smtp_status', Loc::getMessage('ITB_MIKE_MODULE_OPTION_SMTP_STATUS'), '', ['text', '40']],
        ['smtp_start_smtp', Loc::getMessage('ITB_MIKE_MODULE_OPTION_SMTP_START_SMTP'), '', ['text', '40']],
    ],
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
