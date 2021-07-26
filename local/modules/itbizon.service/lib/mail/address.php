<?php


namespace Itbizon\Service\Mail;


class Address
{
    protected $address;
    protected $name;

    /**
     * Address constructor.
     * @param string $address
     * @param string $name
     */
    public function __construct(string $address, string $name)
    {
        $this->address = trim($address);
        $this->name = trim($name);
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $string
     * @return self[]
     */
    public static function parseString(string $string): array
    {
        $addresses = [];
        $items = explode(',', $string);
        foreach ($items as $item) {
            $pos = mb_strpos($item, '<');
            $name = ($pos !== false) ? mb_substr($item, 0, $pos) : '';

            if(preg_match('#<(.*?)>#', $item, $match)) {
                $address = $match[1];
            } else {
                $address = $item;
            }
            $addresses[] = new self($address, $name);
        }
        return $addresses;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        if(!empty($this->name)) {
            return sprintf('%s <%s>', $this->name, $this->address);
        } else {
            return $this->address;
        }
    }

    /**
     * @param self[] $items
     * @return string
     */
    public static function array2String(array $items): string
    {
        $array = [];
        foreach ($items as $item) {
            if(is_a($item, self::class)) {
                $array[] = $item->toString();
            }
        }
        return implode(', ', $array);
    }
}