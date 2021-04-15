<?php

use Bitrix\Main\Application;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Finance\Model\EO_Operation_Collection;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Utils\Money;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinancePeriodIncome
 */
class CITBFinancePeriodIncome extends Simple
{
    protected $options;
    protected $operations;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.ERROR.INCLUDE_FINANCE'));

            $this->setRoute($this->arParams['HELPER']);

            if(!Permission::getInstance()->isAllowPeriodEdit())
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.ERROR.ACCESS_DENY'));

            $periodId = intval($this->getRoute()->getVariable('ID'));
            $period = PeriodTable::getById($periodId)->fetchObject();
            if(!$period) {
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.ERROR.PERIOD_NOT_FOUND'));
            }

            if($period->getStatus() !== PeriodTable::STATUS_DISTRIBUTION_PROCEEDS)
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.INCOME.ERROR.PERIOD_ALREADY_DISTRIBUTED'));

            $this->operations = $period->getIncomeOperation();

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');
                $period->distribute(CurrentUser::get()->getId(), [0 => Money::toBase($data['VALUE'])]);
                $uri = new Uri($this->getRoute()->getUrl('income', ['ID' => $periodId]));
                if($this->getRoute()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }

        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @return mixed
     */
    public function getOperations(): ?EO_Operation_Collection
    {
        return $this->operations;
    }
}
