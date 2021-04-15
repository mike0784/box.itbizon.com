<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Vault;
use Itbizon\Finance\VaultHistory;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceVaultAdd
 */
class CITBFinanceVaultAdd extends CBitrixComponent
{
    public $userId = null;
    public $error;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            $this->userId = CurrentUser::get()->getId();

            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_ADD.ERROR.INCLUDE_FIN'));

            if (!Permission::getInstance()->isAllowVaultAdd())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_ADD.ERROR.ACCESS_DENY'));

            if (isset($_REQUEST['DATA'])) {
                $val = new Vault();
                $val->setName($_REQUEST['DATA']['NAME']);
                $val->setType($_REQUEST['DATA']['TYPE']);
                $val->setResponsibleId($_REQUEST['DATA']['RESPONSIBLE_ID']);
                $val->setHideOnPlanning(isset($_REQUEST['DATA']['VISIBLE']));
                // Переводится в копейки
                //$val->setBalance($_REQUEST['DATA']['BALANCE'] * 100);
                $val->setGroupId($_REQUEST['DATA']['GROUP_ID']);
                $result = $val->save();

                if ($result->isSuccess()) {
                    $val->setBalanceManual($_REQUEST['DATA']['BALANCE'] * 100, Loc::getMessage('ITB_FIN.VAULT_ADD.VAULT_CREATE'));

                    LocalRedirect($this->arParams['FOLDER']);
                    die();
                } else {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
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
}
