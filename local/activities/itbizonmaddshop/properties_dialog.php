<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Кто создал:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('user', 'CREATOR_ID', $arCurrentValues['CREATOR_ID'], ['rows' => 1]) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Получатель уведомления:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('user', 'TARGET_ID', $arCurrentValues['TARGET_ID'], ['rows' => 1]) ?>
    </td>
</tr>
