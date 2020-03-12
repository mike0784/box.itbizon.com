<?php


namespace Itbizon\Tourism\Fields;

use \Bitrix\Main\UserField\TypeBase;
use \CUserTypeManager;

class TravelPoint extends TypeBase
{
    const FIELD_TYPE_NAME    = '[BizON] Направление поездки';
    const USER_TYPE_ID       = 'ITB_TRAVEL_POINT';

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
     * @param $arUserField
     * @return string
     */
    public static function getDBColumnType($arUserField)
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case 'mysql':
                return 'int(11)';
            case 'oracle':
                return 'number(11)';
            case 'mssql':
                return 'int';
        }
        return 'int';
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    public static function getPublicView($arUserField, $arHtmlControl = [])
    {
        return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    public static function getPublicEdit($arUserField, $arHtmlControl = [])
    {
        return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    public static function getAdminListViewHtml($arUserField, $arHtmlControl)
    {
        return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
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
        return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
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
}