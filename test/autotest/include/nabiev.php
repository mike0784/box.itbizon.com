<?php
function search(array $data, int $number) : int
{
    foreach($data as $k=>$it)
    {
        if($number==$it) return $k;
    }
    return -1;
}

function weekend(string $begin, string $end): int
{
    $d1=strtotime($begin);
    $d2=strtotime($end);

    $inc = 24*60*60;
    $count=0;

    for($d1; $d1 <= $d2; $d1+=$inc)
    {
        if(Date('D',$d1) == 'Sat' || Date('D',$d1) == 'Sun')
        {
            $count++;
        }
    }
    return $count;
}

function  rgb(int $r, int $g, int $b)
{
    return ($r << 16) + ($g << 8) + $b;
}

function fiborow(int $limit): string
{
    $arr=[];
    $i=0;
    $y=0;
    while ($i>=0) {
        if($i>1){
            $c=$arr[$i-1]+$arr[$i-2];
            if($c>$limit) break;
            $arr[]=$c;
        }
        else $arr[]=$i;
        $i++;
    }
    return implode(' ',$arr);
}