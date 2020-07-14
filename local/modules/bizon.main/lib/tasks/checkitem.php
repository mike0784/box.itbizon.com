<?php


namespace Bizon\Main\Tasks;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Tasks\Internals\Task\CheckListTable;
use Bitrix\Tasks\Internals\Task\CheckListTreeTable;
use Exception;

class CheckItem
{
    protected $id;
    protected $name;
    protected $complete;
    protected $childes;
    protected $parent;

    /**
     * CheckItem constructor.
     * @param $id
     * @param $name
     * @param $complete
     */
    public function __construct($id, $name, $complete)
    {
        $this->id       = intval($id);
        $this->name     = strval($name);
        $this->complete = boolval($complete);
        $this->childes  = [];
        $this->parent   = null;
    }

    /**
     * @param CheckItem $child
     */
    public function addChild(CheckItem $child)
    {
        $child->parent = $this;
        $this->childes[] = $child;
    }

    /**
     * @return bool
     */
    public function isCheckList()
    {
        return !empty($this->childes);
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return bool
     */
    public function isComplete(): bool
    {
        if($this->isCheckList())
        {
            $complete = true;
            foreach($this->getChildes() as $child)
                $complete &= $child->isComplete();
            return $complete;
        }
        return $this->complete;
    }

    /**
     * @return CheckItem[]
     */
    public function getChildes(): array
    {
        return $this->childes;
    }

    /**
     * @return CheckItem|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param $taskId
     * @return array|CheckItem[]
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getList($taskId)
    {
        if(!Loader::includeModule('tasks'))
            throw new Exception('Error load module tasks');
        $items = [];
        $result = CheckListTable::getList([
            'select' => ['ID', 'TITLE', 'IS_COMPLETE'],
            'filter' => ['=TASK_ID' => $taskId]
        ]);
        while ($item = $result->fetch())
            $items[$item['ID']] = new CheckItem($item['ID'], $item['TITLE'], ($item['IS_COMPLETE'] === 'Y'));

        $itemIds = array_keys($items);
        if(count($itemIds))
        {
            $result = CheckListTreeTable::getList([
                'filter' => [
                    'LOGIC' => 'OR',
                    '=CHILD_ID' => $itemIds,
                    '=PARENT_ID' => $itemIds,
                ]
            ]);
            while($row = $result->fetch())
            {
                if($row['PARENT_ID'] !== $row['CHILD_ID'])
                    $items[$row['PARENT_ID']]->addChild($items[$row['CHILD_ID']]);
            }
        }
        $root = new CheckItem(0,'', false);
        foreach($items as $item)
        {
            if($item->isCheckList())
                $root->addChild($item);
        }
        return $root->getChildes();
    }

    /**
     * @param $taskId
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function recalcCompletePrc($taskId)
    {
        $countItem = 0;
        $countCheckItem = 0;
        $completePrc = 0;

        $list = CheckItem::getList($taskId);
        foreach($list as $checklist)
        {
            foreach($checklist->getChildes() as $item)
            {
                $countItem++;
                if($item->isComplete())
                    $countCheckItem++;
            }
        }
        $completePrc = ($countItem > 0) ? $countCheckItem/$countItem * 100 : 0;

        $task = \CTaskItem::getInstance($taskId, 1);
        $task->update(['UF_AUTO_161831137323' => $completePrc]);
    }
}