<?php
function search(array $data, int $number)
{
    $cnt = count($data);
    if($cnt>1)
    {
        $center = floor($cnt/2);
        $value = $data[$center];
        if($value<$number)
        {
            $rez = search(array_slice($data,$center+1), $number);
            return $rez>=0 ? $rez+$center+1 : -1;
        }
        elseif($value>$number)
        {
            $rez = search(array_slice($data,0,$center), $number);
            return $rez;
        }
        else return $center;
    }
    elseif($cnt==1)
        return $data[0]==$number ? 0 : -1;
    else
        return -1;
}

function weekend (string $begin, string $end)
{
    $count_weekend = 0;
    $begin_datetime = strtotime($begin);
    $begin_day_num = date("w",$begin_datetime);
    $one_day = 60*60*24;

    if($begin_day_num==0)//воскресенье
    {
        $count_weekend++;
        $begin_datetime += $one_day;
    }
    else if($begin_day_num==6)//суббота
    {
        $count_weekend++;
        $begin_datetime += 2*$one_day;
    }

    $begin_day_num = date("w",$begin_datetime);

    $end_datetime = strtotime($end);
    $end_day_num = date("w",$end_datetime);

    if($end_day_num==6)//суббота
        $end_datetime += ($begin_day_num+1)*$one_day;
    else $end_datetime += ($begin_day_num-$end_day_num)*$one_day;

    $interval_in_days = ($end_datetime-$begin_datetime)/$one_day;
    $count_weekend += $interval_in_days/7;
    return $count_weekend;
}

function rgb(int $r, int $g, int $b)
{
    if(checkColor($r) || checkColor($g) || checkColor($b))
    {
        return $r+($g<<8)+($b<<16);
    }
    else return false;
}

function checkColor($color)
{
    if($color<0 || $color>255)
        return false;
    else return true;
}

function fiborow(int $limit)
{
    $a = 0;
    $b = 1;
    if($limit<0) return "";
    if($limit<=1) return "0";
    $result = "0 1";
    while(($c = $a+$b)<$limit)
    {
        $result .= " ".$c;
        $a = $b;
        $b = $c;
    }
    return $result;
}

