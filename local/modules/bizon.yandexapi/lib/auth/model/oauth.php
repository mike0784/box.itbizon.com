<?php

namespace Bizon\Yandexapi\Auth\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\Date;

/**
 * Авторизационные данные приложения Яндекс.OAuth
 * @package Bizon\Yandexapi\Auth\Model
 */
class OAuthTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_yandexapi_oauth';
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
                'APP_NAME',
                [
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'APP_ID',
                [
                    'required' => true
                ]
            ),
            new Fields\StringField(
                'APP_PASS',
                [
                    'required' => true
                ]
            ),
//            new Fields\StringField(
//                'APP_CALLBACK',
//                [
//                    'required' => true
//                ]
//            ),
            new Fields\ArrayField(
                'APP_SCOPE',
                [
                    'required' => true
                ]
            ),
            new Fields\IntegerField(
                'PDD_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'PDD',
                PDDTable::getEntity(),
                Join::on('this.PDD_ID', 'ref.ID')
            ))->configureJoinType('left'),

            new Fields\DateField(
                'DATE_CREATE',
                [
                    'default_value' => new Date
                ]
            ),
        ];
    }
}
