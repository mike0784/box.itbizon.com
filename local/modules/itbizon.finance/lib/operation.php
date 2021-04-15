<?php


namespace Itbizon\Finance;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserGroupTable;
use CCrmOwnerType;
use CIMNotify;
use Exception;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\VaultTable;

Loc::loadMessages(__FILE__);

class Operation extends Model\EO_Operation
{
    /**
     * @param array $data
     * @return Operation
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function createIncome(array $data): self
    {
        if ($data['AMOUNT'] < 1)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_AMOUNT'));

        $dstVault = Model\VaultTable::getById($data['DST_VAULT_ID'])->fetchObject();
        if (!$dstVault)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.DST_EMPTY'));
        if($dstVault->isVirtual())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.VIRTUAL'));

        $category = Model\OperationCategoryTable::getById($data['CATEGORY_ID'])->fetchObject();
        if (!$category)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_CATEGORY'));

        if (!$category->getAllowIncome())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CATEGORY_PROHIBITED'));

        $operation = new self();
        $operation->setName($data['NAME']);
        $operation->setType(Model\OperationTable::TYPE_INCOME);
        $operation->setStatus(Model\OperationTable::STATUS_NEW);
        $operation->setResponsibleId($data['RESPONSIBLE_ID']);
        $operation->setDstVault($dstVault);
        $operation->setCategory($category);
        $operation->setAmount($data['AMOUNT']);

        if(isset($data['ENTITY_TYPE_ID'])) {
            $operation->setEntityTypeId($data['ENTITY_TYPE_ID']);
        }
        if(isset($data['ENTITY_ID'])) {
            $operation->setEntityId($data['ENTITY_ID']);
        }
        if(isset($data['COMMENT'])) {
            $operation->setComment($data['COMMENT']);
        }
        if(isset($data['EXTERNAL_CODE'])) {
            $operation->setExternalCode($data['EXTERNAL_CODE']);
        }
        if(isset($data['REQUEST_ID'])) {
            $operation->setRequestId($data['REQUEST_ID']);
        }
        if(intval($data['FILE_ID']) > 0) {
            $operation->setFileId($data['FILE_ID']);
        }

        $result = $operation->save();
        if ($result->isSuccess()) {
            if(!$operation->getDstVault()->isStock()) {
                $operation->confirmMessage();
            }
            return $operation;
        } else
            throw new Exception(implode('; ', $result->getErrorMessages()));
    }

    /**
     * @param array $data
     * @return Operation
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function createOutgo(array $data): self
    {
        if ($data['AMOUNT'] < 1)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_AMOUNT'));

        $srcVault = Model\VaultTable::getById($data['SRC_VAULT_ID'])->fetchObject();
        if (!$srcVault)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.SRC_EMPTY'));
        if($srcVault->isVirtual())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.VIRTUAL'));

        $category = Model\OperationCategoryTable::getById($data['CATEGORY_ID'])->fetchObject();
        if (!$category)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_CATEGORY'));

        if (!$category->getAllowOutgo())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CATEGORY_PROHIBITED'));

        $operation = new self();
        $operation->setName($data['NAME']);
        $operation->setType(Model\OperationTable::TYPE_OUTGO);
        $operation->setStatus(Model\OperationTable::STATUS_NEW);
        $operation->setResponsibleId($data['RESPONSIBLE_ID']);
        $operation->setSrcVault($srcVault);
        $operation->setCategory($category);
        $operation->setAmount($data['AMOUNT']);

        if(isset($data['ENTITY_TYPE_ID'])) {
            $operation->setEntityTypeId($data['ENTITY_TYPE_ID']);
        }
        if(isset($data['ENTITY_ID'])) {
            $operation->setEntityId($data['ENTITY_ID']);
        }
        if(isset($data['COMMENT'])) {
            $operation->setComment($data['COMMENT']);
        }
        if(isset($data['EXTERNAL_CODE'])) {
            $operation->setExternalCode($data['EXTERNAL_CODE']);
        }
        if(isset($data['REQUEST_ID'])) {
            $operation->setRequestId($data['REQUEST_ID']);
        }
        if(intval($data['FILE_ID']) > 0) {
            $operation->setFileId($data['FILE_ID']);
        }

        $result = $operation->save();
        if ($result->isSuccess()) {
            $operation->confirmMessage();
            return $operation;
        } else
            throw new Exception(implode('; ', $result->getErrorMessages()));
    }

    /**
     * @param array $data
     * @return Operation
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function createTransfer(array $data): self
    {
        if ($data['AMOUNT'] < 1)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_AMOUNT'));

        $srcVault = Model\VaultTable::getById($data['SRC_VAULT_ID'])->fetchObject();
        if (!$srcVault)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.SRC_EMPTY'));
        if($srcVault->isVirtual())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.VIRTUAL'));

        $dstVault = Model\VaultTable::getById($data['DST_VAULT_ID'])->fetchObject();
        if (!$dstVault)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.DST_EMPTY'));
        if($dstVault->isVirtual())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.VIRTUAL'));

        if($srcVault->getId() === $dstVault->getId())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.SQR_DST_EQUAL'));

        $category = Model\OperationCategoryTable::getById($data['CATEGORY_ID'])->fetchObject();
        if (!$category)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_CATEGORY'));

        if (!$category->getAllowTransfer())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CATEGORY_PROHIBITED'));

        $operation = new self();
        $operation->setName($data['NAME']);
        $operation->setType(Model\OperationTable::TYPE_TRANSFER);
        $operation->setStatus(Model\OperationTable::STATUS_NEW);
        $operation->setResponsibleId($data['RESPONSIBLE_ID']);
        $operation->setSrcVault($srcVault);
        $operation->setDstVault($dstVault);
        $operation->setCategory($category);
        $operation->setAmount($data['AMOUNT']);

        if(isset($data['ENTITY_TYPE_ID'])) {
            $operation->setEntityTypeId($data['ENTITY_TYPE_ID']);
        }
        if(isset($data['ENTITY_ID'])) {
            $operation->setEntityId($data['ENTITY_ID']);
        }
        if(isset($data['COMMENT'])) {
            $operation->setComment($data['COMMENT']);
        }
        if(isset($data['EXTERNAL_CODE'])) {
            $operation->setExternalCode($data['EXTERNAL_CODE']);
        }
        if(isset($data['REQUEST_ID'])) {
            $operation->setRequestId($data['REQUEST_ID']);
        }
        if(intval($data['FILE_ID']) > 0) {
            $operation->setFileId($data['FILE_ID']);
        }

        $result = $operation->save();
        if ($result->isSuccess()) {
            $operation->confirmMessage();
            return $operation;
        } else
            throw new Exception(implode('; ', $result->getErrorMessages()));
    }

    /**
     * @return bool
     */
    protected function confirmMessage()
    {
        try {
            //Get user list
            $to = [];
            if ($this->getSrcVault())
                $to[] = $this->getSrcVault()->getResponsibleId();
            if ($this->getDstVault())
                $to[] = $this->getDstVault()->getResponsibleId();
            $to = array_unique($to);

            //Prepare message
            $message = str_replace(
                ['#ID#', '#NAME#', '#SUM#', '#DIR#', '#TYPE#', '#CAT#', '#URL#'],
                [$this->getId(), $this->getName(), $this->getAmountPrint(), $this->getDirection(), $this->getTypeName(), $this->getCategory()->getName(), $this->getUrl()],
                Loc::getMessage('ITB_FIN.OPERATION.CONFIRM_MESSAGE')
            );

            foreach ($to as $userId) {
                if (is_numeric($userId)) {
                    CIMNotify::Add([
                        'FROM_USER_ID' => $this->getResponsibleId(),
                        'TO_USER_ID' => $userId,
                        'NOTIFY_TYPE' => IM_NOTIFY_CONFIRM,
                        'NOTIFY_MESSAGE' => $message,
                        'NOTIFY_MODULE' => 'itbizon.finance',
                        'NOTIFY_TAG' => 'FINANCE|OPERATION|' . $this->getId() . '|' . $userId,
                        'NOTIFY_EVENT' => 'FINANCE|OPERATION|' . $this->getId(),
                        'NOTIFY_BUTTONS' => [
                            ['TITLE' => Loc::getMessage('ITB_FIN.OPERATION.ACCEPT'), 'VALUE' => 'Y', 'TYPE' => 'accept'],
                            ['TITLE' => Loc::getMessage('ITB_FIN.OPERATION.CANCEL'), 'VALUE' => 'N', 'TYPE' => 'cancel'],
                        ],
                    ]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getAmountPrint()
    {
        return sprintf('%.2f', $this->getAmount() / 100.);
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        $directionText = '';
        switch ($this->getType()) {
            case Model\OperationTable::TYPE_INCOME:
                if ($this->getDstVault())
                    $directionText = '> ' . $this->getDstVault()->getName();
                break;
            case Model\OperationTable::TYPE_OUTGO:
                if ($this->getSrcVault())
                    $directionText = $this->getSrcVault()->getName() . ' >';
                break;
            case Model\OperationTable::TYPE_TRANSFER:
                if ($this->getDstVault() && $this->getSrcVault())
                    $directionText = $this->getSrcVault()->getName() . ' > ' . $this->getDstVault()->getName();
                break;
        }
        return $directionText;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return Model\OperationTable::getType($this->getType());
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return '/finance/operation/edit/' . $this->getId() . '/';
    }

    /**
     * @return string
     */
    public function getFileUrl(): string
    {
        if($this->getFileId()) {
            return \CFile::GetPath($this->getFileId());
        }
        return '';
    }

    /**
     * @param $userId
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ObjectException
     * @throws Exception
     */
    public function confirm($userId)
    {
        $userId = intval($userId);

        if (!$this->isAllowConfirmBy($userId))
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CONFIRM'));

        $this->saveAction($userId, Model\OperationActionTable::CONFIRM);

        $srcVault = $this->getSrcVault();
        $dstVault = $this->getDstVault();

        //Commit
        if ($this->isConfirmed()) {
            //Save action
            if ($this->getType() == Model\OperationTable::TYPE_INCOME) {
                if (!$dstVault->commit($this))
                    throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.COMMIT_INCOME'));
            } elseif ($this->getType() == Model\OperationTable::TYPE_OUTGO) {
                if (!$srcVault->commit($this))
                    throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.COMMIT_OUTGO'));
            } elseif ($this->getType() == Model\OperationTable::TYPE_TRANSFER) {
                if ($srcVault->commit($this)) {
                    if (!$dstVault->commit($this)) {
                        $srcVault->commit($this);
                        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.COMMIT_TRANSFER'));
                    }
                } else
                    throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.COMMIT_TRANSFER'));
            }

            //Update DB
            $this->setDateCommit(new DateTime());
            $this->setStatus(Model\OperationTable::STATUS_COMMIT);

            $result = $this->save();
            if (!$result->isSuccess())
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CHANGE_STATUS'));
            $this->clearConfirmMessage();

            $event = new Event("itbizon.finance", "onAfterOperationCommit", [$this->getId()]);
            $event->send();

            $message = str_replace(
                ['#ID#', '#NAME#', '#URL#'],
                [$this->getId(), $this->getName(), $this->getUrl()],
                Loc::getMessage('ITB_FIN.OPERATION.CONFIRM_DONE_MESSAGE')
            );
            Helper::sendNotify($this->getResponsibleId(), $message);

            if($this->getRequestId()) {
                $request = RequestTable::getById($this->getRequestId())->fetchObject();
                if($request) {
                    $request->fix($this);
                }
            }
        }
        return true;
    }

    /**
     * @param $userId
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowConfirmBy($userId)
    {
        $dstResponsible = false;
        $srcResponsible = false;
        if($userId) {
            if($this->getType() == Model\OperationTable::TYPE_INCOME || $this->getType() == Model\OperationTable::TYPE_TRANSFER) {
                if($this->getDstVault() && $this->getDstVault()->getResponsibleId() == $userId) {
                    $dstResponsible = true;
                }
            }
            if($this->getType() == Model\OperationTable::TYPE_OUTGO || $this->getType() == Model\OperationTable::TYPE_TRANSFER) {
                if($this->getSrcVault() && $this->getSrcVault()->getResponsibleId() == $userId) {
                    $srcResponsible = true;
                }
            }
        } else {
            $dstResponsible = true;
            $srcResponsible = true;
        }
        /*if (
            ($this->getType() == Model\OperationTable::TYPE_INCOME || $this->getType() == Model\OperationTable::TYPE_TRANSFER) &&
            ($this->getDstVault() && $this->getDstVault()->getResponsibleId() != $userId) &&
            $userId
        )
            $dstResponsible = false;

        $srcResponsible = true;
        if (
            ($this->getType() == Model\OperationTable::TYPE_OUTGO || $this->getType() == Model\OperationTable::TYPE_TRANSFER) &&
            ($this->getSrcVault() && $this->getSrcVault()->getResponsibleId() != $userId) &&
            $userId
        )
            $srcResponsible = false;*/

        return
            ($this->getStatus() == Model\OperationTable::STATUS_NEW) &&
            ($dstResponsible || $srcResponsible) &&
            (!$this->isConfirmedBy($userId) || !$userId);
    }

    /**
     * @param int $userId
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isConfirmedBy($userId)
    {
        $result = Model\OperationActionTable::getList([
            'count_total' => true,
            'select' => ['ID'],
            'filter' => [
                '=USER_ID' => $userId,
                '=OPERATION_ID' => $this->getId()
            ]
        ]);

        $scrVault = $this->getSrcVault();
        $dstVault = $this->getDstVault();

        if (
            ($this->getType() == Model\OperationTable::TYPE_TRANSFER) &&
            ($scrVault && $scrVault->getResponsibleId() == $userId) &&
            ($dstVault && $dstVault->getResponsibleId() == $userId)
        ) {
            return $result->getCount() > 1;
        }

        return $result->getCount() > 0;
    }

    /**
     * @param $userId
     * @param $type
     * @return bool
     * @throws Exception
     */
    protected function saveAction($userId, $type)
    {
        $action = new OperationAction();
        $action->setOperationId($this->getId());
        $action->setUserId($userId);
        $action->setType($type);
        return $action->save()->isSuccess();
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function isConfirmed()
    {
        $confirmCount = 0;
        $result = Model\OperationActionTable::getList([
            'select' => ['*'],
            'filter' => [
                '=TYPE' => Model\OperationActionTable::CONFIRM,
                '=OPERATION_ID' => $this->getId(),
            ]
        ]);
        while ($action = $result->fetchObject()) {
            if (!$action->getUserId())
                return true;
            $confirmCount++;
        }
        return (
            (($this->getType() == Model\OperationTable::TYPE_INCOME || $this->getType() == Model\OperationTable::TYPE_OUTGO) && $confirmCount > 0) ||
            (($this->getType() == Model\OperationTable::TYPE_TRANSFER) && $confirmCount >= 2)
        );
    }

    /**
     * @return bool
     */
    public function clearConfirmMessage()
    {
        try {
            CIMNotify::DeleteByModule('itbizon.finance', 'FINANCE|OPERATION|' . $this->getId());
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $userId
     * @throws Exception
     */
    public function decline($userId)
    {
        $userId = intval($userId);

        if ($this->getStatus() !== Model\OperationTable::STATUS_NEW && $this->getStatus() !== Model\OperationTable::STATUS_PLANNING)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.INVALID_STATUS') . ' ' . $this->getStatusName());

        if ($this->isAllowDeclineBy($userId)) {
            //Save action
            $this->saveAction($userId, Model\OperationActionTable::DECLINE);

            //Update DB
            $this->setStatus(Model\OperationTable::STATUS_DECLINE);
            $result = $this->save();
            if (!$result->isSuccess())
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CHANGE_STATUS'));

            $this->clearConfirmMessage();

            $event = new Event("itbizon.finance", "onAfterOperationDecline", [$this->getId()]);
            $event->send();

            $message = str_replace(
                ['#ID#', '#NAME#', '#URL#'],
                [$this->getId(), $this->getName(), $this->getUrl()],
                Loc::getMessage('ITB_FIN.OPERATION.DECLINE_MESSAGE')
            );
            Helper::sendNotify($this->getResponsibleId(), $message);

            if($this->getRequestId()) {
                $request = RequestTable::getById($this->getRequestId())->fetchObject();
                if($request) {
                    $request->fix($this);
                }
            }
        } else
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.DECLINE_PROHIBITED'));
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return Model\OperationTable::getStatus($this->getStatus());
    }

    /**
     * @param int $userId
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowDeclineBy(int $userId)
    {
        $dstVault = $this->getDstVault();
        $srcVault = $this->getSrcVault();

        $income = $this->getType() == Model\OperationTable::TYPE_INCOME &&
            $dstVault &&
            $userId === $dstVault->getResponsibleId() &&
            !$this->isConfirmedBy($dstVault->getResponsibleId());

        $outgo = $this->getType() == Model\OperationTable::TYPE_OUTGO &&
            $srcVault &&
            $userId === $srcVault->getResponsibleId() &&
            !$this->isConfirmedBy($srcVault->getResponsibleId());

        $transfer = $this->getType() == Model\OperationTable::TYPE_TRANSFER &&
            (($dstVault && $userId === $dstVault->getResponsibleId() && !$this->isConfirmedBy($dstVault->getResponsibleId())) ||
                ($srcVault && $userId === $srcVault->getResponsibleId() && !$this->isConfirmedBy($srcVault->getResponsibleId())));

        return
            $this->getStatus() == Model\OperationTable::STATUS_NEW && (!$userId || $income || $outgo || $transfer);
    }

    /**
     * @param $userId
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public function rollback($userId)
    {
        $srcVault = $this->getSrcVault();
        $dstVault = $this->getDstVault();

        if (!$this->getId() || !$this->isAllowRollbackBy($userId))
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.ROLLBACK'));

        if ($this->getType() === Model\OperationTable::TYPE_INCOME) {
            if (!$dstVault)
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.DST_EMPTY'));

            $dstVault->rollback($this);
        } elseif ($this->getType() === Model\OperationTable::TYPE_OUTGO) {
            if (!$srcVault)
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.SRC_EMPTY'));

            $srcVault->rollback($this);
        } elseif ($this->getType() === Model\OperationTable::TYPE_TRANSFER) {
            if (!$dstVault)
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.DST_EMPTY'));
            if (!$srcVault)
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.SRC_EMPTY'));

            $dstVault->rollback($this);
            $srcVault->rollback($this);
        }

        $this->setStatus(Model\OperationTable::STATUS_CANCEL);

        $result = $this->save();
        if (!$result->isSuccess())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION.ERROR.CHANGE_STATUS'));

        $event = new Event("itbizon.finance", "onAfterOperationCancel", [$this->getId()]);
        $event->send();

        $this->saveAction($userId, Model\OperationActionTable::CANCEL);

        $message = str_replace(
            ['#ID#', '#NAME#', '#URL#'],
            [$this->getId(), $this->getName(), $this->getUrl()],
            Loc::getMessage('ITB_FIN.OPERATION.CANCEL_MESSAGE')
        );
        Helper::sendNotify($this->getResponsibleId(), $message);

        if($this->getRequestId()) {
            $request = RequestTable::getById($this->getRequestId())->fetchObject();
            if($request) {
                $request->fix($this);
            }
        }
        return true;
    }

    /**
     * @param $userId
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRollbackBy($userId)
    {
        $userIsAdmin = UserGroupTable::getList([
                'filter' => [
                    '=GROUP_ID' => 1, // Группа администраторов
                    'USER_ID' => $userId
                ]
            ])->fetch() !== null;

        return
            ($userIsAdmin || !$userId) &&
            $this->getStatus() == Model\OperationTable::STATUS_COMMIT;
    }

    /**
     * @return string
     */
    public function getResponsibleName(): string
    {
        if ($user = $this->getResponsible())
            return $user->getLastName() . ' ' . $user->getName();
        else
            return '';
    }

    /**
     * @return string
     */
    public function getResponsibleUrl(): string
    {
        return '/company/personal/user/' . $this->getResponsibleId() . '/';
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        if ($this->getEntityTypeId() === CCrmOwnerType::Lead) {
            if ($this->getLead())
                return $this->getLead()->getTitle();
        } else if ($this->getEntityTypeId() === CCrmOwnerType::Deal) {
            if ($this->getDeal())
                return $this->getDeal()->getTitle();
        } else if ($this->getEntityTypeId() === CCrmOwnerType::Contact) {
            if ($this->getContact())
                return $this->getContact()->getFullName();
        } else if ($this->getEntityTypeId() === CCrmOwnerType::Company) {
            if ($this->getCompany())
                return $this->getCompany()->getTitle();
        }
        return '-';
    }

    /**
     * @return string
     */
    public function getEntityUrl(): string
    {
        if ($this->getEntityId()) {
            if ($this->getEntityTypeId() === CCrmOwnerType::Lead) {
                if ($this->getLead())
                    return '/crm/lead/details/' . $this->getEntityId() . '/';
            } else if ($this->getEntityTypeId() === CCrmOwnerType::Deal) {
                if ($this->getDeal())
                    return '/crm/deal/details/' . $this->getEntityId() . '/';
            } else if ($this->getEntityTypeId() === CCrmOwnerType::Contact) {
                if ($this->getContact())
                    return '/crm/contact/details/' . $this->getEntityId() . '/';
            } else if ($this->getEntityTypeId() === CCrmOwnerType::Company) {
                if ($this->getCompany())
                    return '/crm/company/details/' . $this->getEntityId() . '/';
            }
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isStockOperation()
    {
        return (($this->getSrcVault() && $this->getSrcVault()->getType() === VaultTable::TYPE_STOCK) ||
        ($this->getDstVault() && $this->getDstVault()->getType() === VaultTable::TYPE_STOCK));
    }
}
