<?php
/**
 * Created by PhpStorm.
 * User: Komyagin Pavel
 * Date: 19.04.2019
 * Time: 20:39
 */

namespace Bizon\Main\Utils;


class TimeSelector
{
    const FILL_NONE  = 0x0;
    const FILL_LEFT  = 0x1;
    const FILL_RIGHT = 0x2;
    const FILL_ALL   = (self::FILL_LEFT | self::FILL_RIGHT);
    
    protected $Mode;
    protected $DefaultValue;
    protected $Data;
    protected $Cache;
    protected $Sorted = false;
    
    /**
     * TimeSelector constructor.
     * @param int $default
     * @param int $mode
     */
    public function __construct($default = 0, $mode = self::FILL_ALL)
    {
        $this->Mode = $mode;
        $this->DefaultValue = $default;
        $this->Clear();
        $this->ClearCache();
    }
    
    /**
     *
     */
    public function Clear()
    {
        $this->Data  = array();
    }
    
    /**
     *
     */
    public function ClearCache()
    {
        $this->Cache = array();
    }
    
    /**
     * @param $date
     * @param $value
     */
    public function Add($date, $value)
    {
        $date = intval($date);
        $this->Data[] = [
            'date'  => $date,
            'value' => is_object($value) ? clone $value : $value
        ];
        $this->Sorted = false;
        $this->ClearCache();
    }
    
    /**
     * @param $date
     * @return int
     */
    public function Get($date)
    {
        $date = intval($date);
        //Form cache
        if(isset($this->Cache[$date]))
        {
            return $this->Cache[$date];
        }
        //...or search
        else
        {
            //If not sorted - sort
            if(!$this->Sorted)
                $this->Sort();
            
            //If empty return default
            $begin = 0;
            $count = count($this->Data);
            if(!$count)
                return $this->DefaultValue;
            
            $l = $begin;
            $r = $count-1;
            while(1)
            {
                $cur_index = $l + intdiv($r - $l, 2);
                $prev_index = $cur_index-1;
                $next_index = $cur_index+1;
                
                $curr = &$this->Data[$cur_index];
                $prev = null;
                if($prev_index >= 0) $prev = &$this->Data[$prev_index];
                $next = null;
                if($next_index < $count) $next = &$this->Data[$next_index];
                if($date >= $curr['date'])
                {
                    if($next)
                    {
                        if($date < $next['date'])
                        {
                            $this->Cache[$date] = $curr['value'];
                            return $curr['value'];
                        }
                        else
                            $l = $next_index;
                    }
                    else
                    {
                        if($this->Mode & self::FILL_RIGHT)
                        {
                            $this->Cache[$date] = $curr['value'];
                            return $curr['value'];
                        }
                        else
                            return $this->DefaultValue;
                    }
                }
                else
                {
                    if($prev)
                        $r = $prev_index;
                    else
                    {
                        if($this->Mode & self::FILL_LEFT)
                        {
                            $this->Cache[$date] = $curr['value'];
                            return $curr['value'];
                        }
                        else
                            return $this->DefaultValue;
                    }
                }
                unset($curr);
                unset($next);
                unset($prev);
            }
            return $this->DefaultValue;
        }
    }
    
    /**
     *
     */
    public function Sort()
    {
        uasort($this->Data, function($a, $b){
            if ($a['date'] == $b['date']) return 0;
            return ($a['date'] < $b['date']) ? -1 : 1;
        });
        $this->Data = array_values($this->Data);
        $this->Sorted = true;
    }
    
    /**
     * @return mixed
     */
    public function GetData()
    {
        return $this->Data;
    }
}