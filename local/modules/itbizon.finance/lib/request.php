<?php


namespace Itbizon\Finance;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\Type\DateTime;
use Exception;
use Itbizon\Finance\Model\OperationTable;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Model\VaultTable;
use Itbizon\Finance\Utils\Money;

Loc::loadMessages(__FILE__);

/**
 * Class Request
 * @package Itbizon\Finance
 */
class Request extends Model\EO_Request
{
    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return Model\RequestTable::getStatusName($this->getStatus());
    }
    
    /**
     * @return string
     */
    public function getEntityName()
    {
        if($this->getEntityType() == \CCrmOwnerType::Lead)
            return ($this->getLead()) ? $this->getLead()->getTitle() : '';
        elseif($this->getEntityType() == \CCrmOwnerType::Deal)
            return ($this->getDeal()) ? $this->getDeal()->getTitle() : '';
        elseif($this->getEntityType() == \CCrmOwnerType::Contact)
            return ($this->getContact()) ? $this->getContact()->getLastName().' '.$this->getContact()->getName() : '';
        elseif($this->getEntityType() == \CCrmOwnerType::Company)
            return ($this->getCompany()) ? $this->getCompany()->getTitle() : '';
        else
            return 'Неизвестная сущность crm';
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
     * @param float $amount
     * @return Request
     */
    public function setFloatAmount(float $amount)
    {
        return parent::setAmount(Money::toBase($amount));
    }

    /**
     * @return bool
     */
    public function isAllowCancel(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_NEW);
    }

    /**
     * @return bool
     */
    public function isAllowChange(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_NEW ||
            $this->getStatus() === Model\RequestTable::STATUS_DECLINE);
    }

    /**
     * @deprecated use isAllowChange method
     * @return bool
     */
    public function isAllowChangeVault(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_NEW);
    }

    /**
     * @return bool
     */
    public function isAllowApprove(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_NEW ||
                $this->getStatus() === Model\RequestTable::STATUS_DECLINE) && $this->getVaultId() &&
            (!Helper::isStockEnabled() || $this->getStockId());
    }

    /**
     * @return bool
     */
    public function isAllowDecline(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_NEW ||
                $this->getStatus() === Model\RequestTable::STATUS_APPROVE);
    }

    /**
     * @return bool
     */
    public function isAllowRenew(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_DECLINE ||
            $this->getStatus() === Model\RequestTable::STATUS_APPROVE);
    }

    /**
     * @return bool
     */
    public function isAllowConfirm(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_APPROVE || $this->getStatus() === Model\RequestTable::STATUS_DECLINE);
    }

    /**
     * @return bool
     */
    public function isAllowFix(): bool
    {
        return ($this->getStatus() === Model\RequestTable::STATUS_CONFIRM);
    }

    /**
     * @param int $userId
     * @return UpdateResult
     */
    public function cancel(int $userId)
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowCancel()) {
                $this->setStatus(Model\RequestTable::STATUS_CANCEL)
                    ->setApproverId($userId)
                    ->setDateApprove(new DateTime());

                $result = $this->save();
                if($result->isSuccess()) {
                    Helper::sendNotify(
                        $this->getAuthorId(),
                        str_replace(
                            ['#REQUEST_ID#'],
                            [$this->getId()],
                            Loc::getMessage('ITB_FIN.REQUEST.NOTIFY.CANCEL')));
                }
                return $result;
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @deprecated use change method
     * @param int $vaultId
     * @return UpdateResult
     */
    public function changeVault(int $vaultId)
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowChangeVault()) {
                $this->setVaultId($vaultId);
                return $this->save();
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param array $data
     * @return UpdateResult
     */
    public function change(array $data)
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowChange()) {
                $this->setName($data['NAME'])
                    ->setCategoryId($data['CATEGORY_ID'])
                    ->setEntityId($data['ENTITY_ID'])
                    ->setVaultId($data['VAULT_ID']);
                if(Helper::isStockEnabled()) {
                    $this->setStockId($data['STOCK_ID']);
                }
                return $this->save();
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param int $userId
     * @param int $amount
     * @param string $comment
     * @return UpdateResult
     */
    public function approve(int $userId, int $amount, string $comment)
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowApprove()) {
                if($amount < 1)
                    throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.INVALID_AMOUNT'));

                $vault = VaultTable::getById($this->getVaultId())->fetchObject();
                if(!$vault)
                    throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.VAULT_NOT_FOUND'));

                if(Helper::isStockEnabled()) {
                    $stock = StockTable::getById($this->getStockId())->fetchObject();
                    if(!$stock) {
                        throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.STOCK_NOT_FOUND'));
                    }
                    if($stock->getBalance() - $stock->getLockBalance() < $amount) {
                        throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.NOT_ENOUGH_MONEY'));
                    }
                }

                $this->setStatus(Model\RequestTable::STATUS_APPROVE)
                    ->setApproverId($userId)
                    ->setDateApprove(new DateTime())
                    ->setAmount($amount)
                    ->setApproverComment($comment);

                $result = $this->save();
                if($result->isSuccess()) {
                    Helper::sendNotify(
                        $this->getAuthorId(),
                        str_replace(
                            ['#REQUEST_ID#', '#COMMENT#'],
                            [$this->getId(), $this->getApproverComment()],
                            Loc::getMessage('ITB_FIN.REQUEST.NOTIFY.APPROVE')));
                }
                return $result;
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param int $userId
     * @param string $comment
     * @return UpdateResult
     */
    public function decline(int $userId, string $comment)
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowDecline()) {
                $this->setStatus(Model\RequestTable::STATUS_DECLINE)
                    ->setApproverId($userId)
                    ->setDateApprove(new DateTime())
                    ->setApproverComment($comment);

                $result = $this->save();
                if($result->isSuccess()) {
                    Helper::sendNotify(
                        $this->getAuthorId(),
                        str_replace(
                            ['#REQUEST_ID#', '#COMMENT#'],
                            [$this->getId(), $this->getApproverComment()],
                            Loc::getMessage('ITB_FIN.REQUEST.NOTIFY.DECLINE')));
                }
                return $result;
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @return UpdateResult
     */
    public function renew()
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowRenew()) {
                $this->setStatus(Model\RequestTable::STATUS_NEW)
                    ->unsetApproverId()
                    ->unsetDateApprove()
                    ->unsetApproverComment();
                return $this->save();
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @return UpdateResult
     */
    public function confirm()
    {
        $result = new UpdateResult();
        try {
            if($this->isAllowConfirm()) {
                $status = ($this->getStatus() === Model\RequestTable::STATUS_APPROVE) ? Model\RequestTable::STATUS_CONFIRM : Model\RequestTable::STATUS_CANCEL;
                $this->setStatus($status);
                return $this->save();
            } else {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.ACTION_NOT_ALLOW'));
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param Operation $operation
     * @return UpdateResult
     */
    public function fix(Operation $operation)
    {
        $result = new UpdateResult();
        try {
            if($operation->getRequestId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.OPERATION_NOT_BIND'));
            if($operation->getAmount() !== $this->getAmount())
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.AMOUNT_DIFF'));

            if(!$operation->isStockOperation()) {
                if($this->getStatus() === RequestTable::STATUS_CONFIRM && $operation->getStatus() === OperationTable::STATUS_COMMIT) {
                    $status = RequestTable::STATUS_FIX;
                }
                else if($this->getStatus() === RequestTable::STATUS_CONFIRM && $operation->getStatus() === OperationTable::STATUS_DECLINE) {
                    $status = RequestTable::STATUS_CANCEL;
                }
                else if($this->getStatus() === RequestTable::STATUS_FIX && $operation->getStatus() === OperationTable::STATUS_CANCEL) {
                    $status = RequestTable::STATUS_CANCEL;
                }
                else
                    throw new Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.OPERATION_INVALID_STATUS'));

                if(Helper::isStockEnabled()) {
                    $stockOperations = OperationTable::getList([
                        'filter' => [
                            '=REQUEST_ID' => $this->getId(),
                            [
                                'LOGIC' => 'OR',
                                '=SRC_VAULT.TYPE' => VaultTable::TYPE_STOCK,
                                '=DST_VAULT.TYPE' => VaultTable::TYPE_STOCK,
                            ]
                        ]
                    ])->fetchCollection();
                    foreach($stockOperations as $stockOperation) {
                        if($status === RequestTable::STATUS_FIX) {
                            if($stockOperation->getStatus() === OperationTable::STATUS_NEW) {
                                $stockOperation->confirm(0);
                            }
                        } else if($status === RequestTable::STATUS_CANCEL) {
                            if($stockOperation->getStatus() === OperationTable::STATUS_COMMIT) {
                                $stockOperation->rollback(0);
                            } else if($stockOperation->getStatus() === OperationTable::STATUS_NEW) {
                                $stockOperation->decline(0);
                            }
                        }
                    }
                }

                $this->setStatus($status);
                $result = $this->save();
                return $result;
            }
        } catch(Exception $e) {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }
}
