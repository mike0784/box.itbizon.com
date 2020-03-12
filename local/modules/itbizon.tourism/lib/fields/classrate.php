<?php


namespace Itbizon\Tourism\Fields;

use \Bitrix\Main\UserField\TypeBase;
use \CUserTypeManager;

class ClassRate extends TypeBase
{
    const FIELD_TYPE_NAME    = '[BizON] Класс';
    const USER_TYPE_ID       = 'ITB_CLASS_RATE';

    /**
     * @return array
     */
    public static function getUserTypeDescription()
    {
        return [
            'USER_TYPE_ID'  => static::USER_TYPE_ID,
            'CLASS_NAME'    => __CLASS__,
            'DESCRIPTION'   => static::FIELD_TYPE_NAME,
            'BASE_TYPE'     => CUserTypeManager::BASE_TYPE_INT,
            'EDIT_CALLBACK' => [static::class, 'getPublicEdit'],
            'VIEW_CALLBACK' => [static::class, 'getPublicView']
        ];
    }

    /**
     * @return array
     */
    public static function getValueList()
    {
        return [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D'
        ];
    }

    /**
     * @param $arUserField
     * @return string
     */
    public static function getDBColumnType($arUserField)
    {
        return 'int';
    }

    /**
     * @param $arUserField
     * @return string
     */
    public static function getPublicText($arUserField)
    {
        $fieldValue = self::getFieldValue($arUserField);
        foreach($fieldValue as &$value)
        {
            $value = self::getValueList()[$value];
        }
        return '['.implode('.', $fieldValue).']';
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    public static function getPublicView($arUserField, $arHtmlControl = [])
    {
        $fieldValue = self::getFieldValue($arUserField, $arHtmlControl);
        foreach($fieldValue as &$value)
        {
            $value = self::getValueList()[$value];
        }
        return '['.implode('.', $fieldValue).']';
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    public static function getPublicEdit($arUserField, $arHtmlControl = [])
    {
        $html = '';
        $fieldName  = self::getFieldName($arUserField, $arHtmlControl);
        $fieldValue = self::getFieldValue($arUserField, $arHtmlControl);
        $valueList  = self::getValueList();
        $html .= '<select style="width: 100%; padding: 5px;" name="'.$fieldName.'">';
        $html .= '<option value=""></option>';
        foreach($valueList as $value => $name)
        {
            $html .= '<option '.(in_array($value, $fieldValue) ? 'selected' : '').' value="'.$value.'">'.$name.'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    public static function getAdminListViewHtml($arUserField, $arHtmlControl)
    {
        $fieldValue = self::normalizeFieldValue($arHtmlControl['VALUE']);
        foreach($fieldValue as &$value)
        {
            $value = self::getValueList()[$value];
        }
        return '['.implode('.', $fieldValue).']';
        //return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    public static function getAdminListEditHtml($arUserField, $arHtmlControl)
    {
        return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    public static function getEditFormHtml($arUserField, $arHtmlControl)
    {
        return self::getPublicEdit($arUserField, $arHtmlControl);
        //return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    public static function getFilterHtml($arUserField, $arHtmlControl)
    {
        return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return array
     */
    public static function getFilterData($arUserField, $arHtmlControl)
    {
        return [
            'id'         => $arHtmlControl['ID'],
            'name'       => $arHtmlControl['NAME'],
            'type'       => 'list',
            'items'      => self::getValueList(),
            'filterable' => ''
        ];
    }
}