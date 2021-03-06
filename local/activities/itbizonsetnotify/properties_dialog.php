<?
use Bitrix\Bizproc\Activity\PropertiesDialog;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var PropertiesDialog $dialog */
foreach ($dialog->getMap() as $fieldId => $field):
    ?>
    <tr>
        <td align="right" width="40%"><?=htmlspecialcharsbx($field['Name'])?>:</td>
        <td width="60%">
            <? $filedType = $dialog->getFieldTypeObject($field);

            echo $filedType->renderControl([
                'Form' => $dialog->getFormName(),
                'Field' => $field['FieldName']
            ], $dialog->getCurrentValue($field['FieldName']), true, 0);
            ?>
        </td>
    </tr>
<?endforeach;?>

