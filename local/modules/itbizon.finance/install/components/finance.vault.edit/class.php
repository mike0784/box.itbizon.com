<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceVaultEdit
 */
class CITBFinanceVaultEdit extends CBitrixComponent
{
    protected $error;
    protected $vault;
    protected $beginHistory;
    protected $endHistory;
    protected $pathToAjax;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.INCLUDE_FIN'));

            $this->beginHistory = (new DateTime())->modify('-1 month');
            $this->endHistory = (new DateTime());
            $this->pathToAjax = $this->GetPath() . '/templates/.default/ajax.php';

            // Get id
            $id = intval($this->arParams['VARIABLES']['ID']);

            // Get vault
            $this->vault = Finance\Model\VaultTable::getByPrimary($id)->fetchObject();
            if(!$this->vault)
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.VAULT_INVALID'));

            if($this->vault->isStock()) {
                $this->vault = null;
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.VAULT_INVALID'));
            }

            if(!Finance\Permission::getInstance()->isAllowVaultView($this->vault)) {
                $this->vault = null;
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.ACCESS_DENIED'));
            }

            if(isset($_REQUEST['DATA'])) {
                if(!Finance\Permission::getInstance()->isAllowVaultEdit($this->vault))
                    throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.ACCESS_DENIED'));

                $this->vault->setName($_REQUEST['DATA']['NAME']);
                $this->vault->setResponsibleId($_REQUEST['DATA']['RESPONSIBLE_ID']);
                $this->vault->setGroupId($_REQUEST['DATA']['GROUP_ID']);
                $this->vault->setHideOnPlanning(isset($_REQUEST['DATA']['VISIBLE']));

                $result = $this->vault->save();
                if(!$result->isSuccess())
                    throw new Exception(implode('; ', $result->getErrorMessages()));

                if($this->vault->isVirtual()) {
                    $this->vault->setBalanceManual(Finance\Utils\Money::toBase(floatval($_REQUEST['DATA']['BALANCE'])));
                }

                LocalRedirect($this->arParams['FOLDER']);
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return Finance\Model\AccessRightTable::getActions();
    }

    /**
     * @return Finance\Vault|null
     */
    public function getVault(): ?Finance\Vault
    {
        return $this->vault;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
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
     * @return mixed
     */
    public function getPathToAjax()
    {
        return $this->pathToAjax;
    }
}
