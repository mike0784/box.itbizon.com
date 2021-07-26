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
 * Class CITBServiceLogList
 */
class CITBServiceLogList extends CBitrixComponent
{
    protected $error;
    protected $helper;
    protected $gridHelper;

    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.LOG.LIST.ERROR.INCLUDE_SERVICE'));
            }

            $this->setHelper(new RouterHelper(
                $this,
                [
                    'list' => 'list/',
                    'view' => 'view/#FILE_NAME#/',
                ],
                'list'
            ));
            $this->getHelper()->run();

            $this->gridHelper = $gridHelper = (new GridHelper('itb_service_log', ''))
                ->setColumns([
                    ['id' => 'ID', 'name' => Loc::getMessage('ITB_SERVICE.LOG.LIST.FIELD.ID'), 'sort' => '', 'default' => true],
                    ['id' => 'NAME', 'name' => Loc::getMessage('ITB_SERVICE.LOG.LIST.FIELD.NAME'), 'sort' => '', 'default' => true],
                    ['id' => 'DATE', 'name' => Loc::getMessage('ITB_SERVICE.LOG.LIST.FIELD.DATE'), 'sort' => '', 'default' => true],
                    ['id' => 'FILENAME', 'name' => Loc::getMessage('ITB_SERVICE.LOG.LIST.FIELD.FILE_NAME'), 'sort' => '', 'default' => false],
                    ['id' => 'SIZE', 'name' => Loc::getMessage('ITB_SERVICE.LOG.LIST.FIELD.SIZE'), 'sort' => '', 'default' => true],
                ]);

            if(!CurrentUser::get()->isAdmin()) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.LOG.LIST.ERROR.ACCESS_DENY'));
            }

            $limit = 0;
            $logs = Log::getList();
            if ($logs) {
                foreach ($logs as $id => $log) {
                    $id++;
                    if ($id > $gridHelper->getNavigation()->getOffset()) {
                        if ($limit < $gridHelper->getNavigation()->getLimit()) {
                            $gridHelper->addRow([
                                'data' => [
                                    'ID' => $id,
                                    'NAME' => $log['NAME'],
                                    'FILENAME' => $log['FILENAME'],
                                    'DATE' => ($log['DATE'] instanceof \DateTime) ? $log['DATE']->format('d.m.Y') : '',
                                    'SIZE' => $log['SIZE']
                                ],
                                'actions' => [
                                    [
                                        'text' => Loc::getMessage('ITB_SERVICE.LOG.LIST.ACTION.VIEW'),
                                        'default' => true,
                                        'onclick' => 'BX.SidePanel.Instance.open("' . $this->getHelper()->getUrl('view', ['FILE_NAME' => $log['FILENAME']]) . '", {
                                            cacheable: false,
                                        });'
                                    ],
                                    [
                                        'text' => Loc::getMessage('ITB_SERVICE.LOG.LIST.ACTION.DOWNLOAD'),
                                        'default' => false,
                                        'onclick' => 'download("' . $this->makeDownloadLink($log['PATH']) . '");'
                                    ],
                                ]
                            ]);
                            $limit++;
                        }
                    }
                }
            }
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
     * @return GridHelper|null
     */
    public function getGridHelper(): ?GridHelper
    {
        return $this->gridHelper;
    }

    /**
     * @param mixed $gridHelper
     */
    public function setGridHelper($gridHelper): void
    {
        $this->gridHelper = $gridHelper;
    }

    /**
     * @param string $path
     * @return string
     */
    private function makeDownloadLink(string $path): string
    {
        $params = http_build_query([
            'process' => [Log::class, 'downloadLog'],
            'pathToFile' => $path
        ]);
        return Processor::getAjaxPath() . '?' . $params;
    }
}
