<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Itbizon\Scratch\Box;


Loc::loadMessages(__FILE__);

/**
 * Class CITBScratchBoxAdd
 */
class CITBScratchBoxAdd extends CBitrixComponent
{
    public $error;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        $this->error = null;
        try {
            if (!Loader::includeModule('itbizon.ыскфеср'))
                throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_ADD.ERROR.INCLUDE'));

            if (isset($_REQUEST['DATA'])) {
                $val = new Box();
                $val->setTitle($_REQUEST['DATA']['TITLE']);
                $val->setAmount(isset($_REQUEST['DATA']['AMOUNT']));
                $val->setCount(isset($_REQUEST['DATA']['Count']));
                $val->setComment(isset($_REQUEST['DATA']['COMMENT']));

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
