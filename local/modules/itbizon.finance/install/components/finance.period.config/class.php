<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Permission;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinancePeriodConfig
 */
class CITBFinancePeriodConfig extends Simple
{
    protected $options;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.ERROR.INCLUDE_FINANCE'));

            $this->setRoute($this->arParams['HELPER']);

            $this->options = Option::getForModule('itbizon.finance');
            $request = Application::getInstance()->getContext()->getRequest();
            $data = $request->getPost('DATA');
            if($request->getPost('save') === 'Y') {
                if(!Permission::getInstance()->isAllowConfigSave())
                    throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.ERROR.ACCESS_DENY'));

                $startWeek = intval($data['startWeek']);
                $startTime = strval($data['startTime']);
                $reserveStockId = intval($data['reserveStockId']);
                $incomeCategoryId = intval($data['incomeCategoryId']);
                if($startWeek < 0 || $startWeek > 6)
                    throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.ERROR.INVALID_WEEK_START'));

                $time = PeriodTable::parseTime($startTime);
                $startTime = sprintf('%02d:%02d', $time[0], $time[1]);

                Option::set('itbizon.finance', 'startWeek', $startWeek);
                Option::set('itbizon.finance', 'startTime', $startTime);
                Option::set('itbizon.finance', 'reserveStockId', $reserveStockId);
                Option::set('itbizon.finance', 'incomeCategoryId', $incomeCategoryId);

                $uri = new Uri($this->getRoute()->getUrl('config'));
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
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }
}
