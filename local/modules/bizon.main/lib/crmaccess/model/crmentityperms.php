<?php

namespace Bizon\Main\CrmAccess\Model;

use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;
use \Bitrix\Main\ORM\Data\DataManager;

class CrmEntityPermsTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_crm_entity_perms';
    }
    
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true
                ]),
            
            new StringField(
                'ENTITY',
                [
                    'required' => true
                ]
            ),
            new IntegerField(
                'ENTITY_ID',
                [
                    'required' => true,
                ]
            ),
            new StringField(
                'ATTR',
                [
                    'required' => true
                ]
            ),
        ];
    }
}