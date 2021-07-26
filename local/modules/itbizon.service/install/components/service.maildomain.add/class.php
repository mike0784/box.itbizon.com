<?php

use Bitrix\Main\Application;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Service\Mail\MailDomain;

Loc::loadMessages(__FILE__);

/**
 * Class CITBServiceMailDomainAdd
 */
class CITBServiceMailDomainAdd extends CBitrixComponent
{
    protected $helper;
    protected $gridHelper;
    protected $error;

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.ERROR.INCLUDE_SERVICE'));
            }
            if (!CurrentUser::get()->isAdmin()) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.ERROR.ACCESS_DENY'));
            }
            if (!isset($this->arParams['HELPER']) || !is_a($this->arParams['HELPER'], RouterHelper::class)) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.ERROR.NO_HELPER'));
            }

            $this->setHelper($this->arParams['HELPER']);

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');
                if(!is_array($data)) {
                    $data = [];
                }

                $maildomain = (new MailDomain())
                    ->setActive($data['ACTIVE'])
                    ->setDomain($data['DOMAIN'])
                    ->setServer($data['SERVER'])
                    ->setPort($data['PORT']);
                $result = $maildomain->save();
                if(!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }

                $uri = new Uri($this->getHelper()->getUrl('add'));
                if($this->getHelper()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
        // Include template
        $this->IncludeComponentTemplate();
    }

    /**
     * @return RouterHelper|null
     */
    public function getHelper(): ?RouterHelper
    {
        return $this->helper;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param mixed $helper
     */
    protected function setHelper(RouterHelper $helper): void
    {
        $this->helper = $helper;
    }

    /**
     * @param mixed $error
     */
    protected function setError($error): void
    {
        $this->error = $error;
    }
}
