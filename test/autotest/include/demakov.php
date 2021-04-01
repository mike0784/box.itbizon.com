<?php
function search($data, $number)
{
    $count = count($data);
    if ($count < pow(2, 31) && is_int($number)) {
        $start = 0;
        $end = $count - 1;
        while (true) {
            $len = $end - $start;
            if ($len > 2) {
                if ($len % 2 != 0) $len++;
                $mid = (int)($len / 2 + $start);
            } elseif ($len >= 0) {
                $mid = $start;
            } else {
                return false;
            }
            if ($data[$mid] == $number) {
                while (($mid != 0) && ($data[$mid - 1] == $number))
                    $mid--;
                return $mid;
            } elseif ($data[$mid] > $number) {
                $end = $mid - 1;
            } else {
                $start = $mid + 1;
            }
        }
    } else {
        return false;
    }
}
/*function weekend($begin, $end){
    $weekend = 0;
    $interval = new DateInterval('P1D');
    while($begin <= $end){
        if($begin->format('N') == 6 OR $begin->format('N') == 7)
            $weekend++;
        $begin->add($interval);
    }
    return $weekend;
}*/
function fiborow($limit) {
    $j = 1;
    for($i = 0; $i < $limit; $i += $j) {
        if($i == 0){
            echo '0 ';
            $i++;
        }
        echo $i, ' ';
        $j = $i - $j;
    }
}