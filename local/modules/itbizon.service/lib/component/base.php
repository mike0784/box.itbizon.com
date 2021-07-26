<?php


namespace Itbizon\Service\Component;


use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use CBitrixComponent;

/**
 * Class Base
 * @package Itbizon\Service\Component
 */
class Base extends CBitrixComponent
{
    protected $route;
    protected $grid;
    protected $errors;

    /**
     * Base constructor.
     * @param null $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errors = new ErrorCollection();
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @param string $message
     * @param $code
     * @param null $customData
     */
    protected function addError(string $message, $code = 0, $customData = null)
    {
        $this->getErrors()->add([new Error($message, $code, $customData)]);
    }

    /**
     * @return mixed
     */
    public function getRoute(): ?RouterHelper
    {
        return $this->route;
    }

    /**
     * @param RouterHelper $route
     */
    public function setRoute(RouterHelper $route): void
    {
        $this->route = $route;
    }

    /**
     * @return GridHelper|null
     */
    public function getGrid(): ?GridHelper
    {
        return $this->grid;
    }

    /**
     * @param GridHelper $grid
     */
    public function setGrid(GridHelper $grid): void
    {
        $this->grid = $grid;
    }
}