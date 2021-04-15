<?php

namespace Itbizon\Finance;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\Result;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\SystemException;
use DateTime;
use Exception;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\VaultTable;

Loc::loadMessages(__FILE__);

/**
 * Class Vault
 * @package Itbizon\Finance
 */
class Vault extends Model\EO_Vault
{
    /**
     * @param Operation $operation
     * @return bool
     * @throws Exception
     */
    public function commit(Operation $operation)
    {
        if (!$operation->getId())
            return false;
        if ($this->getType() === VaultTable::TYPE_VIRTUAL)
            throw new Exception(Loc::getMessage('ITB_FIN.VAULT.OPERATION_VIRTUAL_DENY'));

        $delta = abs($operation->getAmount());
        if ($operation->getType() === Model\OperationTable::TYPE_INCOME) {
            if ($operation->getDstVaultId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.INVALID_DST'));
        } elseif ($operation->getType() === Model\OperationTable::TYPE_OUTGO) {
            if ($operation->getSrcVaultId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.INVALID_SRC'));
            $delta = -$delta;
        } elseif ($operation->getType() === Model\OperationTable::TYPE_TRANSFER) {
            if ($operation->getSrcVaultId() === $this->getId())
                $delta = -$delta;
            elseif ($operation->getDstVaultId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.INVALID_DST'));
        }

        $newBalance = $this->getBalance() + $delta;
        $this->setBalance($newBalance);
        $result = $this->save();
        if ($result->isSuccess()) {
            $this->saveHistory($operation->getId(), Loc::getMessage('ITB_FIN.VAULT.OPERATION_COMMIT'));
            return true;
        }
        $this->resetBalance();
        return false;
    }

    /**
     * @param Operation $operation
     * @return bool
     * @throws Exception
     */
    public function rollback(Operation $operation)
    {
        if (!$operation->getId())
            return false;
        if ($this->getType() === VaultTable::TYPE_VIRTUAL)
            throw new Exception(Loc::getMessage('ITB_FIN.VAULT.OPERATION_VIRTUAL_DENY'));

        $delta = -abs($operation->getAmount());
        if ($operation->getType() === Model\OperationTable::TYPE_INCOME) {
            if ($operation->getDstVaultId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.INVALID_DST'));
        } elseif ($operation->getType() === Model\OperationTable::TYPE_OUTGO) {
            if ($operation->getSrcVaultId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.INVALID_SRC'));
            $delta = abs($delta);
        } elseif ($operation->getType() === Model\OperationTable::TYPE_TRANSFER) {
            if ($operation->getSrcVaultId() === $this->getId())
                $delta = abs($delta);
            else if ($operation->getDstVaultId() !== $this->getId())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT.INVALID_DST'));
        }

        $newBalance = $this->getBalance() + $delta;
        $this->setBalance($newBalance);
        $result = $this->save();
        if ($result->isSuccess()) {
            $this->saveHistory($operation->getId(), Loc::getMessage('ITB_FIN.VAULT.OPERATION_ROLLBACK'));
            return true;
        }
        $this->resetBalance();
        return false;
    }

    /**
     * @param int $balance
     * @param string $comment
     * @return bool
     */
    public function setBalanceManual(int $balance, string $comment = ''): bool
    {
        $this->setBalance($balance);
        $result = $this->save();
        if($result->isSuccess()) {
            $this->saveHistory(0, empty($comment) ? Loc::getMessage('ITB_FIN.VAULT.MANUAL_CHANGE') : $comment);
            return true;
        }
        return false;
    }

    /**
     * @param $operationId
     * @param string $comment
     * @return bool
     */
    protected function saveHistory($operationId, $comment = '')
    {
        $history = new VaultHistory();
        $history->setBalance($this->getBalance());
        $history->setVaultId($this->getId());
        $history->setOperationId($operationId);
        $history->setComment($comment);
        $result = $history->save();
        return $result->isSuccess();
    }

    /**
     * @return array|string
     */
    public function getTypeName()
    {
        return Model\VaultTable::getTypes($this->getType());
    }

    /**
     * @return string
     */
    public function getBalancePrint()
    {
        return sprintf('%.2f', $this->getBalance() / 100.);
    }

    /**
     * @return string
     */
    public function getResponsibleName()
    {
        $user = $this->getResponsible();
        if ($user)
            return $user->getLastName() . ' ' . $user->getName();
        else
            return '';
    }

    /**
     * @return string
     */
    public function getResponsibleUrl()
    {
        $user = $this->getResponsible();
        if ($user)
            return '/company/personal/user/' . $user->getId() . '/';
        else
            return '/company/personal/user/' . $this->getResponsibleId() . '/';
    }

    /**
     * @param DateTime $begin
     * @param DateTime $end
     * @return VaultHistory[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function loadHistory(DateTime $begin, DateTime $end)
    {
        $records = [];
        $result = Model\VaultHistoryTable::getList([
            'select' => [
                '*',
                'OPERATION.NAME'
            ],
            'filter' => [
                'VAULT_ID' => $this->getId(),
                '>=DATE_CREATE' => $begin->format('d.m.Y H:i:s'),
                '<=DATE_CREATE' => $end->format('d.m.Y H:i:s'),
            ],
            'order' => ['DATE_CREATE' => 'DESC']
        ]);
        while ($record = $result->fetchObject())
            $records[] = $record;
        return $records;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if($this->getType() !== VaultTable::TYPE_STOCK) {
            return '/finance/vault/edit/' . $this->getId() . '/';
        } else {
            return '/finance/stock/edit/' . $this->getId() . '/';
        }
    }

    /**
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getLockBalance(): int
    {
        $balance = 0;
        $result  = RequestTable::getList([
            'select' => ['ID', 'STATUS', 'AMOUNT'],
            'filter' => ['=VAULT_ID' => $this->getId(), '=STATUS' => [RequestTable::STATUS_APPROVE, RequestTable::STATUS_CONFIRM]]
        ]);
        while($request = $result->fetchObject()) {
            $balance += $request->getAmount();
        }
        return $balance;
    }

    /**
     * @return bool
     */
    public function isVirtual(): bool
    {
        return ($this->getType() === VaultTable::TYPE_VIRTUAL);
    }

    /**
     * @return bool
     */
    public function isStock(): bool
    {
        return ($this->getType() === VaultTable::TYPE_STOCK);
    }
}