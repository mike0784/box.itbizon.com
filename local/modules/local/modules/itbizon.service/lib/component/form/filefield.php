<?php


namespace Itbizon\Service\Component\Form;


class FileField extends Field
{
    const BUTTON = 'button';
    const DND = 'dnd';
    const LINK = 'link';

    /**
     * @param $value
     * @return int
     */
    public function cast($value)
    {
        return intval($value);
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return boolval($this->options['multiple']);
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return strval($this->options['theme']);
    }

    /**
     * @return string
     */
    public function getButtonCaption(): string
    {
        $caption = strval($this->options['button_caption']);
        return !empty($caption) ? $caption : 'Выбрать';
    }
}