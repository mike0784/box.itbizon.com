<?php

namespace Itbizon\Finance;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use \Itbizon\Finance\Model\AccessRightTable;
use Itbizon\Finance\Model\EO_AccessRight;

/**
 * Class AccessRight
 * @package Itbizon\Finance
 */
class AccessRight extends EO_AccessRight
{
    protected static $cache = [];

    /**
     * @param int $userId
     * @param int $entityType
     * @param int $entityId
     * @param int $action
     * @return bool
     * @throws ObjectNotFoundException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function checkPermission(int $userId, int $entityType, int $entityId, int $action): bool
    {
        if(!isset(self::$cache[$userId])) {
            $objUser = UserTable::getByPrimary($userId, [
                'select' => [
                    'ID',
                    'UF_DEPARTMENT'
                ]
            ])->fetchObject();

            if(!$objUser)
                throw new ObjectNotFoundException(Loc::getMessage("ITB_FIN.EO_ACCESS_RIGHT.INVALID_USER"));

            $depsList = $objUser->get("UF_DEPARTMENT");
            $arrDeps = is_array($depsList) ? $depsList : [$depsList];

            $result = AccessRightTable::getList([
                'select' => ['*'],
                'filter' => [
                    'LOGIC' => 'OR',
                    [
                        '=USER_TYPE' => AccessRightTable::DEPARTMENT,
                        '=USER_ID' => $arrDeps,
                    ],
                    [
                        '=USER_TYPE' => AccessRightTable::USER,
                        '=USER_ID' => $userId,
                    ]
                ]
            ]);
            self::$cache[$userId] = [];
            while($access = $result->fetchObject()) {
                self::$cache[$userId][$access->getEntityTypeId()][$access->getAction()][$access->getEntityId()] = $access->getEntityId();
            }
        }
        return isset(self::$cache[$userId][$entityType][$action][$entityId]);
    }

    /**
     * @return array
     */
    public static function getCache(): array
    {
        return self::$cache;
    }
}