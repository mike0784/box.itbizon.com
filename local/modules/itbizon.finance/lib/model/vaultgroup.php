<?php


namespace Itbizon\Finance\Model;


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use Itbizon\Finance\VaultGroup;

Loc::loadMessages(__FILE__);

/**
 * Class VaultGroupTable
 * @package Itbizon\Finance\Model
 */
class VaultGroupTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.VAULT_GROUP_TABLE.ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_vault_group';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_VAULT_GROUP';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return VaultGroup::class;
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
                    'title' => Loc::getMessage('ITB_FIN.VAULT_GROUP_TABLE.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'title' => Loc::getMessage('ITB_FIN.VAULT_GROUP_TABLE.NAME'),
                    'required' => true,
                ]
            ),
            (new OneToMany('ITEMS', VaultTable::class, 'GROUP'))
        ];
    }
}