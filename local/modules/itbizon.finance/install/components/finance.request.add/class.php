<?php

use Bitrix\Crm\ContactTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\CompanyTable;
use Bitrix\Main\Engine\CurrentUser;

use Itbizon\Finance\Model\CategoryBindTable;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Request;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Utils\Money;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * Class CITBRequestAdd
 */
class CITBRequestAdd extends CBitrixComponent
{
    protected $error;
    protected $category;
    protected $company;
    protected $data;
    
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
                    [
                        'LOGIC'=>'OR',
                        '!=COMPANY_ID'=>0,
                        '!=CONTACT_ID'=>0,
                    ],
                    'CLOSED'=>'N',
                ],
                'select'=>[
                    'COMPANY_ID',
                    'CONTACT_ID',
                ]
            ]);
            $companyId = [];
            $contactId = [];
            while($row = $list->fetch()) {
                if($row['COMPANY_ID'])
                    $companyId[$row['COMPANY_ID']] = $row['COMPANY_ID'];
                if($row['CONTACT_ID'])
                    $contactId[$row['CONTACT_ID']] = $row['CONTACT_ID'];
            }
            
            $list = ContactTable::getList([
                'filter'=>[
                    '=ID'=>$contactId,
                    '!=COMPANY_ID'=>0,
                ],
                'select'=>[
                    'COMPANY_ID',
                ],
            ]);
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
            
            if(isset($_REQUEST['DATA']))
            {
                $data = $_REQUEST['DATA'];
                $request = new Request();
                $request->setAuthorId(CurrentUser::get()->getId());
                $request->setName(strval($data['NAME']));
                $request->setCategoryId(intval($data['CATEGORY']));
                $request->setFloatAmount(floatval($data['AMOUNT']));
                $request->setCommentSituation(strval($data['COMMENT_SITUATION']));
                $request->setCommentData(strval($data['COMMENT_DATA']));
                $request->setEntityType(CCrmOwnerType::Company);
                $request->setEntityId(intval($data['COMPANY']));

                if(isset($_FILES['FILE']) && is_array($_FILES['FILE']) && !empty($_FILES['FILE']) && $_FILES['FILE']['tmp_name'] && $_FILES['FILE']['error'] == 0) {
                    $file = $_FILES['FILE'];
                    $file['MODULE_ID'] = 'itbizon.finance';
                    $file['old_file'] = '';
                    $file['description'] = '';
                    $file['del'] = '';
                    $fileId = CFile::SaveFile($file, 'itbizon.finance');
                    if(!$fileId) {
                        throw new Exception('Ошибка загрузки файла');
                    }
                    $request->setFileId($fileId);
                }

                $bind = CategoryBindTable::getById($request->getCategoryId())->fetchObject();
                if($bind) {
                    $request->setStockId($bind->getStockId());
                }

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
                        if($request->getFileId() > 0) {
                            CFile::Delete($request->getFileId());
                        }
                        throw new Exception(implode(", ", $result->getErrorMessages()));
                    }
                } else {
                    throw new Exception(Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.ADD.ERROR.CREATE_FAILED"));
                }
            }
            if(isset($_GET['TEMPLATE_ID']))
            {
                $request = RequestTable::getById($_GET['TEMPLATE_ID'])->fetchObject();
                $this->data['NAME'] = $request->getName();
                $this->data['CATEGORY'] = $request->getCategoryId();
                $this->data['AMOUNT'] = Money::fromBase($request->getAmount());
                $this->data['COMMENT_SITUATION'] = $request->getCommentSituation();
                $this->data['COMMENT_DATA'] = $request->getCommentData();
                $this->data['COMMENT_SOLUTION'] = $request->getCommentSolution();
                $this->data['COMPANY'] = $request->getEntityId();
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
    public function getData()
    {
        return $this->data;
    }
}