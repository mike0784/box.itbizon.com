use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Bizproc\FieldType;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var PropertiesDialog $dialog */
foreach ($dialog->getMap() as $fieldId => $field):
    ?>
    <tr>
        <td align="right" width="40%"><?=htmlspecialcharsbx($field['Name'])?>:</td>
        <td width="60%">
            <?php $filedType = $dialog->getFieldTypeObject($field);

			if($field['Type']==FieldType::USER)
                echo CBPDocument::ShowParameterField(FieldType::USER, 'authorUser',  $arCurrentValues['authorUser']);
			else echo $filedType->renderControl([
                'Form' => $dialog->getFormName(),
                'Field' => $field['FieldName']
            ], $dialog->getCurrentValue($field['FieldName']), true, 0);
            ?>
        </td>

    </tr>
<?php endforeach;?>