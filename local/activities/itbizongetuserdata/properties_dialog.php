<?

use Bitrix\Bizproc\FieldType;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<tr>
    <td align="right" width="40%" valign="top"><span class="adm-required-field">Пользователь</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField(FieldType::USER, 'User', $arCurrentValues['User']) ?>
    </td>
</tr>
