<?php
function search(array $data, int $number): int
{
    if (count($data) === 0) {
        return -1;
    }
    $first_element = 0;
    $last_element = count($data) - 1;
    do {
        $half = (int)(($first_element + $last_element) / 2);
        if ($data[$half] !== $number) {
            if ($data[$half] < $number) {
                $first_element = $half + 1;
            } else {
                $last_element = $half - 1;
            }
        } else {
            return $half;
        }
    } while (($last_element - $first_element) >= 0);
    return -1;
}

function weekend(string $begin, string $end): int
{
    $saturday = 0;
    $resurrection = 0;

    $interval = ((strtotime($end) - strtotime($begin)) / 86400);
    $dayNumber = date("N", strtotime($begin));


    if ($interval >= 0) {

        if (($interval + $dayNumber) >= 6) {
            if ($dayNumber == 7) {
                $saturday = intdiv(($interval + 1), 7);
            } else {
                $saturday = 1 + intdiv(($interval - (6 - $dayNumber)), 7);
            }
        }
        if (($interval + $dayNumber) >= 7) {
            $resurrection = 1 + intdiv(($interval - (7 - $dayNumber)), 7);

        } else {
            $resurrection = 0;
        }

    } else {
        return 0;
    }
    return ($saturday + $resurrection);
}

function rgb(int $r, int $g, int $b): int
{
    $result = '';
    foreach (array($r, $g, $b) as $row) {
        $result .= str_pad(dechex($row), 2, '0', STR_PAD_LEFT);
    }
    return hexdec($result);
}

function fiborow(int $limit)
{
    $res = array(0,1);
    for( $i=0; $i < ($limit-1); $i++ ){
        $cur = $res[$i] + $res[$i+1];
        array_push( $res, $cur);
    }
    return implode(", ",$res);
}

