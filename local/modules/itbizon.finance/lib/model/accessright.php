<?php

namespace Itbizon\Finance\Model;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use Itbizon\Finance\AccessRight;

Loc::loadMessages(__FILE__);

/**
 * Class AccessRightTable
 * @package Itbizon\Finance\Model
 */
class AccessRightTable extends DataManager
{
    const ENTITY_ID_ALL = 0;

    /**
     * enum ENTITY_TYPE_ID
     */
    const ENTITY_VAULT       = 1;
    const ENTITY_VAULT_GROUP = 2;
    const ENTITY_OPERATION   = 10;
    const ENTITY_CATEGORY    = 20;
    const ENTITY_PERIOD      = 30;
    const ENTITY_REQUEST     = 40;
    const ENTITY_REQUEST_TEMPLATE = 41;
    const ENTITY_CATEGORY_REPORT = 50;
    const ENTITY_STOCK       = 60;
    const ENTITY_CONFIG      = 100;

    /**
     * enum USER_TYPE
     */
    const USER = 1;
    const DEPARTMENT = 2;

    /**
     * enum ACTION
     */
    const ACTION_REQUEST_INCOME = 1;
    const ACTION_VIEW   = 10;
    const ACTION_ADD    = 20;
    const ACTION_EDIT   = 30;
    const ACTION_DELETE = 40;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.ACCESS_RIGHT_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_access_right';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_ACCESS_RIGHT';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return AccessRight::class;
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.ACCESS_RIGHT.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\IntegerField(
                'ENTITY_TYPE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE_ID'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'ENTITY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.ACCESS_RIGHT.ENTITY_ID'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'ACTION',
                [
                    'title' => Loc::getMessage('ITB_FIN.ACCESS_RIGHT.ACTION'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'USER_TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.ACCESS_RIGHT.USER_TYPE'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'USER_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.ACCESS_RIGHT.USER_ID'),
                    'required' => true,
                ]
            ),
        ];
    }

    /**
     * @param Event $event
     * @return Entity\EventResult
     */
    public static function onBeforeAdd(Event $event)
    {
        $result = new Entity\EventResult();
        $data = $event->getParameter("fields");

        try {
            $accessRight = self::getList([
                'filter' => [
                    '=ENTITY_TYPE_ID' => $data['ENTITY_TYPE_ID'],
                    '=ENTITY_ID' => $data['ENTITY_ID'],
                    '=ACTION' => $data['ACTION'],
                    '=USER_TYPE' => $data['USER_TYPE'],
                    '=USER_ID' => $data['USER_ID'],
                ]
            ])->fetch();

            if($accessRight)
                throw new Exception(new EntityError(Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ERROR_DUPLICATE_ACCESS_RIGHT")));

        } catch (\Exception $e) {
            $result->addError(new EntityError($e->getMessage()));
        }

        return $result;
    }

    /**
     * @param int $actionId
     * @return array|string
     */
    public static function getActions(int $actionId = null)
    {

        $actions = [
            self::ACTION_REQUEST_INCOME => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ACTIONS.REQUEST_INCOME"),
            self::ACTION_VIEW => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ACTIONS.VIEW"),
            self::ACTION_ADD => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ACTIONS.ADD"),
            self::ACTION_EDIT => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ACTIONS.EDIT"),
            self::ACTION_DELETE => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ACTIONS.DELETE"),
        ];

        if(!is_null($actionId))
            return isset($actions[$actionId]) ? $actions[$actionId] : Loc::getMessage('ITB_FIN.ACCESS_RIGHT.ACTIONS.UNKNOWN');
        else
            return $actions;
    }

    /**
     * @param int $typeId
     * @return array|string
     */
    public static function getUserTypes(int $typeId = null)
    {
        $types = [
            self::USER => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.USER_TYPE.USER"),
            self::DEPARTMENT => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.USER_TYPE.DEPARTMENT"),
        ];

        if(!is_null($typeId))
            return isset($types[$typeId]) ? $types[$typeId] : Loc::getMessage('ITB_FIN.ACCESS_RIGHT.USER_TYPE.UNKNOWN');
        else
            return $types;
    }

    /**
     * @param int $typeId
     * @return array|string
     */
    public static function getEntityTypes(int $typeId = null)
    {
        $types = [
            self::ENTITY_VAULT => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.VAULT"),
            self::ENTITY_OPERATION => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.OPERATION"),
            self::ENTITY_CATEGORY => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.CATEGORY"),
            self::ENTITY_PERIOD => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.PERIOD"),
            self::ENTITY_REQUEST => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.REQUEST"),
            self::ENTITY_REQUEST_TEMPLATE => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.REQUEST_TEMPLATE"),
            self::ENTITY_CATEGORY_REPORT => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.CATEGORY_REPORT"),
            self::ENTITY_STOCK => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.STOCK"),
            self::ENTITY_CONFIG => Loc::getMessage("ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.CONFIG"),
        ];

        if(!is_null($typeId))
            return isset($types[$typeId]) ? $types[$typeId] : Loc::getMessage('ITB_FIN.ACCESS_RIGHT.ENTITY_TYPE.UNKNOWN');
        else
            return $types;
    }
}