<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\UpdateResult;
use Itbizon\Scratch;

Loc::loadMessages(__FILE__);

/**
 * Class CITBScratchBoxEdit
 */
class CITBScratchBoxEdit extends CBitrixComponent
{
    protected $error;
    protected $box = null;

    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.scratch'))
                throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_EDIT.ERROR.INCLUDE'));

            //Get id
            $id = intval($this->arParams['VARIABLES']['ID']);

            //Get box object
	        $this->box = Scratch\Model\BoxTable::getByPrimary($id)->fetchObject();
	        if (!$this->box)
		        throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_EDIT.ERROR.INVALID_EDIT_ID'));

	        //Get Title
	        $title = intval($this->arParams['VARIABLES']['TITLE']);
            if (empty($title))
		        throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_EDIT.ERROR.INVALID_TITLE'));


            if (isset($_REQUEST['DATA'])) {

                $this->box->setTitle($_REQUEST['DATA']['TITLE']);
                $this->box->setAmount(isset($_REQUEST['DATA']['AMOUNT']));
                $this->box->setCount(isset($_REQUEST['DATA']['COUNT']));
                $this->box->setComment(isset($_REQUEST['DATA']['COMMENT']));

                /** @var UpdateResult $result */
                $result = $this->box->save();

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

    public function getError()
    {
        return $this->error;
    }
}
