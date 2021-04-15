<?php

namespace Itbizon\Finance\Model;

use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Main\Error;
use Bitrix\Main\FileTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Exception;
use Itbizon\Finance\Request;

Loc::loadMessages(__FILE__);

/**
 * Class RequestTable
 * @package Itbizon\Finance\Model
 */
class RequestTable extends DataManager
{
    protected static $status;
    
    const STATUS_NEW = 1;
    const STATUS_APPROVE = 2;
    const STATUS_DECLINE = 3;
    const STATUS_CONFIRM = 4;
    const STATUS_CANCEL = 5;
    const STATUS_FIX = 6;
    const STATUS_ERROR = 7;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.REQUEST_TABLE_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_request';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_REQUEST';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return Request::class;
    }
    
    /**
     *
     */
    protected static function statusInit()
    {
        if(!self::$status)
        {
            self::$status = [
                self::STATUS_NEW => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.NEW'),
                self::STATUS_APPROVE => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.APPROVE'),
                self::STATUS_DECLINE => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.DECLINE'),
                self::STATUS_CONFIRM => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.CONFIRM'),
                self::STATUS_CANCEL => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.CANCEL'),
                self::STATUS_FIX => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.FIX'),
                self::STATUS_ERROR => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS.ERROR'),
            ];
        }
    }
    
    /**
     * @param $statusId
     * @return string
     */
    public static function getStatusName($statusId)
    {
        self::statusInit();
        return self::$status[$statusId] ?? Loc::getMessage("ITB_FIN.REQUEST_TABLE.STATUS.ID_ERROR");
    }
    
    /**
     * @return mixed
     */
    public static function getStatuses()
    {
        self::statusInit();
        return self::$status;
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
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\IntegerField(
                'STATUS',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STATUS'),
                    'required' => true,
                    'default_value' => 1
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.NAME'),
                    'required' => true,
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.DATE_CREATE'),
                    'required' => true,
                    'default_value' => new DateTime()
                ]
            ),
            new Fields\DatetimeField(
                'DATE_APPROVE',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.DATE_APPROVE'),
                    'required' => false,
                ]
            ),
            new Fields\IntegerField(
                'AUTHOR_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.AUTHOR_ID'),
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
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.CATEGORY_ID'),
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
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.AMOUNT'),
                    'required' => true,
                    'validation' => function () {
                        return [
                            function ($value) {
                                return $value > 0 ? true : Loc::getMessage('ITB_FIN.REQUEST_TABLE.AMOUNT.VALIDATION_ERROR');
                            }
                        ];
                    }
                ]
            ),
            new Fields\TextField(
                'COMMENT_SITUATION',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.COMMENT_SITUATION'),
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMMENT_DATA',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.COMMENT_DATA'),
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMMENT_SOLUTION',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.COMMENT_SOLUTION'),
                ]
            ),
            new Fields\IntegerField(
                'APPROVER_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.APPROVER_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'APPROVER',
                UserTable::getEntity(),
                Join::on('this.APPROVER_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\TextField(
                'APPROVER_COMMENT',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.APPROVER_COMMENT'),
                ]
            ),
            new Fields\IntegerField(
                'VAULT_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.VAULT_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'VAULT',
                VaultTable::getEntity(),
                Join::on('this.VAULT_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'STOCK_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.STOCK_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'STOCK',
                StockTable::getEntity(),
                Join::on('this.STOCK_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'ENTITY_TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.ENTITY_TYPE'),
                ]
            ),
            new Fields\IntegerField(
                'ENTITY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.ENTITY_ID'),
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
            new Fields\IntegerField(
                'FILE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.REQUEST_TABLE.FILE_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'FILE',
                FileTable::getEntity(),
                Join::on('this.FILE_ID', 'ref.ID')
            ))->configureJoinType('left'),
        ];
    }

    /**
     * @param array $data
     * @return AddResult
     */
    public static function add(array $data)
    {
        $result = new AddResult();
        try {
            $currentDate = new \DateTime();
            $period = PeriodTable::getByDate($currentDate);
            if(!$period) {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST_TABLE.ERROR.PERIOD_NOT_FOUND'));
            }
            $currentDate = new DateTime();
            if($currentDate > $period->getDateEnd()) {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST_TABLE.ERROR.PERIOD_INVALID_STATUS'));
            }
            return parent::add($data);
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }
}
