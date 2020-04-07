<?php

namespace Itbizon\Template\SystemFines\Entities;

use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Type\Date;
use Bitrix\Main\UserTable;
use Itbizon\Template\SystemFines\Model\FinesTable;

class Fines
{
    private $id;
    private $title;
    private $value;
    private $dateCreate;
    private $creator;
    private $target;
    private $comment;

    public function __construct(int $id = null)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(String $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return String
     */
    public function getTitle(): String
    {
        return $this->title;
    }

    public function setDateCreate(Date $dateCreate): self
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * @return Date
     */
    public function getDateCreate(): Date
    {
        return $this->dateCreate;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return integer
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function setCreator(int $userId): self
    {
        $this->creator = UserTable::getByPrimary($userId)->fetchObject();

        return $this;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setTarget(int $userId): self
    {
        $this->target = UserTable::getByPrimary($userId)->fetchObject();;

        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

}
