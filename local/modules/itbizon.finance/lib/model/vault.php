<?php


namespace Itbizon\Finance\Model;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Exception;
use Itbizon\Finance\Vault;

Loc::loadMessages(__FILE__);

/**
 * Class VaultTable
 * @package Itbizon\Finance\Model
 */
class VaultTable extends DataManager
{
    const TYPE_CASHBOX = 1;
    const TYPE_ACCOUNT = 2;
    const TYPE_CARD    = 3;
    const TYPE_STOCK   = 4;
    const TYPE_VIRTUAL = 5;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.VAULT_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_vault';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_VAULT';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return Vault::class;
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
                    'title' => Loc::getMessage('ITB_FIN.VAULT.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.DATE_CREATE'),
                    'default_value' => new DateTime()
                ]
            ),
            new Fields\IntegerField(
                'GROUP_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.GROUP_ID'),
                    'default_value' => new DateTime()
                ]
            ),
            (new Fields\Relations\Reference(
                'GROUP',
                VaultGroupTable::getEntity(),
                Join::on('this.GROUP_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\StringField(
                'NAME',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.NAME'),
                    'required' => true,
                    'validation' => function () {
                        return [
                            new Fields\Validators\UniqueValidator()
                        ];
                    },
                ]
            ),
            new Fields\IntegerField(
                'TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.TYPE'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'RESPONSIBLE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.RESPONSIBLE_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'RESPONSIBLE',
                UserTable::getEntity(),
                Join::on('this.RESPONSIBLE_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'BALANCE',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.BALANCE'),
                ]
            ),
            new BooleanField(
                'HIDE_ON_PLANNING',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.HIDE_ON_PLANNING'),
                    'default_value' => false
                ]
            ),
            new Fields\IntegerField(
                'STOCK_GROUP_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.STOCK_GROUP_ID'),
                    'required' => false,
                ]
            ),
            new Fields\IntegerField(
                'PERCENT',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT.PERCENT'),
                    'default_value' => 0,
                    'required' => false,
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
            self::TYPE_CASHBOX => Loc::getMessage('ITB_FIN.VAULT.TYPE.CASHBOX'),
            self::TYPE_ACCOUNT => Loc::getMessage('ITB_FIN.VAULT.TYPE.ACCOUNT'),
            self::TYPE_CARD => Loc::getMessage('ITB_FIN.VAULT.TYPE.CARD'),
            self::TYPE_VIRTUAL => Loc::getMessage('ITB_FIN.VAULT.TYPE.VIRTUAL'),
        ];
        if ($typeId !== null)
            return isset($types[$typeId]) ? $types[$typeId] : Loc::getMessage('ITB_FIN.VAULT.TYPE.UNKNOWN');
        else
            return $types;
    }

    /**
     * @param array $data
     * @return AddResult
     * @throws Exception
     */
    public static function add(array $data)
    {
        if(isset($data['TYPE']) && $data['TYPE'] == self::TYPE_STOCK)
            throw new Exception('Stock type deny for vault entity');
        return parent::add($data);
    }

    /**
     * @param mixed $primary
     * @param array $data
     * @return UpdateResult
     * @throws Exception
     */
    public static function update($primary, array $data)
    {
        if(isset($data['TYPE']) && $data['TYPE'] == self::TYPE_STOCK)
            throw new Exception('Stock type deny for vault entity');
        return parent::update($primary, $data);
    }

    /**
     * @param array $data
     * @return Result
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public static function getList(array $data = [])
    {
        if (!isset($data['select']) || !is_array($data['select']) || empty($data['select']))
            $data['select'] = ['*', 'RESPONSIBLE.LAST_NAME', 'RESPONSIBLE.NAME'];
        return parent::getList($data);
    }

    /**
     * @param $event
     * @return Entity\EventResult
     */
    public static function OnBeforeDelete($event)
    {
        $result = new Entity\EventResult;
        $id = $event->getParameter("id");

        try {
            $id = $id["ID"];

            $currentUser = CurrentUser::get();
            if (!$currentUser || !$currentUser->isAdmin())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.ACCESS_ERROR'));

            $vault = self::getById($id)->fetchObject();

            if ($vault->getBalance() != 0)
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.DELETE_ERROR'));

        } catch (Exception $e) {
            $result->addError(new Entity\EntityError($e->getMessage()));
        }

        return $result;
    }
}