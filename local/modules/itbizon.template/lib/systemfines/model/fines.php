<?php

namespace Itbizon\Template\SystemFines\Model;

use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Query\Join;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\Type\Date;

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
                    'default_value' => new Date
                ]
            ),
            new Fields\IntegerField(
                'CREATOR_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'CREATOR',
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
                'TARGET',
                \Bitrix\Main\UserTable::getEntity(),
                Join::on('this.TARGET_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'VALUE',
                [
                    'validation' => function () {
                        return array(
                            function ($value) {
                                if (is_numeric($value) && $value !== 0) {
                                    return true;
                                } else {
                                    return 'Размер штрафа или бонуса должен содержать корректные цыфры';
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

    public static function onBeforeAdd(Event $event)
    {
        $result = new EventResult();
        $data = $event->getParameter("fields");

        if (isset($data['VALUE'])) {
            $val = $data['VALUE'] * 100;
            $result->modifyFields(array('VALUE' => $val));
        }

        return $result;
    }

    public static function onBeforeUpdate(Event $event)
    {
        $result = new EventResult();
        $data = $event->getParameter("fields");
        if (isset($data['VALUE'])) {
            $val = $data['VALUE'] * 100;
            $result->modifyFields(array('VALUE' => $val));
        }

        return $result;
    }
}
