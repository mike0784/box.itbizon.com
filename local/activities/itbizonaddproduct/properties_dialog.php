<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">ID Накладной:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'INVOICE_ID', $arCurrentValues['INVOICE_ID'], Array('rows' => 1)) ?>
    </td>
</tr>


<tr>
    <td align="right" width="40%"><span class="adm-required-field">Кто создал:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('user', 'CREATOR_ID', $arCurrentValues['CREATOR_ID'], Array('rows' => 1)) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Получатель уведомления:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('user', 'TARGET_ID', $arCurrentValues['TARGET_ID'], Array('rows' => 1)) ?>
    </td>
</tr>