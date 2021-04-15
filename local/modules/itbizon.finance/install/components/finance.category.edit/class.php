<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\UpdateResult;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceCategoryEdit
 */
class CITBFinanceCategoryEdit extends CBitrixComponent
{
    protected $error;
    protected $category = null;

    /**
     * @return bool|mixed|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_EDIT.ERROR.INCLUDE_FIN'));

            //Get id
            $id = intval($this->arParams['VARIABLES']['ID']);

            //Get category
            $this->category = Finance\Model\OperationCategoryTable::getByPrimary($id)->fetchObject();
            if (!$this->category)
                throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_EDIT.ERROR.INVALID_CATEGORY'));
            if (!Finance\Permission::getInstance()->isAllowCategoryView($this->category)) {
                $this->category = null;
                throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_EDIT.ERROR.ACCESS_DENY'));
            }

            if (isset($_REQUEST['DATA'])) {
                if (!Finance\Permission::getInstance()->isAllowCategoryEdit($this->category))
                    throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_EDIT.ERROR.ACCESS_DENY'));

                $this->category->setName($_REQUEST['DATA']['NAME']);
                $this->category->setAllowIncome(isset($_REQUEST['DATA']['ALLOW_INCOME']));
                $this->category->setAllowOutgo(isset($_REQUEST['DATA']['ALLOW_OUTGO']));
                $this->category->setAllowTransfer(isset($_REQUEST['DATA']['ALLOW_TRANSFER']));

                /** @var UpdateResult $result */
                $result = $this->category->save();

                if (!$result->isSuccess())
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                LocalRedirect($this->arParams['FOLDER']);
            }
            $this->arResult['pathToAjax'] = $this->GetPath() . '/templates/.default/ajax.php';
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @return Finance\OperationCategory||null
     */
    public function getCategory(): ?Finance\OperationCategory
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
