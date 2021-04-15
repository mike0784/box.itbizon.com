<?php


namespace Itbizon\Finance\Properties;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Itbizon\Finance\Model\VaultTable;

Loc::loadMessages(__FILE__);

/**
 * Class Vault
 * @package Itbizon\Finance\Properties
 */
class Vault extends Base
{
    /**
     * @return array
     */
    public static function getUserTypeDescription()
    {
        $data = [
            'PROPERTY_TYPE' => PropertyTable::TYPE_NUMBER,
            'USER_TYPE' => 'itb_finance_vault',
            'DESCRIPTION' => Loc::getMessage('ITB_FIN.PROP_VAULT.NAME'),
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
        $items[] = '<option value="">' . Loc::getMessage('ITB_FIN.PROP_VAULT.NOT_SELECT') . '</option>';
        $result = VaultTable::getList([
            'select' => ['*'],
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
            $item = VaultTable::getById($id)->fetchObject();
            if ($item)
                return '<a href="' . $item->getUrl() . '">' . htmlspecialchars($item->getName()) . '</a>';
        }
        return Loc::getMessage('ITB_FIN.PROP_VAULT.NOT_SELECT');
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
     * @param array $arProperty
     * @param array $strHTMLControlName
     * @param array $arPropertyFields
     * @return string
     */
    public static function getSettingsHTML(array $arProperty, array $strHTMLControlName, array &$arPropertyFields)
    {
        return '';
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
            $result = VaultTable::getList([
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