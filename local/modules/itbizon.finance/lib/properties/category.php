<?php


namespace Itbizon\Finance\Properties;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Itbizon\Finance\Model\OperationCategoryTable;

Loc::loadMessages(__FILE__);

/**
 * Class Category
 * @package Itbizon\Finance\Properties
 */
class Category extends Base
{
    /**
     * @return array
     */
    public static function getUserTypeDescription()
    {
        $data = [
            'PROPERTY_TYPE' => PropertyTable::TYPE_NUMBER,
            'USER_TYPE' => 'itb_finance_category',
            'DESCRIPTION' => Loc::getMessage('ITB_FIN.PROP_CATEGORY.NAME'),
        ];
        return array_merge($data, parent::getUserTypeDescription());
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @return bool
     */
    public static function getLength(array $arProperty, array $value)
    {
        return (intval($value['VALUE']) > 0);
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlName
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPropertyFieldHtml(array $arProperty, array $value, array $strHTMLControlName)
    {
        $items[] = '<option value="">' . Loc::getMessage('ITB_FIN.PROP_CATEGORY.NOT_SELECT') . '</option>';
        $filter = ['LOGIC' => 'OR'];
        if ($arProperty['USER_TYPE_SETTINGS']['ALLOW_INCOME'] == 'Y')
            $filter['ALLOW_INCOME'] = 'Y';
        if ($arProperty['USER_TYPE_SETTINGS']['ALLOW_OUTGO'] == 'Y')
            $filter['ALLOW_OUTGO'] = 'Y';
        if ($arProperty['USER_TYPE_SETTINGS']['ALLOW_TRANSFER'] == 'Y')
            $filter['ALLOW_TRANSFER'] = 'Y';
        $result = OperationCategoryTable::getList([
            'select' => ['*'],
            'filter' => $filter,
            'order' => ['NAME' => 'ASC'],
        ]);
        while ($item = $result->fetchObject()) {
            $selected = (($value['VALUE'] == $item->getId()) ? 'selected' : '');
            $items[] = '<option value="' . $item->getId() . '" ' . $selected . '>' . htmlspecialchars($item->getName()) . '</option>';
        }
        return '<select name="' . $strHTMLControlName['VALUE'] . '">' . implode('', $items) . '</select>';
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlName
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getAdminListViewHTML(array $arProperty, array $value, array $strHTMLControlName)
    {
        return self::getPublicViewHTML($arProperty, $value, $strHTMLControlName);
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlNam
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPublicViewHTML(array $arProperty, array $value, $strHTMLControlNam)
    {
        $id = intval($value['VALUE']);
        if ($id > 0) {
            $item = OperationCategoryTable::getById($id)->fetchObject();
            if ($item)
                return '<a href="/finance/category/edit/' . $item->getId() . '/">' . htmlspecialchars($item->getName()) . '</a>';
        }
        return Loc::getMessage('ITB_FIN.PROP_CATEGORY.NOT_SELECT');
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlNam
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPublicEditHTML(array $arProperty, array $value, array $strHTMLControlNam)
    {
        return self::getPropertyFieldHtml($arProperty, $value, $strHTMLControlNam);
    }

    /**
     * @param array $arProperty
     * @param array $strHTMLControlName
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPublicFilterHTML(array $arProperty, array $strHTMLControlName)
    {
        $name = $strHTMLControlName['VALUE'];
        $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : '';

        return self::getPropertyFieldHtml($arProperty, ['VALUE' => $value], $strHTMLControlName);
    }

    /**
     * @param array $arFields
     * @return array
     */
    public static function prepareSettings(array $arFields)
    {
        $allowIncome = ($arFields['USER_TYPE_SETTINGS']['ALLOW_INCOME'] == 'Y') ? 'Y' : 'N';
        $allowOutgo = ($arFields['USER_TYPE_SETTINGS']['ALLOW_OUTGO'] == 'Y') ? 'Y' : 'N';
        $allowTransfer = ($arFields['USER_TYPE_SETTINGS']['ALLOW_TRANSFER'] == 'Y') ? 'Y' : 'N';
        return [
            'ALLOW_INCOME' => $allowIncome,
            'ALLOW_OUTGO' => $allowOutgo,
            'ALLOW_TRANSFER' => $allowTransfer
        ];
    }

    /**
     * @param array $arProperty
     * @param array $strHTMLControlName
     * @param array $arPropertyFields
     * @return string
     */
    public static function getSettingsHTML(array $arProperty, array $strHTMLControlName, array &$arPropertyFields)
    {
        $arPropertyFields = array(
            'HIDE' => ['ROW_COUNT', 'COL_COUNT'],
            'SET' => ['FILTRABLE' => 'N'],
            'USER_TYPE_SETTINGS_TITLE' => Loc::getMessage('ITB_FIN.PROP_CATEGORY.SETTINGS.TITLE')
        );
        $html = '';
        $settings = [
            'ALLOW_INCOME' => Loc::getMessage('ITB_FIN.PROP_CATEGORY.SETTINGS.ALLOW_INCOME'),
            'ALLOW_OUTGO' => Loc::getMessage('ITB_FIN.PROP_CATEGORY.SETTINGS.ALLOW_OUTGO'),
            'ALLOW_TRANSFER' => Loc::getMessage('ITB_FIN.PROP_CATEGORY.SETTINGS.ALLOW_TRANSFER'),
        ];
        foreach ($settings as $id => $name) {
            $html .= '<tr>
                <td>' . $name . '</td>
                <td>
                    <select name="' . $strHTMLControlName['NAME'] . '[' . $id . ']">
                        <option value="Y" ' . (($arProperty['USER_TYPE_SETTINGS'][$id] == 'Y') ? 'selected' : '') . '>' . Loc::getMessage('ITB_FIN.PROP_CATEGORY.SETTINGS.YES') . '</option>
                        <option value="N" ' . (($arProperty['USER_TYPE_SETTINGS'][$id] == 'N') ? 'selected' : '') . '>' . Loc::getMessage('ITB_FIN.PROP_CATEGORY.SETTINGS.NO') . '</option>
                    </select>
                </td>
            </tr>';
        }
        return $html;
    }

    /**
     * @param array $property
     * @param array $listValue
     * @param $formatSeparator
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getValuePrintable(array $property, array $listValue, $formatSeparator)
    {
        $items = [];
        if (!empty($listValue)) {
            $result = OperationCategoryTable::getList([
                'select' => ['ID', 'NAME'],
                'filter' => [
                    '=ID' => $listValue
                ]
            ]);
            while ($item = $result->fetchObject())
                $items[] = $item->getName() . '[' . $item->getId() . ']';
        }
        return implode($formatSeparator, $items);
    }
}