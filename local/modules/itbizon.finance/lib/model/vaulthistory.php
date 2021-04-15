<?php


namespace Itbizon\Finance\Model;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Itbizon\Finance\VaultHistory;

Loc::loadMessages(__FILE__);

/**
 * Class VaultHistoryTable
 * @package Itbizon\Finance\Model
 */
class VaultHistoryTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.VAULT_HISTORY_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_vault_history';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return VaultHistory::class;
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
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.DATE_CREATE'),
                    'required' => true,
                    'default_value' => new DateTime(),
                ]
            ),
            new Fields\IntegerField(
                'VAULT_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.VAULT_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'VAULT',
                VaultTable::getEntity(),
                Join::on('this.VAULT_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'BALANCE',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.BALANCE'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'OPERATION_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.OPERATION_ID'),
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'COMMENT',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_HISTORY.COMMENT'),
                ]
            ),
            (new Fields\Relations\Reference(
                'OPERATION',
                OperationTable::getEntity(),
                Join::on('this.OPERATION_ID', 'ref.ID')
            ))->configureJoinType('left'),
        ];
    }
}