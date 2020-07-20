<?php

namespace Bizon\Main\Department\Model;

use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\ORM\Fields\Relations\OneToMany;
use \Bitrix\Main\ORM\Objectify\EntityObject;
use \Bitrix\Main\Type\Date;
use \Bizon\Main\Department\DepartmentHistory;

class DepartmentHistoryTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_department_history';
    }
    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return DepartmentHistory::class;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'primary'      => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\DateField(
                'CHANGE_DATE',
                [
                    'required' => true,
                    'default_value' => new Date
                ]
            ),
            new Fields\IntegerField(
                'DEPARTMENT_ID',
                [
                    'required' => true,
                ]
            ),
            (new OneToMany('STRUCTURE_HISTORIES', StructureHistoryTable::class,
                'DEPARTMENT_HISTORY'))
        ];
    }
}