<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Название:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'NAME', $arCurrentValues['NAME'], Array('rows' => 1)) ?>
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

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Станция:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'STATION_ID', $arCurrentValues['STATION_ID'], Array('rows' => 1)) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Материалы:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('text', 'MATERIALS', $arCurrentValues['MATERIALS'], Array('rows' => 1)) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Стоимость:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'VALUE', $arCurrentValues['VALUE'], Array('rows' => 1)) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Выпущен?:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('bool', 'IS_RELEASED', $arCurrentValues['IS_RELEASED'], Array('rows' => 1)) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Коментарий:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('text', 'COMMENT', $arCurrentValues['COMMENT'], Array('rows' => 1)) ?>
    </td>
</tr>