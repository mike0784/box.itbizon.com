<?php


namespace Itbizon\Finance\Model;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Query\Join;
use \Bitrix\Main\Type\DateTime;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\ArgumentException;
use Itbizon\Finance\OperationAction;

Loc::loadMessages(__FILE__);

/**
 * Class OperationActionTable
 * @package Itbizon\Finance\Model
 */
class OperationActionTable extends DataManager
{
    const CONFIRM = 1;
    const DECLINE = 2;
    const COMMIT = 3;
    const CANCEL = 4;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.OPERATION_ACTION_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_operation_action';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return OperationAction::class;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_ACTION.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_ACTION.DATE_CREATE'),
                    'default_value' => new DateTime()
                ]
            ),
            new Fields\IntegerField(
                'OPERATION_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.OPERATION_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'OPERATION',
                OperationTable::getEntity(),
                Join::on('this.OPERATION_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\StringField(
                'USER_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_ACTION.USER_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'USER',
                UserTable::getEntity(),
                Join::on('this.USER_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_ACTION.TYPE'),
                    'required' => true,
                ]
            ),
        ];
    }

    /**
     * @param int $typeId
     * @return array|string
     */
    public static function getTypes($typeId = null)
    {
        $types = [
            self::CONFIRM => Loc::getMessage('ITB_FIN.OPERATION_ACTION.TYPE.CONFIRM'),
            self::DECLINE => Loc::getMessage('ITB_FIN.OPERATION_ACTION.TYPE.DECLINE'),
            self::COMMIT => Loc::getMessage('ITB_FIN.OPERATION_ACTION.TYPE.COMMIT'),
            self::CANCEL => Loc::getMessage('ITB_FIN.OPERATION_ACTION.TYPE.CANCEL'),
        ];
        if ($typeId !== null)
            return isset($types[$typeId]) ? $types[$typeId] : Loc::getMessage('ITB_FIN.OPERATION_ACTION.TYPE.UNKNOWN');
        else
            return $types;
    }
}