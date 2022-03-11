<?php


namespace Itbizon\Service\Activities;


/**
 * Class Field
 * @package Itbizon\Service\Activities
 */
class Field
{
    protected $id;
    protected $name;
    protected $type;
    protected $required;
    protected $options;
    protected $defaultValue;

    /**
     * Field constructor.
     * @param string $id
     * @param string $name
     * @param string $type
     * @param bool $required
     * @param $options
     * @param null $defaultValue
     */
    public function __construct(string $id, string $name, string $type, bool $required, $options = null, $defaultValue = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->options = $options;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }
}