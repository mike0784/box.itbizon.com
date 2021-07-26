<?php

use Bitrix\Main\Application;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Service\Mail\MailDomain;
use Itbizon\Service\Mail\Model\MailDomainTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBServiceMailDomainEdit
 */
class CITBServiceMailDomainEdit extends CBitrixComponent
{
    protected $helper;
    protected $gridHelper;
    protected $error;
    protected $maildomain;

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ERROR.INCLUDE_SERVICE'));
            }
            if (!CurrentUser::get()->isAdmin()) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ERROR.ACCESS_DENY'));
            }
            if (!isset($this->arParams['HELPER']) || !is_a($this->arParams['HELPER'], RouterHelper::class)) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ERROR.NO_HELPER'));
            }

            $this->setHelper($this->arParams['HELPER']);

            $id = intval($this->getHelper()->getVariable('ID'));
            $this->maildomain = MailDomainTable::getById($id)->fetchObject();
            if(!$this->maildomain) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.EDIT.ERROR.NOT_FOUND'));
            }

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');
                if(!is_array($data)) {
                    $data = [];
                }

                $this->maildomain
                    ->setActive($data['ACTIVE'])
                    ->setDomain($data['DOMAIN'])
                    ->setServer($data['SERVER'])
                    ->setPort($data['PORT']);
                $result = $this->maildomain->save();
                if(!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }

                $uri = (new Uri($this->getHelper()->getUrl('edit', ['ID' => $this->maildomain->getId()])));
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

    /**
     * @return mixed
     */
    public function getMaildomain(): ?MailDomain
    {
        return $this->maildomain;
    }
}
