<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\CompanyTable;
use Itbizon\Finance\Model\RequestTemplateTable;

use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\VaultTable;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Utils\Money;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * Class CITBRequestAdd
 */
class CITBRequestTemplateEdit extends CBitrixComponent
{
    protected $error;
    protected $category;
    protected $company;
    protected $vault;
    
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
                throw new Exception(Loc::getMessage('ITB_FIN.REQ_TEMPLATE.EDIT.ERROR.INCLUDE_FINANCE'));
    
            $requestId = $this->arParams['VARIABLES']['ID'];
            $request = RequestTemplateTable::getById($requestId)->fetchObject();
            
            $permission = Permission::getInstance();
            if(!$permission->isAllowRequestTemplateEdit($request))
            {
                echo '<div class="alert alert-danger">'.Loc::getMessage("ITB_FIN.REQ_TEMPLATE.EDIT.ERROR.ACCESS").'</div>';
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
            
            $list = DealTable::getList([
                'filter'=>[
                    '!=COMPANY_ID'=>0,
                    'CLOSED'=>'N',
                ],
                'select'=>[
                    'COMPANY_ID',
                ]
            ]);
            $companyId = [];
            while($row = $list->fetch())
                $companyId[$row['COMPANY_ID']] = $row['COMPANY_ID'];
            
            $list = CompanyTable::getList([
                'filter'=>[
                    '=ID'=>$companyId,
                ],
                'select'=>[
                    'ID',
                    'TITLE',
                ],
            ]);
            while($row = $list->fetch())
                $this->company[$row['ID']] = $row['TITLE'];

            $list = VaultTable::getList([
                'select' => [
                    'ID',
                    'NAME'
                ],
                'filter' => [
                    '!=TYPE' => [VaultTable::TYPE_VIRTUAL, VaultTable::TYPE_STOCK]
                ]
            ]);
            while($row = $list->fetch())
                $this->vault[$row['ID']] = $row['NAME'];
            
            if(isset($_REQUEST['DATA']))
            {
                $data = $_REQUEST['DATA'];
                
                $request->setName(strval($data['NAME']));
                $request->setCategoryId(intval($data['CATEGORY']));
                $request->setFloatAmount(floatval($data['AMOUNT']));
                $request->setActive(strval($data['ACTIVE']));
                $request->setCommentSituation(strval($data['COMMENT_SITUATION']));
                $request->setCommentData(strval($data['COMMENT_DATA']));
                $request->setCommentSolution(strval($data['COMMENT_SOLUTION']));
                $request->setEntityId(intval($data['COMPANY']));
                $request->setVaultId(intval($data['VAULT_ID']));
                
                $result = $request->save();
                
                if (isset($result)) {
                    if (!$result->isSuccess())
                        throw new Exception(implode(", ", $result->getErrorMessages()));
                    
                    ?><script>window.parent.postMessage('resetGrid', '*');</script><?
                }
                else
                    throw new Exception(Loc::getMessage("ITB_FIN.REQ_TEMPLATE.EDIT.ERROR.CREATE_FAILED"));
            }
            else
            {
                $data['NAME'] = $request->getName();
                $data['CATEGORY'] = $request->getCategoryId();
                $data['AMOUNT'] = Money::fromBase($request->getAmount());
                $data['ACTIVE'] = $request->getActive() ? 'Y' : 'N';
                $data['COMMENT_SITUATION'] = $request->getCommentSituation();
                $data['COMMENT_DATA'] = $request->getCommentData();
                $data['COMMENT_SOLUTION'] = $request->getCommentSolution();
                $data['COMPANY'] = $request->getEntityId();
                $data['VAULT_ID'] = $request->getVaultId();
                $_REQUEST['DATA'] = $data;
            }
            
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
    
    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    /**
     * @return mixed
     */
    public function getVault()
    {
        return $this->vault;
    }
}