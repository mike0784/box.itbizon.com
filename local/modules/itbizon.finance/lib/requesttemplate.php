<?php

namespace Itbizon\Finance;

use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Utils\Money;

Loc::loadMessages(__FILE__);

/**
 * Class Request
 * @package Itbizon\Finance
 */
class RequestTemplate extends Model\EO_RequestTemplate
{
    /**
     * @param float $amount
     * @return RequestTemplate
     */
    public function setFloatAmount(float $amount)
    {
        return parent::setAmount(Money::toBase($amount));
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
     * @return \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult
     */
    public function createRequest()
    {
        $request = new Request();
        $request->setAuthorId($this->getAuthorId());
        $request->setStatus(RequestTable::STATUS_NEW);
        $request->setName($this->getName());
        $request->setCategoryId($this->getCategoryId());
        $request->setAmount($this->getAmount());
        $request->setCommentSituation($this->getCommentSituation());
        $request->setCommentData($this->getCommentData());
        $request->setCommentSolution($this->getCommentSolution());
        $request->setEntityType($this->getEntityType());
        $request->setEntityId($this->getEntityId());
        $request->setVaultId($this->getVaultId());
        return $request->save();
    }
}
