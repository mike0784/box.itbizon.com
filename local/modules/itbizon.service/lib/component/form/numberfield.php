<?php


namespace Itbizon\Service\Component\Form;


/**
 * Class StringField
 * @package Itbizon\Service\Component\Form
 */
class NumberField extends Field
{
    /**
     * @param $value
     * @return string
     */
    public function cast($value)
    {
        return is_numeric($value) ? $value : 0;
    }

    /**
     * @return float
     */
    public function getMin(): float
    {
        return floatval($this->options['min']);
    }

    /**
     * @return float
     */
    public function getMax(): float
    {
        return floatval($this->options['max']);
    }

    /**
     * @return float
     */
    public function getStep(): float
    {
        return floatval($this->options['step']);
    }
}