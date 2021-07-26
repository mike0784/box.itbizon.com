<?php


namespace Itbizon\Service\Component\Form;


use Exception;

/**
 * Class Field
 * @package Itbizon\Service\Component\Form
 */
abstract class Field implements FieldInterface
{
    protected $name;
    protected $title;
    protected $value;
    protected $type;
    protected $options;

    /**
     * Field constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setType($data['type'])
            ->setName($data['name'])
            ->setTitle($data['title'])
            ->setValue($data['value'])
            ->setOption($data['options']);
    }

    /**
     * @param array $data
     * @return Field|null
     * @throws Exception
     */
    public static function create(array $data): ?Field
    {
        $fieldType = strval($data['type']);
        $className = '\\Itbizon\\Service\\Component\\Form\\' . $fieldType . 'Field';
        if (class_exists($className) && is_subclass_of($className, self::class)) {
            return new $className($data);
        } else {
            throw new Exception('Некорректный тип поля ' . $fieldType);
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return strtolower($this->type);
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
    public function getCssName(): string
    {
        return mb_strtolower($this->name);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->cast($this->value);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return boolval($this->options['required']);
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return boolval($this->options['disabled']);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return strval($this->options['description']);
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return strval($this->options['placeholder']);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setName($value): self
    {
        $this->name = strval($value);
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setType($value): self
    {
        $this->type = strval($value);
        if (empty($this->type)) {
            $classPath = explode('\\', get_called_class());
            $className = end($classPath);
            $this->type = mb_substr($className, 0, mb_strpos($className, 'Field'));
        }
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTitle($value): self
    {
        $this->title = strval($value);
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOption($value): self
    {
        $this->options = (is_array($value)) ? $value : [];
        return $this;
    }
}