<?php
function search(array $data, int $number)
{
    foreach ($data as $key=>$value) {
        if ($value === $number) {
            return $key;
        }
        else return -1;
    }
}
function weekend(string $begin, string $end) : int
{
    $sub = 0;  $vos = 0;

    $interval =((strtotime($end) - strtotime($begin)) / 86400);
    $dayNumber = date("N", strtotime($begin));


    if ($interval >= 0) {

        if (($interval + $dayNumber) >= 6) {
            if ($dayNumber == 7) {
                $sub = intdiv(($interval + 1), 7);
            } else {
                $sub = 1 + intdiv(($interval - (6 - $dayNumber)), 7);
            }
        }
        if (($interval + $dayNumber) >= 7) {
            $vos = 1 + intdiv(($interval - (7 - $dayNumber)), 7);

        } else {
            $vos = 0;
        }

    } else {
        return 0;
    }
    return ($sub + $vos);
}
function rgb(int $r, int $g, int $b):int
{
    return $b * 65536 + $g * 256 + $r ;
}
function fiborow(int $limit) : string
{
    $f1 = 0;
    $f2 = 1;
    $f3 = 0;
    $string = "0";
    do {

        $string .= " ".($f1 + $f2);
        $f3 = $f1 + $f2;
        $f1 = $f2;
        $f2 = $f3;
    } while (($f1+$f2) < $limit);

    return $string;
}