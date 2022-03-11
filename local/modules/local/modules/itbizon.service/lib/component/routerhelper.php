<?php


namespace Itbizon\Service\Component;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use CBitrixComponent;
use CComponentEngine;
use Exception;

/**
 * Class Helper
 * @package Itbizon\Service\Component
 */
class RouterHelper
{
    protected $component;
    protected $folder;
    protected $urlTemplates;
    protected $defaultAction;
    protected $action;
    protected $variables;

    /**
     * Helper constructor.
     * @param CBitrixComponent $component
     * @param array $urlTemplates
     * @param string $defaultAction
     * @throws Exception
     */
    public function __construct(CBitrixComponent $component, array $urlTemplates, string $defaultAction)
    {
        $this->component = $component;
        $this->folder = strval($this->getComponent()->arParams['SEF_FOLDER']);
        if(empty($this->folder))
            $this->folder = Context::getCurrent()->getRequest()->getRequestedPageDirectory().'/';
        $this->urlTemplates = $urlTemplates;
        $this->defaultAction = $defaultAction;
        $this->action = $this->defaultAction;
        $this->variables = [];
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        if($this->getComponent()->arParams['SEF_MODE'] !== 'Y') {
            throw new Exception('Component work only in SEF mode');
        }
        $this->variables = [];
        $this->action = CComponentEngine::ParseComponentPath(
            $this->getFolder(),
            $this->getUrlTemplates(),
            $this->variables
        );
        if(empty($this->action))
            $this->action = $this->defaultAction;
    }

    /**
     * @return CBitrixComponent
     */
    public function getComponent(): CBitrixComponent
    {
        return $this->component;
    }

    /**
     * @return array
     */
    public function getUrlTemplates(): array
    {
        return $this->urlTemplates;
    }

    /**
     * @return string
     */
    public function getDefaultAction(): string
    {
        return $this->defaultAction;
    }

    /**
     * @return mixed
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param string $name
     * @return mixed|string
     */
    public function getVariable(string $name)
    {
        return (isset($this->variables[$name])) ? $this->variables[$name] : '';
    }

    /**
     * @param string $action
     * @param array $variables
     * @return string
     */
    public function getUrl(string $action, array $variables = []): string
    {
        $url = $this->getFolder().$this->urlTemplates[$action];
        foreach($variables as $key => $value) {
            $url = str_replace('#'.$key.'#', $value, $url);
        }
        return $url;
    }

    /**
     * @return bool
     */
    public function isInSliderMode()
    {
        $request = Application::getInstance()->getContext()->getRequest();
        return ($request->get('IFRAME') === 'Y' && $request->get('IFRAME_TYPE') === 'SIDE_SLIDER');
    }
}