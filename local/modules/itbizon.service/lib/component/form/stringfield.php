<?php


namespace Itbizon\Service\Component\Form;


/**
 * Class StringField
 * @package Itbizon\Service\Component\Form
 */
class StringField extends Field
{
    /**
     * @param $value
     * @return string
     */
    public function cast($value)
    {
        return strval($value);
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return strval($this->options['pattern']);
    }
}