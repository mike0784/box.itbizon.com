<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\SystemException;
use Itbizon\Finance;
use Itbizon\Finance\Model\OperationTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceOperationEdit
 */
class CITBFinanceOperationEdit extends CBitrixComponent
{
    protected $operation;
    protected $error;
    protected $beginHistory;
    protected $endHistory;

    /**
     * @return bool|mixed|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.INCLUDE_FIN'));

            if (!Loader::IncludeModule('crm'))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.INCLUDE_CRM'));

            if (!Loader::IncludeModule('iblock'))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.INCLUDE_IBLOCK'));

            //Get id
            $id = intval($this->arParams['VARIABLES']['ID']);

            $this->operation = Finance\Model\OperationTable::getById($id)->fetchObject();
            if (!$this->operation)
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.LOAD_FAILED'));

            if (!Finance\Permission::getInstance()->isAllowOperationView($this->operation)) {
                $this->operation = null;
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.ACCESS_DENY'));
            }

            $this->beginHistory = DateTime::createFromFormat('d.m.Y H:i:s', $this->operation->getDateCreate());
            $this->endHistory = new DateTime();

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                if (!Finance\Permission::getInstance()->isAllowOperationEdit($this->operation))
                    throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.ACCESS_DENY'));

                $data = $request->getPost('DATA');

                $this->operation
                    ->setName($data['NAME'])
                    ->setComment($data['COMMENT'])
                    ->setResponsibleId($data['RESPONSIBLE_ID']);
                if($this->operation->getStatus() === Finance\Model\OperationTable::STATUS_NEW) {
                    $this->operation
                        ->setAmount(Finance\Utils\Money::toBase($data['AMOUNT']))
                        ->setCategoryId($data['CATEGORY_ID'])
                        ->setEntityTypeId($data['ENTITY_TYPE_ID'])
                        ->setEntityId($data['ENTITY_ID']);
                    if(isset($data['SRC_VAULT_ID'])) {
                        $this->operation->setSrcVaultId($data['SRC_VAULT_ID']);
                    }
                    if(isset($data['DST_VAULT_ID'])) {
                        $this->operation->setDstVaultId($data['DST_VAULT_ID']);
                    }
                }
                $result = $this->operation->save();
                if(!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }

                if($this->operation->getRequestId() > 0) {
                    $this->operation->fillRequest();
                    if($this->operation->getRequest()) {
                        $this->operation->getRequest()->setAmount($this->operation->getAmount());
                        $result = $this->operation->getRequest()->save();
                        if(!$result->isSuccess()) {
                            throw new Exception(implode('; ', $result->getErrorMessages()));
                        }
                    }

                    $bindOperations = OperationTable::getList([
                        'filter' => [
                            '!=ID' => $this->operation->getId(),
                            '=REQUEST_ID' => $this->operation->getRequestId(),
                        ],
                        'limit' => 1
                    ])->fetchCollection();
                    foreach($bindOperations as $operation) {
                        $operation->setAmount($this->operation->getAmount());
                        $result = $operation->save();
                        if(!$result->isSuccess()) {
                            throw new Exception(implode('; ', $result->getErrorMessages()));
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return Finance\Operation|null
     */
    public function getOperation(): ?Finance\Operation
    {
        return $this->operation;
    }

    /**
     * @return mixed
     */
    public function getBeginHistory()
    {
        return $this->beginHistory;
    }

    /**
     * @return mixed
     */
    public function getEndHistory()
    {
        return $this->endHistory;
    }

    /**
     * @return array
     */
    public function getCrmList()
    {
        return [
            CCrmOwnerType::Lead => CCrmOwnerType::GetDescription(CCrmOwnerType::Lead),
            CCrmOwnerType::Deal => CCrmOwnerType::GetDescription(CCrmOwnerType::Deal),
            CCrmOwnerType::Company => CCrmOwnerType::GetDescription(CCrmOwnerType::Company),
            CCrmOwnerType::Contact => CCrmOwnerType::GetDescription(CCrmOwnerType::Contact),
        ];
    }

    /**
     * @throws SystemException
     */
    public function getUserFields()
    {
        static $fields = null;
        if (!$fields) {
            $fields = (new \CUserTypeManager())->GetUserFields(Finance\Model\OperationTable::getUfId(), 0, Bitrix\Main\Application::getInstance()->getContext()->getLanguage());
        }
        return $fields;
    }

    /**
     * @return string
     */
    public function getPathToAjax()
    {
        return $this->GetPath() . '/templates/.default/ajax.php';
    }

    /**
     * @return Finance\Model\EO_Vault_Collection|null
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public function getVaults(): ?Finance\Model\EO_Vault_Collection
    {
        static $vaults = null;
        if($vaults === null) {
            $vaults = Finance\Model\VaultTable::getList([
                'select' => [
                    'ID', 'NAME'
                ],
                'filter' => [
                    '!=TYPE' => [Finance\Model\VaultTable::TYPE_VIRTUAL, Finance\Model\VaultTable::TYPE_STOCK]
                ],
                'order' => ['NAME' => 'ASC']
            ])->fetchCollection();
        }
        return $vaults;
    }

    /**
     * @param int $type
     * @return Collection|Finance\Model\EO_OperationCategory_Collection|null
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public function getCategories(int $type)
    {
        static $categories = null;
        if($categories === null) {
            $filter = [];
            if($type === Finance\Model\OperationTable::TYPE_INCOME) {
                $filter['ALLOW_INCOME'] = 'Y';
            } else if($type === Finance\Model\OperationTable::TYPE_OUTGO) {
                $filter['ALLOW_OUTGO'] = 'Y';
            } else if($type === Finance\Model\OperationTable::TYPE_TRANSFER) {
                $filter['ALLOW_TRANSFER'] = 'Y';
            }
            $categories = Finance\Model\OperationCategoryTable::getList([
                'select' => ['ID', 'NAME'],
                'filter' => $filter,
                'order' => ['NAME' => 'ASC']
            ])->fetchCollection();
        }
        return $categories;
    }
}
