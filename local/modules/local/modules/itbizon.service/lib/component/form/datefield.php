<?php


namespace Itbizon\Service\Component\Form;


/**
 * Class DateField
 * @package Itbizon\Service\Component\Form
 */
class DateField extends Field
{
    /**
     * @param $value
     * @return false|string
     */
    public function cast($value): string
    {
        if (!empty($value)) {
            if ($this->isUseTime()) {
                return date('d.m.Y H:i', strtotime($value));
            } else {
                return date('d.m.Y', strtotime($value));
            }
        } else {
            return '';
        }
    }

    /**
     * @return bool
     */
    public function isUseTime(): bool
    {
        return boolval($this->options['time']);
    }
}