<?php

namespace Bizon\Main;

use Bitrix\Main\UserFieldTable;

class UserFieldHelper
{
    /**
     * @param array $userFieldIds Array view: [<fieldId>, <fieldId>...]
     * @return array
     */
    protected static function getUtsNames(array $userFieldIds)
    {
        global $DB;
        $userFieldIdsStr = implode(', ', $userFieldIds);
        $userFieldNames = [];
        $list = $DB->Query(
            'SELECT USER_FIELD_ID as ID, EDIT_FORM_LABEL as NAME FROM b_user_field_lang WHERE USER_FIELD_ID IN (' . $userFieldIdsStr . ') and LANGUAGE_ID = "ru"', false);
        while ($item = $list->Fetch())
            $userFieldNames[$item['ID']] = $item['NAME'];
        return $userFieldNames;
    }
    
    /**
     * @param $entityId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getUtsList($entityId)
    {
        $utsList = [];
        $list = UserFieldTable::getList([
            'filter' => [
                '=ENTITY_ID' => $entityId,
            ],
            'select' => [
                'ID',
                'FIELD_NAME',
                'TYPE' => 'USER_TYPE_ID',
            ],
        ]);
        while ($item = $list->fetch())
        {
            $item['TYPE'] = $item['TYPE'] == 'boolean' ? 'bool' : $item['TYPE'];
            $item['TYPE'] = $item['TYPE'] == 'date' ? 'datetime' : $item['TYPE'];
            $item['TYPE'] = $item['TYPE'] == 'employee' ? 'user' : $item['TYPE'];
    
            $utsList[$item['ID']] = $item;
        }
        
        $utsNames = self::getUtsNames(array_keys($utsList));
        foreach ($utsList as $key => $item)
            $utsList[$key]['NAME'] = $utsNames[$key];
        
        return $utsList;
    }
}