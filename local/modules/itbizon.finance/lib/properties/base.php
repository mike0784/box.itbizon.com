<?php


namespace Itbizon\Finance\Properties;

/**
 * Class Base
 * @package Itbizon\Finance\Properties
 */
class Base
{
    /**
     * @return array
     */
    public static function getUserTypeDescription()
    {
        $class = get_called_class();
        return [
            'GetPublicEditHTML' => [$class, 'getPublicEditHTML'],
            'CheckFields' => [$class, 'checkFields'],
            'GetLength' => [$class, 'getLength'],
            'ConvertToDB' => [$class, 'convertToDB'],
            'ConvertFromDB' => [$class, 'convertFromDB'],
            'GetPropertyFieldHtml' => [$class, 'getPropertyFieldHtml'],
            'GetAdminListViewHTML' => [$class, 'getAdminListViewHTML'],
            'GetPublicViewHTML' => [$class, 'getPublicViewHTML'],
            'GetPublicFilterHTML' => [$class, 'getPublicFilterHTML'],
            'PrepareSettings' => [$class, 'prepareSettings'],
            'GetSettingsHTML' => [$class, 'getSettingsHTML'],
            'GetValuePrintable' => [$class, 'getValuePrintable'],
            'AddFilterFields' => [$class, 'addFilterFields'],
        ];
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @return array
     */
    public static function checkFields(array $arProperty, array $value)
    {
        return [];
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @return int
     */
    public static function getLength(array $arProperty, array $value)
    {
        return strlen($value['VALUE']);
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @return array
     */
    public static function convertToDB(array $arProperty, array $value)
    {
        return $value;
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @return array
     */
    public static function convertFromDB(array $arProperty, array $value)
    {
        return $value;
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlName
     * @return string
     */
    public static function getPropertyFieldHtml(array $arProperty, array $value, array $strHTMLControlName)
    {
        return get_called_class() . '::getPropertyFieldHtml()';
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlName
     * @return string
     */
    public static function getAdminListViewHTML(array $arProperty, array $value, array $strHTMLControlName)
    {
        return get_called_class() . '::getAdminListViewHTML()';
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param $strHTMLControlNam
     * @return string
     */
    public static function getPublicViewHTML(array $arProperty, array $value, $strHTMLControlNam)
    {
        return get_called_class() . '::getPublicViewHTML()';
    }

    /**
     * @param array $arProperty
     * @param array $value
     * @param array $strHTMLControlNam
     * @return string
     */
    public static function getPublicEditHTML(array $arProperty, array $value, array $strHTMLControlNam)
    {
        return get_called_class() . '::getPublicEditHTML()';
    }

    /**
     * @param array $arProperty
     * @param array $strHTMLControlName
     * @return string
     */
    public static function getPublicFilterHTML(array $arProperty, array $strHTMLControlName)
    {
        return get_called_class() . '::getPublicFilterHTML()';
    }

    /**
     * @param array $arFields
     * @return array
     */
    public static function prepareSettings(array $arFields)
    {
        return [];
    }

    /**
     * @param array $arProperty
     * @param array $strHTMLControlName
     * @param array $arPropertyFields
     * @return string
     */
    public static function getSettingsHTML(array $arProperty, array $strHTMLControlName, array &$arPropertyFields)
    {
        return get_called_class() . '::getSettingsHTML()';
    }

    /**
     * @param array $property
     * @param array $listValue
     * @param $formatSeparator
     * @return string
     */
    public static function getValuePrintable(array $property, array $listValue, $formatSeparator)
    {
        return implode($formatSeparator, $listValue);
    }
}