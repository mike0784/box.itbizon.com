<?php

namespace Itbizon\Template\SystemFines\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Join;
use \Bitrix\Main\ORM\Fields;

class FinesTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_fines';
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
            new Fields\StringField(
                'TITLE',
                [
                    'required' => true
                ]
            ),
            new Fields\DateField(
                'DATE_CREATE',
                [
                    'default_value' => new \Bitrix\Main\Type\Date
                ]
            ),
            new Fields\IntegerField(
                'CREATOR_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'CREATOR_ID',
                \Bitrix\Main\UserTable::getEntity(),
                Join::on('this.CREATOR_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'TARGET_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'TARGET_ID',
                \Bitrix\Main\UserTable::getEntity(),
                Join::on('this.TARGET_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'VALUE',
                [
                    'required' => true,
                    'validation' => function () {
                        return array(
                            function ($value) {
                                if ($value !== 0) {
                                    return true;
                                } else {
                                    return 'Сумма не должна равняться 0';
                                }
                            }
                        );
                    }
                ]
            ),
            new Fields\TextField(
                'COMMENT'
            ),
        ];
    }

}
