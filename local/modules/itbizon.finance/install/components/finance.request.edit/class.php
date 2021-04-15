<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

use Bitrix\Main\UserTable;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\RequestTable;
use Bitrix\Main\Engine\CurrentUser;
use Itbizon\Finance\Request;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * Class CITBRequestEdit
 */
class CITBRequestEdit extends CBitrixComponent
{
    protected $error;
    protected $request;
    protected $category;
    protected $users;
    
    /**
     * @return bool|mixed|null
     * @throws \Bitrix\Main\LoaderException
     * @throws Exception
     */
    public function executeComponent()
    {
        try
        {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.ADD.ERROR.INCLUDE_FINANCE'));
    
            $request = RequestTable::getById($this->arParams['VARIABLES']['ID'])->fetchObject();
            $currUser = CurrentUser::get();
            if($currUser->getId() != $request->getAuthorId() && !$currUser->isAdmin())
            {
                echo '<div class="alert alert-danger">'.Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.EDIT.ERROR.ACCESS").'</div>';
                die();
            }
            
            $list = OperationCategoryTable::getList([
                'filter'=>[
                    '=ALLOW_OUTGO'=>true,
                ],
                'select'=>[
                    'ID',
                    'NAME',
                ]
            ]);
            while($row = $list->fetch())
                $this->category[$row['ID']] = $row['NAME'];
    
            $list = UserTable::getList([
                'filter'=>[
                    'ACTIVE'=>'Y',
                ],
                'select'=>[
                    'ID',
                    'NAME',
                    'LAST_NAME',
                ]
            ]);
            while($row = $list->fetch())
                $this->users[$row['ID']] = $row['LAST_NAME'].' '.$row['NAME'];
            
            $this->request = RequestTable::getByPrimary($this->arParams['VARIABLES']['ID'], [
                'select'=>[
                    '*',
                    'LEAD',
                    'DEAL',
                    'CONTACT',
                    'COMPANY',
                ]
            ])->fetchObject();
            
            if($this->request->getAuthorId() != CurrentUser::get()->getId() && !CurrentUser::get()->isAdmin())
                throw new Exception('У вас нет доступа к заявке');
            
        } catch (Exception $ex)
        {
            $this->error = $ex->getMessage();
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
    
    public function getRequest(): ?Request
    {
        return $this->request;
    }
    
    public function getCategory()
    {
        return $this->category;
    }
    
    public function getUsers()
    {
        return $this->users;
    }
}