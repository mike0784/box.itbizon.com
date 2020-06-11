<?php

use \Bitrix\Main\Loader;

class ITBFieldUserSelect extends CBitrixComponent
{
    public $currentUser;
    public $fieldName;
    public $fieldId;
    public $title;


    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {

        $this->currentUser = $this->arParams['CURRENT_USER'];
        $this->fieldId = $this->arParams['FIELD_ID'];
        $this->fieldName = $this->arParams['FIELD_NAME'];
        $this->title = $this->arParams['TITLE'];

        //Include template
        $this->IncludeComponentTemplate();

        return true;
    }
}