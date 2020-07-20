<?php

namespace Bizon\Main\Department\Model;

use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\ORM\Fields\Relations\Reference;
use \Bitrix\Main\ORM\Objectify\EntityObject;
use \Bitrix\Main\ORM\Query\Join;
use \Bizon\Main\Department\StructureHistory;

class StructureHistoryTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_structure_history';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return StructureHistory::class;
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
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\IntegerField(
                'DEPARTMENT_HISTORY_ID',
                [
                    'required' => true,
                ]
            ),
            (new Reference(
                'DEPARTMENT_HISTORY',
                DepartmentHistoryTable::class,
                Join::on('this.DEPARTMENT_HISTORY_ID', 'ref.ID')
            ))->configureJoinType('inner'),

            new Fields\IntegerField(
                'USER_ID',
                [
                    'required' => true,
                ]
            ),
            new Fields\BooleanField(
                'IS_CHIEF',
                [
                    'required' => true,
                    'values' => ['N', 'Y']
                ]
            ),
        ];
    }
}