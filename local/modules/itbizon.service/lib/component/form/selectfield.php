<?php


namespace Itbizon\Service\Component\Form;


/**
 * Class SelectField
 * @package Itbizon\Service\Component\Form
 */
class SelectField extends Field
{
    const SELECT = 'select';
    const CHECKLIST = 'checklist';

    /**
     * @param $value
     * @return array
     */
    public function cast($value)
    {
        return is_array($value) ? $value : [$value];
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return boolval($this->options['multiple']);
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return (is_array($this->options['items'])) ? $this->options['items'] : [];
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return max(1, !$this->isMultiple() ? intval($this->options['size']) : count($this->getItems()));
    }

    /**
     * @return bool
     */
    public function isUseEmpty(): bool
    {
        return boolval($this->options['use_empty']);
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return strval($this->options['theme']);
    }
}