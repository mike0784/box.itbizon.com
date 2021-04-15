<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceVaultEdit
 */
class CITBFinanceVaultGroupEdit extends CBitrixComponent
{
    protected $error;
    protected $group;
    protected $pathToAjax;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.ERROR.INCLUDE_FIN'));

            $this->pathToAjax = $this->GetPath() . '/templates/.default/ajax.php';

            //Get id
            $id = intval($this->arParams['VARIABLES']['ID']);

            //Get vault
            $this->group = Finance\Model\VaultGroupTable::getByPrimary($id)->fetchObject();
            if (!$this->group)
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.ERROR.GROUP_INVALID'));

            if (!Finance\Permission::getInstance()->isAllowVaultGroupView($this->group)) {
                $this->group = null;
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.ERROR.ACCESS_DENIED'));
            }

            if (isset($_REQUEST['DATA'])) {
                if (!Finance\Permission::getInstance()->isAllowVaultGroupEdit($this->group))
                    throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.ACCESS_DENIED'));

                $this->group->setName($_REQUEST['DATA']['NAME']);
                $result = $this->group->save();

                if (!$result->isSuccess())
                    throw new Exception(implode('; ', $result->getErrorMessages()));

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
     * @return Finance\VaultGroup|null
     */
    public function getGroup(): ?Finance\VaultGroup
    {
        return $this->group;
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
    public function getPathToAjax()
    {
        return $this->pathToAjax;
    }
}
