<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\UpdateResult;
use Itbizon\Scratch;
use Itbizon\Scratch\Model\BoxTable;


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

            // debug
	        echo "\n<br>DEBUG 11111<pre>";
	        print_r($this->arParams['VARIABLES']);
	        echo "</pre><br>\n";

            //Get id
            $id = intval($this->arParams['VARIABLES']['ID']);

            //Get box object
	        $this->box = Scratch\Model\BoxTable::getByPrimary($id)->fetchObject();
	        if (!$this->box)
		        throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_EDIT.ERROR.INVALID_EDIT_ID'));

	        /*  debug
	        echo "\n<br>DEBUG 22222<pre>";
	        echo "AMOUNT=".$this->box['AMOUNT']."\n";
	        echo "COUNT=".$this->box['COUNT']."\n";
	        echo "AMOUNT=".($this->box->getAmount())."\n";
	        echo "COUNT=".($this->box->getCount())."\n";
	        print_r($this->box);
	        echo "</pre><br>\n";
	        // */

	        /*
	        //Get Title
	        $title = intval($this->arParams['VARIABLES']['TITLE']);
            if (empty($title))
		        throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_EDIT.ERROR.INVALID_TITLE'));
			// */

            if (isset($_REQUEST['DATA'])) {

                $this->box->setTitle($_REQUEST['DATA']['TITLE']);
                $this->box->setAmount($_REQUEST['DATA']['AMOUNT']);
                $this->box->setCount($_REQUEST['DATA']['COUNT']);
                $this->box->setComment($_REQUEST['DATA']['COMMENT']);

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

	public function getBox(): ?Itbizon\Scratch\Box
	{
		return $this->box;
	}

}
