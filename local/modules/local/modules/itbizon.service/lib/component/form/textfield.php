<?php


namespace Itbizon\Service\Component\Form;


/**
 * Class TextField
 * @package Itbizon\Service\Component\Form
 */
class TextField extends Field
{
    /**
     * @param $value
     * @return string
     */
    public function cast($value)
    {
        return strval($value);
    }
}