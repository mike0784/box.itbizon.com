<?php
function search(array $data, int $number) : int
{
    foreach ($data as $key => $item) {
        if ($item == $number) {
            return $key;
        } else {
            return -1;
        }
    }
    return 0;
}

function weekend(string $begin, string $end)
{
    $begin = strtotime($begin);
    $end = strtotime($end);
    $days = ($end-$begin)/86400;
    $week = floor($days/7);

    return $weekend = $week*2;
}

function fiborow($limit) {
    $first=0;$second=1;
    for($i=0;$i<$limit;$i++){
        if($i<=1){
            $next=$i;
        }  else  {
            $next=$first+$second;
            $first=$second;
            $second=$next;
        }

        echo  $next." , ";
    }
}