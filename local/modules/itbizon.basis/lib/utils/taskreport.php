<?php

namespace Itbizon\Basis\Utils;

use Bitrix\Main\Config\Option;
use Bitrix\Main\UserTable;
use Bitrix\Tasks\Internals\Fields\Status;
use Bitrix\Tasks\TagTable;
use DateTime;
use Exception;
use Bitrix\Main\Loader;
use Bitrix\Tasks\ElapsedTimeTable;
use Bitrix\Tasks\TaskTable;
use Bitrix\Socialnetwork\WorkgroupTable;

/**
 * Class TaskReportItem
 * @package Itbizon\Basis\Utils
 */
class TaskReportItem
{
    const STAGE_IN_PROGRESS = 1;
    const STAGE_COMPLETE = 2;
    
    const STAGE = [
        self::STAGE_IN_PROGRESS => 'В работе',
        self::STAGE_COMPLETE => 'Заверешна',
    ];
    const TYPE_ROOT = 0;
    const TYPE_PROJECT = 1;
    const TYPE_USER = 2;
    const TYPE_TASK = 3;
    
    protected static $indexCounter;
    protected $index;
    protected $id;
    protected $name;
    protected $begin;
    protected $end;
    protected $deadLine;
    protected $overdueDays;
    protected $workTime;
    protected $parent;
    protected $children;
    protected $status;
    protected $stage;
    protected $weekNumber;
    protected $itemType;
    protected $link;
    protected $groupId;
    
    /**
     * TaskReportItem constructor.
     * @param int           $id
     * @param string        $name
     * @param DateTime|null $begin
     * @param DateTime|null $end
     * @param DateTime|null $deadLine
     * @param int           $workTime
     * @param int           $status
     * @param string        $weekNumber
     */
    public function __construct(int $id, string $name, DateTime $begin = null, DateTime $end = null, DateTime $deadLine = null, int $workTime = 0, int $status = 0, $weekNumber = '')
    {
        self::$indexCounter++;
        $this->index       = self::$indexCounter;
        $this->id          = $id;
        $this->name        = $name;
        $this->begin       = ($begin)    ? clone $begin    : null;
        $this->end         = ($end)      ? clone $end      : null;
        $this->deadLine    = ($deadLine) ? clone $deadLine : null;;
        $this->workTime    = $workTime;
        $this->status      = $status;
        $this->weekNumber  = $weekNumber;
        $this->parent      = null;
        $this->children    = [];
        
        if(!$this->deadLine && $this->end)
            $this->deadLine = $this->end;
        
        if($this->deadLine)
        {
            $now = new DateTime();
            $diff = $now->diff($this->deadLine);
            $this->overdueDays = $diff->invert ? $diff->d ? $diff->d : 1 : 0;
        }
        
        if($this->weekNumber)
            $this->stage = $this->status == Status::COMPLETED ? self::STAGE_COMPLETE : self::STAGE_IN_PROGRESS;
    }
    
    /**
     *
     */
    protected function setLink()
    {
        if($this->itemType == self::TYPE_PROJECT)
            $this->link = '/workgroups/group/'.$this->id.'/';
        elseif ($this->itemType == self::TYPE_USER)
            $this->link = '/company/personal/user/'.$this->id.'/';
        elseif ($this->itemType == self::TYPE_TASK)
            $this->link = '/workgroups/group/'.$this->groupId.'/tasks/task/view/'.$this->id.'/';
    }
    
    /**
     * @param $type
     */
    public function setItemType($type)
    {
        $this->itemType = $type;
    }
    
    /**
     * @param $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }
    
    /**
     * @param TaskReportItem $child
     * @return bool
     */
    public function addChild(TaskReportItem $child) : bool
    {
        if(!$child->getParent()) {
            $this->children[$child->getIndex()] = $child;
            $child->setParent($this);
            return true;
        }
        return false;
    }
    
    /**
     * @return string
     */
    public function getStageName()
    {
        return self::STAGE[$this->stage];
    }
    
    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
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
     * @return DateTime|null
     */
    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }
    
    /**
     * @return DateTime|null
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }
    
    /**
     * @return DateTime|null
     */
    public function getDeadLine(): ?DateTime
    {
        return $this->deadLine;
    }
    
    /**
     * @param bool $recursive
     * @return int
     */
    public function getWorkTime(bool $recursive = true): int
    {
        if($recursive) {
            $childTime = 0;
            foreach($this->getChildren() as $child) {
                $childTime += $child->getWorkTime(true);
            }
            return $this->workTime + $childTime;
        } else {
            return $this->workTime;
        }
    }
    
    /**
     * @return TaskReportItem|null
     */
    public function getParent(): ?TaskReportItem
    {
        return $this->parent;
    }
    
    /**
     * @return TaskReportItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
    
    /**
     * @param TaskReportItem $parent
     */
    protected function setParent(TaskReportItem $parent): void
    {
        $this->parent = $parent;
        $this->setLink();
    }
    
    /**
     * @return int
     */
    public function getWeekNumber(): int
    {
        return $this->weekNumber;
    }
    
    /**
     * @return string
     */
    public function getStage(): string
    {
        return $this->stage;
    }
    
    /**
     * @return int
     */
    public function getOverdueDays(): int
    {
        return $this->overdueDays;
    }
    
    /**
     * @param     $result
     * @param int $parentId
     * @return mixed
     */
    public function getReportData($result, $parentId = 0)
    {
        if(count($result) > 100)
            return $result;
        $blank = $this->itemType == TaskReportItem::TYPE_PROJECT ? 'target="_blank"' : '';
        $entityName = 'Проект: ';
        $entityName = $this->itemType == TaskReportItem::TYPE_USER ? 'Пользователь: ' : $entityName;
        $entityName = $this->itemType == TaskReportItem::TYPE_TASK ? 'Задача: ' : $entityName;
        $result[$this->index] = [
            'ID'=>strval($this->index),
            'NAME'=>$entityName.$this->name,
            'LINK_NAME'=>$entityName.'<a href="'.$this->link.'" '.$blank.'>'.$this->name.'</a>',
            'STAGE'=>$this->getStageName(),
            'WEEK_ID'=>$this->weekNumber,
            'OVERDUE'=>$this->overdueDays,
            'WORK_TIME'=>$this->getFormatWorkTime(),
            'HAS_CHILD'=>false,
            'PARENT_ID'=>$parentId,
        ];
        $deadline = $this->deadLine ? $this->deadLine : $this->end;
        $deadline = $deadline ? $deadline->format('d.m.Y') : $deadline;
        $result[$this->index]['DEADLINE'] = $deadline;
        
        $children = $this->getChildren();
        if($children)
            $result[$this->index]['HAS_CHILD'] = true;
        foreach ($children as $child)
            $result = $child->getReportData($result, $this->index);
        return $result;
    }
    
    /**
     * @return string
     */
    protected function getFormatWorkTime()
    {
        $min = round($this->getWorkTime() / 60);
        $hour = floor($min / 60);
        $min = $min % 60;
        return sprintf('%02d:%02d', $hour, $min);
    }
}

/**
 * Class TaskReport
 * @package Itbizon\Basis\Utils
 */
class TaskReport
{
    protected $root;
    protected $beginDate;
    protected $endDate;
    protected $tasksCheck;
    protected $taskWeekField;
    
    /**
     * TaskReport constructor.
     * @param DateTime $beginDate
     * @param DateTime $endDate
     * @param array    $postFilter
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws Exception
     */
    public function __construct(DateTime $beginDate, DateTime $endDate, array $postFilter = [])
    {
        if (!Loader::includeModule('tasks'))
            throw new Exception('Error load module tasks');
        if (!Loader::includeModule('socialnetwork'))
            throw new Exception('Error load module tasks');
        
        $this->taskWeekField = Option::get('itbizon.basis','number_week');
        $this->root = new TaskReportItem(1, 'Корень');
        $this->root->setItemType(TaskReportItem::TYPE_ROOT);
        $this->beginDate = $beginDate;
        $this->endDate   = $endDate;
        
        $filter = [
            '>=DATE_START' => $beginDate->format('d.m.Y H:i:s'),
            '<=DATE_START' => $endDate->format('d.m.Y H:i:s'),
            '>TASK_GROUP_ID' => 0,
        ];
        
        // filter by tag
        if(isset($postFilter['TAG']))
        {
            $list = TagTable::getList([
                'filter'=>[
                    'NAME'=>$postFilter['TAG'],
                ],
                'select'=>[
                    'TASK_ID',
                ]
            ]);
            $result = [];
            while($row = $list->fetch())
                $result[] = $row['TASK_ID'];
            
            if($postFilter['TASK_ID'])
                $result = array_intersect([$postFilter['TASK_ID']], $result);
            if(!$result)
                return false;
            foreach ($result as $item)
                $filter['=TASK_ID'][$item] = $item;
        }
        // filter by task id
        if(isset($postFilter['TASK_ID']) && !$filter['=TASK_ID'])
            $filter['=TASK_ID'] = $postFilter['TASK_ID'];
        
        // filter by user id
        if(isset($postFilter['USER_ID']) && is_array($postFilter['USER_ID']))
            $filter['=USER_ID'] = $postFilter['USER_ID'];
        
        $times = [];
        $taskIds = [];
        $userIds = [];
        $result = ElapsedTimeTable::getList([
            'select' => [
                'ID',
                'TASK_ID',
                'USER_ID',
                'SECONDS',
                'TASK_GROUP_ID' => 'TASK.GROUP_ID',
            ],
            'filter' => $filter,
        ]);
        while($row = $result->fetch()) {
            $projectId = intval($row['TASK_GROUP_ID']);
            $userId    = intval($row['USER_ID']);
            $taskId    = intval($row['TASK_ID']);
            $taskIds[] = $taskId;
            $userIds[] = $userId;
            $times[$projectId][$userId][$taskId][] = $row;
        }
        $taskIds = array_unique(array_merge($taskIds, $this->getParentTaskIds($taskIds)));
        
        if(!empty($taskIds)) {
            $users = [];
            if(!empty($userIds)) {
                $userIds = array_unique($userIds);
                $result = UserTable::getList([
                    'select' => [
                        'ID', 'NAME', 'LAST_NAME'
                    ],
                    'filter' => [
                        '=ID' => $userIds
                    ]
                ]);
                while($row = $result->fetch()) {
                    $userId    = intval($row['ID']);
                    $users[$userId] = $row;
                }
            }
            
            $tasks = [];
            $projectIds = [];
            $filter = [
                '=ID' => array_unique($taskIds),
                'ZOMBIE'=>'N',
            ];
            if(isset($postFilter['WEEK_ID']))
                $filter[$this->taskWeekField] = $postFilter['WEEK_ID'];
            
            $result = TaskTable::getList([
                'select' => [
                    'ID',
                    'TITLE',
                    'GROUP_ID',
                    'START_DATE_PLAN',
                    'END_DATE_PLAN',
                    'DEADLINE',
                    'PARENT_ID',
                    'STATUS',
                    $this->taskWeekField,
                ],
                'filter' => $filter,
            ]);
            while($row = $result->fetch()) {
                $taskId    = intval($row['ID']);
                $projectId = intval($row['GROUP_ID']);
                if($projectId) {
                    $projectIds[] = $projectId;
                }
                if($row['PARENT_ID'] && isset($tasks[$row['PARENT_ID']]))
                    $tasks[$row['PARENT_ID']]['CHILDREN'][] = $taskId;
                $tasks[$taskId] = $row;
            }
            foreach ($tasks as $taskId => $task)
            {
                if(isset($task['CHILDREN']))
                {
                    foreach ($times as $projectId => $user)
                    {
                        $uId = 0;
                        foreach ($user as $userId => $item)
                        {
                            $uId = $userId;
                            $inter = array_intersect($task['CHILDREN'], array_keys($item));
                            $isContain = in_array($task['ID'], array_keys($item));
                            if(!empty($inter) && !$isContain)
                            {
                                $times[$projectId][$userId][$task['ID']][] = [
                                    'ID' => 0,
                                    'TASK_ID' => $task['ID'],
                                    'USER_ID' => $userId,
                                    'SECONDS' => 0,
                                    'TASK_GROUP_ID' => $projectId,
                                ];
                            }
                        }
                        if($uId) ksort($times[$projectId][$uId]);
                    }
                }
            }
            
            if(!empty($projectIds)) {
                $projectIds = array_unique($projectIds);
                $result = WorkgroupTable::getList([
                    'select' => [
                        'ID',
                        'NAME',
                        'PROJECT_DATE_START',
                        'PROJECT_DATE_FINISH'
                    ],
                    'filter' => [
                        '=ID' => $projectIds
                    ]
                ]);
                while($row = $result->fetch()) {
                    $projectId = intval($row['ID']);
                    $begin    = $this->dateFormat($row['PROJECT_DATE_START']);
                    $end    = $this->dateFormat($row['PROJECT_DATE_FINISH']);
                    $projectItem = new TaskReportItem($projectId, $row['NAME'], $begin, $end);
                    $projectItem->setItemType(TaskReportItem::TYPE_PROJECT);
                    if(isset($times[$projectId])) {
                        foreach ($times[$projectId] as $userId => $data) {
                            if(isset($users[$userId])) {
                                $userItem = new TaskReportItem($userId, $users[$userId]['LAST_NAME'].' '.$users[$userId]['NAME']);
                                $userItem->setItemType(TaskReportItem::TYPE_USER);
                                if(isset($times[$projectId][$userId])) {
                                    foreach($times[$projectId][$userId] as $taskId => $task) {
                                        if(isset($tasks[$taskId])) {
                                            if(intval($tasks[$taskId]['GROUP_ID']) === $projectId && !$this->tasksCheck[$projectId][$userId][$taskId]) {
                                                $this->tasksCheck[$projectId][$userId][$taskId] = true;
                                                $taskItem = $this->createTaskItem($taskId, $tasks, $times[$projectId][$userId], $projectId, $userId);
                                                $userItem->addChild($taskItem);
                                            }
                                        }
                                    }
                                }
                                $projectItem->addChild($userItem);
                            }
                        }
                    }
                    $this->root->addChild($projectItem);
                }
            }
        }
    }
    
    /**
     * @param array $taskId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getParentTaskIds(array $taskId)
    {
        $parentTaskIds = [];
        if(!empty($taskId)) {
            $result = TaskTable::getList([
                'select' => ['ID', 'PARENT_ID'],
                'filter' => [
                    '=ID' => array_unique($taskId)
                ]
            ]);
            while($row = $result->fetch()) {
                $parentTaskId = intval($row['PARENT_ID']);
                if($parentTaskId) {
                    $parentTaskIds[] = $parentTaskId;
                }
            }
            $parentTaskIds = array_unique($parentTaskIds);
            if(!empty($parentTaskIds)) {
                $parentTaskIds = array_merge($parentTaskIds, $this->getParentTaskIds($parentTaskIds));
            }
        }
        return array_unique($parentTaskIds);
    }
    
    protected function getTaskTree(TaskReportItem $parent)
    {
    
    }
    
    /**
     * @param $taskId
     * @param $tasks
     * @param $times
     * @param $projectId
     * @param $userId
     * @return TaskReportItem
     */
    protected function createTaskItem($taskId, $tasks, $times, $projectId, $userId)
    {
        $task = $tasks[$taskId];
        $begin    = $this->dateFormat($task['START_DATE_PLAN']);
        $end    = $this->dateFormat($task['START_END_PLAN']);
        $deadline    = $this->dateFormat($task['DEADLINE']);
        $workTime = 0;
        foreach ($times[$taskId] as $item)
            $workTime += $item['SECONDS'];
    
        $taskItem = new TaskReportItem(
            $task['ID'],
            $task['TITLE'],
            $begin,
            $end,
            $deadline,
            intval($workTime),
            $task['STATUS'],
            $task[$this->taskWeekField]
        );
        $taskItem->setItemType(TaskReportItem::TYPE_TASK);
        $taskItem->setGroupId(array_shift($times[$taskId])['TASK_GROUP_ID']);
        
        if(isset($task['CHILDREN']))
        {
            foreach ($task['CHILDREN'] as $childId)
            {
                $taskItem->addChild($this->createTaskItem($childId, $tasks, $times, $projectId, $userId));
            }
        }
        $this->tasksCheck[$projectId][$userId][$taskId] = true;
        return $taskItem;
    }
    
    /**
     * @param $date
     * @return DateTime|null
     * @throws Exception
     */
    protected function dateFormat($date)
    {
        return ($date) ? new \DateTime($date->format('d.m.Y H:i:s')) : null;
    }
    
    /**
     * @return TaskReportItem
     */
    public function getRoot()
    {
        return $this->root;
    }
}