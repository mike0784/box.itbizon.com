<?php

namespace Itbizon\Basis\Types;

use Bitrix\Main\UserField\TypeBase;
use CUserTypeManager;
use Itbizon\Basis\Utils\WeekDay;

class WeekDayType extends TypeBase
{
    const FIELD_TYPE_NAME = 'Номер недели';
    const USER_TYPE_ID = 'ITB_WEEK_DAY';

    /**
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => static::USER_TYPE_ID,
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => static::FIELD_TYPE_NAME,
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_INT,
            'EDIT_CALLBACK' => [static::class, 'GetPublicEdit'],
            'VIEW_CALLBACK' => [static::class, 'GetPublicView']
        ];
    }

    /**
     * Обязательный метод для определения типа поля таблицы в БД при создании свойства
     * @param $arUserField
     * @return string
     */
    public static function GetDBColumnType($arUserField)
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case "mysql":
                return "int(18)";
            case "oracle":
                return "number(18)";
            case "mssql":
                return "int";
        }
        return "int";
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public static function GetPublicView($arUserField, $arHtmlControl = [])
    {
        $weekNumber = intval($arUserField['VALUE']);
        if ($weekNumber) {
            try {
                return $weekNumber . WeekDay::getWeekString($weekNumber);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return 'Не выбран';
        }
    }

    /**
     * @param $arUserField
     * @param array $arHtmlControl
     * @return string
     * @throws \Exception
     */
    public static function GetPublicEdit($arUserField, $arHtmlControl = [])
    {
        $weeks = WeekDay::getWeekList();
        $value = intval($arUserField['VALUE']);
        $fieldName = isset($arHtmlControl["NAME"]) ? $arHtmlControl["NAME"] : $arUserField['FIELD_NAME'];
        $html = '<select name="' . $fieldName . '" style="width: 100%; height: 35px; border: 1px solid #c6cdd3; color: #545c69;border-radius: 2px;">';
        $html .= '<option value="">[не установлено]</option>';
        foreach ($weeks as $weekNumber => $week) {
            $html .= '<option value="' . $weekNumber . '" ' . (($weekNumber == $value) ? 'selected' : '') .
                ' >' . $weekNumber . WeekDay::getWeekString($weekNumber) . '</option>';
        }
        $html .= '</select>';

        return $html;
    }
}