<?php

use \Bitrix\Main\Loader;

class ITBFieldUserSelect extends CBitrixComponent
{

    public $currentUser;
    public $fieldName;
    public $fieldId;
    public $title;
    public $status;


    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {

        $this->currentUser = $this->arParams['CURRENT_USER'];
        $this->fieldId = $this->arParams['FIELD_ID'];
        $this->fieldName = $this->arParams['FIELD_NAME'];
        $this->title = $this->arParams['TITLE'];
        $this->status = isset($this->arParams['CHANGE_ACTIVE']) ? $this->arParams['CHANGE_ACTIVE'] : true;

        //Include template
        $this->IncludeComponentTemplate();

        return true;
    }
}