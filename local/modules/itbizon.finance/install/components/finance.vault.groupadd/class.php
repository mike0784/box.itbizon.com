<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Permission;
use Itbizon\Finance\VaultGroup;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceVaultAdd
 */
class CITBFinanceVaultGroupAdd extends CBitrixComponent
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
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.ERROR.INCLUDE_FIN'));

            if (!Permission::getInstance()->isAllowVaultGroupAdd())
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.ERROR.ACCESS_DENY'));

            if (isset($_REQUEST['DATA'])) {
                $group = new VaultGroup();
                $group->setName($_REQUEST['DATA']['NAME']);
                $result = $group->save();

                if ($result->isSuccess()) {
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
