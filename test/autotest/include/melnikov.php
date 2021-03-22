<?php
function search(array $data, int $number) : int {
    $result = -1;
    for ($i = 1; $i <= count($data); $i++)
        if ($data[$i] == $number) {
            $result = $i;
            break;
        }
    return $result;
}

function weekend (string $begin, string $end) : int {
    $days = floor(($begin - $end)/(60*60*24));
    $weeks = ceil($days/7);
    $res = $weeks*2;

    if (date("w", $begin) == 6) {$res = $res - 2;};
    if (date("w", $begin) == 0) {$res = $res - 1;};
    if (date("w", $end) == 6) {$res = $res - 1;};
//if (date("w", $end) == 0) {$res = $res - 1;};

    return $res;
}

function rgb(int $r, int $g, int $b):int {
    $result = ($r << 16) + ($g << 8) + $b;
    return $result;
}

function fiborow(int $limit) : string {
    $a = array();
    $a[0] = 0;
    $a[1] = 1;
    $i=2;
    while (true) {

        $x = $a[$i-1]+$a[$i-2];

        if ($x > $limit){
            break;
        }
        else{
            $a[$i] = $x;
        }
        $i=$i+1;
    }

    $str=(string)$a[0];
    for ($i = 1; $i <= count($a); $i++) {
        $str = $str.' '.(string)$a[$i];
    }

    return $str;
}


