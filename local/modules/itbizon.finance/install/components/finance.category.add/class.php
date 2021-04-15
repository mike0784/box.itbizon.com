<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Itbizon\Finance\OperationCategory;
use Itbizon\Finance\Permission;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceCategoryAdd
 */
class CITBFinanceCategoryAdd extends CBitrixComponent
{
    public $error;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        $this->error = null;
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_ADD.ERROR.INCLUDE_FIN'));

            if (!Permission::getInstance()->isAllowCategoryAdd())
                throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_ADD.ERROR.ACCESS_DENY'));

            if (isset($_REQUEST['DATA'])) {
                $val = new OperationCategory();
                $val->setName($_REQUEST['DATA']['NAME']);
                $val->setAllowIncome(isset($_REQUEST['DATA']['ALLOW_INCOME']));
                $val->setAllowOutgo(isset($_REQUEST['DATA']['ALLOW_OUTGO']));
                $val->setAllowTransfer(isset($_REQUEST['DATA']['ALLOW_TRANSFER']));

                /** @var AddResult $result */
                $result = $val->save();

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
