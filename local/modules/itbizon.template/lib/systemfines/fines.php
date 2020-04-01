<?php

namespace Itbizon\Template\SystemFines\Model;

use Bitrix\Main\UserTable;

class Fines
{
    protected $id;
    protected $title;
    protected $dateCreate;
    protected $creatorId;
    protected $targetId;
    protected $value;

    /**
     * Fines constructor.
     * @param int $id
     * @param string $title
     * @param \Bitrix\Main\Type\Date $dateCreate
     */
    public function __construct($id, $title, $dateCreate)
    {
        $this->id = intval($id);
        $this->title = strval($title);
        $this->dateCreate = $dateCreate;
        $this->creatorId = null;
        $this->targetId = null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return \Bitrix\Main\Type\Date
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * @param \Bitrix\Main\Type\Date $dateCreate
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;
    }

    /**
     * @return UserTable
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * @param UserTable $creatorId
     */
    public function setCreatorId(UserTable $creatorId)
    {
        $this->creatorId = $creatorId;
    }

    /**
     * @return UserTable
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @param UserTable $targetId
     */
    public function setTargetId(UserTable $targetId)
    {
        $this->targetId = $targetId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}
