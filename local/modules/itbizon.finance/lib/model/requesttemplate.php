<?php

namespace Itbizon\Finance\Model;

use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Exception;
use Itbizon\Finance\RequestTemplate;

Loc::loadMessages(__FILE__);

/**
 * Class RequestTable
 * @package Itbizon\Finance\Model
 */
class RequestTemplateTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE_TABLE_ENTITY_TITLE");
    }
    
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_request_template';
    }
    
    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_REQUEST_TEMPLATE';
    }
    
    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return RequestTemplate::class;
    }
    
    /**
     * @param string|null $activeKey
     * @return array|mixed
     */
    public static function getActiveName(string $activeKey = null)
    {
        $result = [
            'Y'=>Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE_ACTIVE.YES'),
            'N'=>Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE_ACTIVE.NO'),
        ];
        if($activeKey)
            return $result[$activeKey];
        return $result;
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
                    'title'        => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.ID'),
                    'primary'      => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\BooleanField(
                'ACTIVE',
                [
                    'title'        => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.ACTIVE'),
                    'default_value'=>'Y',
                    'values' => [
                        'N',
                        'Y'
                    ]
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'title'    => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.NAME'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'AUTHOR_ID',
                [
                    'title'    => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.AUTHOR_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'AUTHOR',
                UserTable::getEntity(),
                Join::on('this.AUTHOR_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'CATEGORY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.CATEGORY_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'CATEGORY',
                OperationCategoryTable::getEntity(),
                Join::on('this.CATEGORY_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'AMOUNT',
                [
                    'title'      => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.AMOUNT'),
                    'required'   => true,
                    'validation' => function()
                    {
                        return [
                            function($value)
                            {
                                return $value > 0 ? true : Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.AMOUNT.VALIDATION_ERROR');
                            }
                        ];
                    }
                ]
            ),
            new Fields\TextField(
                'COMMENT_SITUATION',
                [
                    'title'    => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.COMMENT_SITUATION'),
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMMENT_DATA',
                [
                    'title'    => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.COMMENT_DATA'),
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMMENT_SOLUTION',
                [
                    'title'    => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.COMMENT_SOLUTION'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'VAULT_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.VAULT_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'VAULT',
                VaultTable::getEntity(),
                Join::on('this.VAULT_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'ENTITY_TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.ENTITY_TYPE'),
                ]
            ),
            new Fields\IntegerField(
                'ENTITY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE_TABLE.ENTITY_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'LEAD',
                LeadTable::getEntity(),
                Join::on('this.ENTITY_ID', 'ref.ID')
            ))->configureJoinType('left'),
            (new Fields\Relations\Reference(
                'DEAL',
                DealTable::getEntity(),
                Join::on('this.ENTITY_ID', 'ref.ID')
            ))->configureJoinType('left'),
            (new Fields\Relations\Reference(
                'CONTACT',
                ContactTable::getEntity(),
                Join::on('this.ENTITY_ID', 'ref.ID')
            ))->configureJoinType('left'),
            (new Fields\Relations\Reference(
                'COMPANY',
                CompanyTable::getEntity(),
                Join::on('this.ENTITY_ID', 'ref.ID')
            ))->configureJoinType('left'),
        ];
    }
}