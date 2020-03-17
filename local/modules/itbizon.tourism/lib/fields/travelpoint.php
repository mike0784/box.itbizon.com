<?php


namespace Itbizon\Tourism\Fields;

use \Bitrix\Main\Loader;
use \Bitrix\Main\UI\Extension;
use \Bitrix\Main\Page\Asset;
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
     * @return array
     */
    public static function getPointsTree()
    {
        //TODO DEMO
        return [
            1 => [
                'ID' => 1,
                'NAME' => 'Юго-восточная азия',
                'CHILD' => [
                    1 => [
                        'ID' => 1,
                        'NAME' => 'Тайланд',
                        'CHILD' => [
                            1 => [
                                'ID' => 1,
                                'NAME' => 'Пхукет',
                            ],
                            2 => [
                                'ID' => 2,
                                'NAME' => 'Потайя',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $cityId
     * @return array
     */
    public static function getChainByCity($cityId)
    {
        $tree = self::getPointsTree();
        $regionId = 0;
        $countryId = 0;
        foreach($tree as $rId => $region)
        {
            foreach($region['CHILD'] as $cId => $country)
            {
                if(isset($country['CHILD'][$cityId]))
                {
                    $regionId  = $rId;
                    $countryId = $cId;
                    break;
                }
            }
        }
        return [$regionId, $countryId];
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    public static function getPublicView($arUserField, $arHtmlControl = [])
    {
        //return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
        $fieldValue = self::getFieldValue($arUserField, $arHtmlControl);
        $tree = self::getPointsTree();
        $html = '';
        foreach($fieldValue as $cityId)
        {
            $cityId = intval($cityId);
            list($regionId, $countryId) = self::getChainByCity($cityId);

            $region  = (isset($tree[$regionId])) ? $tree[$regionId]['NAME'] : '';
            $country = (isset($tree[$regionId]['CHILD'][$countryId])) ? $tree[$regionId]['CHILD'][$countryId]['NAME'] : '';
            $city    = (isset($tree[$regionId]['CHILD'][$countryId]['CHILD'][$cityId])) ? $tree[$regionId]['CHILD'][$countryId]['CHILD'][$cityId]['NAME'] : '';
            $html .= '<p>'.implode(' > ', [$region, $country, $city]).'</p>';
        }
        return $html;
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    public static function getPublicEdit($arUserField, $arHtmlControl = [])
    {
        Extension::load('ui.bootstrap4');
        Asset::getInstance()->addJs('/local/modules/itbizon.tourism/js/travelpoint.js');
        $fieldName  = self::getFieldName($arUserField, $arHtmlControl);
        $fieldValue = self::getFieldValue($arUserField, $arHtmlControl);
        $tree = self::getPointsTree();
        $html = '';
        foreach($fieldValue as $cityId)
        {
            $cityId = intval($cityId);
            list($regionId, $countryId) = self::getChainByCity($cityId);

            $html .= '<div data-tree="'.htmlspecialchars(json_encode($tree)).'" class="form-group row travel-point-selector">
            <div class="col">
            <label>Регион</label>
            <select class="form-control region-selector">
                <option value=""></option>';
            foreach($tree as $rId => $region)
                $html .= '<option value="'.$rId.'" '.(($rId == $regionId) ? 'selected' : '').' >'.$region['NAME'].'</option>';
            $html .= '</select>
            </div>
            <div class="col">
            <label>Страна</label>
            <select class="form-control country-selector">
                <option value=""></option>';
            if(isset($tree[$regionId]))
            {
                foreach($tree[$regionId]['CHILD'] as $cId => $country)
                    $html .= '<option value="'.$cId.'" '.(($cId == $countryId) ? 'selected' : '').' >'.$country['NAME'].'</option>';
            }
            $html .= '</select>
            </div>
            <div class="col">
            <label>Город</label>
            <select name="'.$fieldName.'" class="form-control city-selector">
                <option value=""></option>';
            if(isset($tree[$regionId]))
            {
                if(isset($tree[$regionId]['CHILD'][$countryId]))
                {
                    foreach($tree[$regionId]['CHILD'][$countryId]['CHILD'] as $cId => $city)
                        $html .= '<option value="'.$cId.'" '.(($cId == $cityId) ? 'selected' : '').' >'.$city['NAME'].'</option>';
                }
            }
            $html .= '</select>
            </div>
            </div>';
        }
        return $html;
        //return __METHOD__.'<pre>'.print_r($arUserField, true).' '.print_r($arHtmlControl, true).'</pre>';
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

    /**
     * @param $userField
     * @return string
     */
    public static function getPublicText($userField)
    {
        return self::getPublicView($userField);
    }
}