<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\CompanyTable;
use Bitrix\Main\Engine\CurrentUser;

use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\RequestTemplate;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Model\VaultTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * Class CITBRequestTemplateAdd
 */
class CITBRequestTemplateAdd extends CBitrixComponent
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
                throw new Exception(Loc::getMessage('ITB_FIN.REQ_TEMPLATE.ADD.ERROR.INCLUDE_FINANCE'));
    
            $permission = Permission::getInstance();
            if(!$permission->isAllowRequestTemplateAdd())
            {
                echo '<div class="alert alert-danger">'.Loc::getMessage("ITB_FIN.REQ_TEMPLATE.ADD.ERROR.ACCESS").'</div>';
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
                $request = new RequestTemplate();
                $request->setAuthorId(CurrentUser::get()->getId());
                $request->setName(strval($data['NAME']));
                $request->setCategoryId(intval($data['CATEGORY']));
                $request->setFloatAmount(floatval($data['AMOUNT']));
                $request->setActive(strval($data['ACTIVE']));
                $request->setCommentSituation(strval($data['COMMENT_SITUATION']));
                $request->setCommentData(strval($data['COMMENT_DATA']));
                $request->setCommentSolution(strval($data['COMMENT_SOLUTION']));
                $request->setEntityType(CCrmOwnerType::Company);
                $request->setEntityId(intval($data['COMPANY']));
                $request->setVaultId(intval($data['VAULT_ID']));
                
                $result = $request->save();
                
                if (isset($result)) {
                    if ($result->isSuccess()) {
                        ?><script>window.parent.postMessage('resetGrid', '*');</script><?
                        if ($_REQUEST['IFRAME'] == 'Y' && $_REQUEST['IFRAME_TYPE'] == 'SIDE_SLIDER') {
                            $this->actionSuccessAjax();
                            die();
                        }
                        LocalRedirect($this->arParams['FOLDER'] . str_replace("#ID#", $result->getId(), $this->arParams['TEMPLATE_ADD']));
                        die();
                    } else {
                        throw new Exception(implode(", ", $result->getErrorMessages()));
                    }
                } else {
                    throw new Exception(Loc::getMessage("ITB_FIN.REQ_TEMPLATE.ADD.ERROR.CREATE_FAILED"));
                }
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
     *
     */
    private function actionSuccessAjax()
    {
        echo "<script>BX.SidePanel.Instance.close()</script>";
    }
    
    /**
     * @return mixed
     */
    public function getVault()
    {
        return $this->vault;
    }
}