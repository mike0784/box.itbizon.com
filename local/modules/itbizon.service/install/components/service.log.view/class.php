<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Service\Component\GridHelper;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Service\Log;
use Itbizon\Service\Processor;

Loc::loadMessages(__FILE__);

/**
 * Class CITBServiceLogView
 */
class CITBServiceLogView extends CBitrixComponent
{
    protected $error;
    protected $helper;
    protected $gridHelper;
    protected $log;

    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.LOG.VIEW.ERROR.INCLUDE_SERVICE'));
            }
            if (!isset($this->arParams['HELPER']) || !is_a($this->arParams['HELPER'], RouterHelper::class)) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.LOG.VIEW.ERROR.NO_HELPER'));
            }
            if (!CurrentUser::get()->isAdmin()) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.LOG.VIEW.ERROR.ACCESS_DENY'));
            }

            $this->setHelper($this->arParams['HELPER']);

            $fileName = strval($this->getHelper()->getVariable('FILE_NAME'));
            $pathToFile = Log::LOG_PATH.'/'.$fileName;
            if (!file_exists($pathToFile)) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.LOG.VIEW.ERROR.FILE_NOTFOUND'));
            }
            $this->log = $this->processFileContent(file_get_contents($pathToFile));
        } catch (Exception $e) {
            echo $e->getMessage();
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
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @return RouterHelper|null
     */
    public function getHelper(): ?RouterHelper
    {
        return $this->helper;
    }

    /**
     * @param mixed $helper
     */
    public function setHelper($helper): void
    {
        $this->helper = $helper;
    }

    /**
     * @return string|null
     */
    public function getLog(): ?string
    {
        return $this->log;
    }

    /**
     * @param string $content
     * @return string
     */
    private function processFileContent(string $content): string
    {
        $content = nl2br(htmlspecialchars($content));
        $content = preg_replace('/(\[[0-9]{2}:[0-9]{2}:[0-9]{2}\])/', '<span class="itb-service-log-timestamp">$1</span>', $content);
        $patterns = [
            '[INFO]' => '<span class="itb-service-log-level itb-service-log-level-info">[INFO]</span>',
            '[WARN]' => '<span class="itb-service-log-level itb-service-log-level-warn">[WARN]</span>',
            '[ERROR]' => '<span class="itb-service-log-level itb-service-log-level-error">[ERROR]</span>',
            '[OK]' => '<span class="itb-service-log-level itb-service-log-level-ok">[OK]</span>',
        ];
        $content = str_replace(array_keys($patterns), array_values($patterns), $content);

        return $content;
    }
}
