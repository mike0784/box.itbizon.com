<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Finance\Helper;
use Itbizon\Finance\Model\PeriodTable;
use ItBizon\Finance\Period;
use Itbizon\Finance\Permission;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinancePeriodAdd
 */
class CITBFinancePeriodAdd extends Simple
{
    protected $begin;
    protected $end;
    protected $fixed;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.ERROR.INCLUDE_FINANCE'));

            $this->setRoute($this->arParams['HELPER']);

            $request = Application::getInstance()->getContext()->getRequest();
            $data = $request->getPost('DATA');
            $lastPeriod = PeriodTable::getLast();
            if($lastPeriod) {
                list($begin, $end) = PeriodTable::getNextPeriod();
                $this->fixed = true;
                if($lastPeriod && $lastPeriod->getStatus() !== PeriodTable::STATUS_CLOSED)
                    $this->addError(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.ALREADY_ADD.PERIOD_NOT_CLOSED'));
            } else {
                $begin = new DateTime(isset($data['FROM']) ? $data['FROM'] : date('d.m.Y H:i:s'));
                $end   = PeriodTable::getPeriodEnd($begin);
                $this->fixed = false;
            }
            $this->begin = $begin;
            $this->end   = $end;

            if(!Permission::getInstance()->isAllowPeriodAdd())
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.ALREADY_ADD.ACCESS_DENY'));

            if($request->getPost('save') === 'Y') {
                $period = new Period();
                $period->setDateStart(\Bitrix\Main\Type\DateTime::createFromPhp($begin));
                $period->setDateEnd(\Bitrix\Main\Type\DateTime::createFromPhp($end));
                if(Helper::isStockEnabled()) {
                    $period->setStatus(PeriodTable::STATUS_DISTRIBUTION_PROCEEDS);
                } else {
                    $period->setStatus(PeriodTable::STATUS_ALLOCATION_COSTS);
                }
                $result = $period->save();
                if(!$result->isSuccess()) {
                    throw new Exception(implode(", ", $result->getErrorMessages()));
                }

                $uri = new Uri($this->getRoute()->getUrl('add'));
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
    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }

    /**
     * @return mixed
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    /**
     * @return mixed
     */
    public function isFixed()
    {
        return $this->fixed;
    }
}
