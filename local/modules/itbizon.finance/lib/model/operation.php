<?php


namespace Itbizon\Finance\Model;

use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Entity;
use Bitrix\Main\FileTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Exception;
use Itbizon\Finance\Operation;

Loc::loadMessages(__FILE__);

/**
 * Class OperationTable
 * @package Itbizon\Finance\Model
 */
class OperationTable extends DataManager
{
    const TYPE_INCOME = 1;
    const TYPE_OUTGO = 2;
    const TYPE_TRANSFER = 3;

    const STATUS_NEW = 1;
    const STATUS_PLANNING = 2;
    const STATUS_DECLINE = 3;
    const STATUS_COMMIT = 4;
    const STATUS_CANCEL = 5;
    const STATUS_ERROR = -1;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.OPERATION_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_operation';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_OPERATION';
    }

    /**
     * @return Operation|string
     */
    public static function getObjectClass()
    {
        return Operation::class;
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
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.NAME'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.TYPE'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'STATUS',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.STATUS'),
                    'required' => true,
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.DATE_CREATE'),
                    'required' => true,
                    'default_value' => new DateTime()
                ]
            ),
            new Fields\DatetimeField(
                'DATE_COMMIT',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.DATE_COMMIT'),
                ]
            ),
            new Fields\IntegerField(
                'RESPONSIBLE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.RESPONSIBLE_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'RESPONSIBLE',
                UserTable::getEntity(),
                Join::on('this.RESPONSIBLE_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'SRC_VAULT_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.SRC_VAULT_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'SRC_VAULT',
                VaultTable::getEntity(),
                Join::on('this.SRC_VAULT_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'DST_VAULT_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.DST_VAULT_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'DST_VAULT',
                VaultTable::getEntity(),
                Join::on('this.DST_VAULT_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'CATEGORY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.CATEGORY_ID'),
                    'required' => true,
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
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.AMOUNT'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'ENTITY_TYPE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.ENTITY_TYPE_ID'),
                ]
            ),
            new Fields\IntegerField(
                'ENTITY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.ENTITY_ID'),
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
            new Fields\TextField(
                'COMMENT',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.COMMENT'),
                ]
            ),
            new Fields\StringField(
                'EXTERNAL_CODE',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.EXTERNAL_CODE'),
                ]
            ),
            new Fields\IntegerField(
                'REQUEST_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.REQUEST_ID'),
                ]
            ),
            (new Fields\Relations\Reference(
                'REQUEST',
                RequestTable::getEntity(),
                Join::on('this.REQUEST_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'FILE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION.FILE_ID'),
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
     * @param int $typeId
     * @return array|string
     */
    public static function getType($typeId = null)
    {
        $types = [
            self::TYPE_INCOME => Loc::getMessage('ITB_FIN.OPERATION.TYPE.INCOME'),
            self::TYPE_OUTGO => Loc::getMessage('ITB_FIN.OPERATION.TYPE.OUTGO'),
            self::TYPE_TRANSFER => Loc::getMessage('ITB_FIN.OPERATION.TYPE.TRANSFER'),
        ];
        if ($typeId !== null)
            return isset($types[$typeId]) ? $types[$typeId] : Loc::getMessage('ITB_FIN.OPERATION.TYPE.UNKNOWN');
        else
            return $types;
    }

    /**
     * @param int $statusId
     * @return array|string
     */
    public static function getStatus($statusId = null)
    {
        $types = [
            self::STATUS_NEW => Loc::getMessage('ITB_FIN.OPERATION.STATUS.NEW'),
            self::STATUS_PLANNING => Loc::getMessage('ITB_FIN.OPERATION.STATUS.PLANNING'),
            self::STATUS_DECLINE => Loc::getMessage('ITB_FIN.OPERATION.STATUS.DECLINE'),
            self::STATUS_COMMIT => Loc::getMessage('ITB_FIN.OPERATION.STATUS.COMMIT'),
            self::STATUS_CANCEL => Loc::getMessage('ITB_FIN.OPERATION.STATUS.CANCEL'),
            self::STATUS_ERROR => Loc::getMessage('ITB_FIN.OPERATION.STATUS.ERROR'),
        ];

        if ($statusId !== null)
            return isset($types[$statusId]) ? $types[$statusId] : Loc::getMessage('ITB_FIN.OPERATION.STATUS.UNKNOWN');
        else
            return $types;
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
            $data['select'] = [
                '*',
                'RESPONSIBLE.NAME',
                'RESPONSIBLE.LAST_NAME',
                'CATEGORY',
                'SRC_VAULT',
                'DST_VAULT',
                'LEAD.TITLE',
                'DEAL.TITLE',
                'CONTACT.FULL_NAME',
                'COMPANY.TITLE'
            ];
        return parent::getList($data);
    }

    /**
     * @param Event $event
     * @return Entity\EventResult|void
     */
    public static function onBeforeDelete(Event $event)
    {
        $result = new Entity\EventResult;
        $data   = $event->getParameter("id");

        try {
            $id = $data["ID"];
            $operation = OperationTable::getById($id)->fetchObject();
            $operation->clearConfirmMessage();

        } catch (Exception $e) {
            $result->addError(new Entity\EntityError($e->getMessage()));
        }

        return $result;
    }

    /**
     * @param Event $event
     * @return Entity\EventResult|void
     */
    public static function onAfterDelete(Event $event)
    {
        $result = new Entity\EventResult;
        try {
            $data = $event->getParameter("id");
            $id   = $data["ID"];
            $event = new \Bitrix\Main\Event("itbizon.finance", "onAfterOperationDelete", [$id]);
            $event->send();
        } catch (Exception $e) {
            $result->addError(new Entity\EntityError($e->getMessage()));
            return $result;
        }
    }
}