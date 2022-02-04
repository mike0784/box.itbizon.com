<?php
function search(array $data, int $number):int {
    $start = 0;
    $end = count($data) - 1;

    while ($start <= $end) {
        $index = floor(($start+ $end) / 2);

        if ($data[$index] == $number) {
            return (int) $index;
        }
        if ($data[$index] > $number) {
            $end = $index - 1;
        }  else {
            $start = $index + 1;
        }
    }

    return -1;
}

function weekend(string $begin, string $end) : int {
    $begin = strtotime($begin);
    $end = strtotime($end);
    $iter = 24 * 60 * 60;
    $countOfWeekend = 0;

    for ($i = $begin; $i <= $end; $i = $i + $iter) {
        if ((date('D', $i) == 'Sat') || (date('D', $i) == 'Sun')) {
            $countOfWeekend++;
        }
    }

    return $countOfWeekend;
}

function rgb($r, $g, $b):int {
    $r = $r <<16;
    $g = $g <<8;
    $b = $b;

    return $r+$g+$b;
}

function fiborow(int $limit):string {
    $previous = 0;
    $current = 1;
    $fibaNumber = 0;
    $fibaNumbers = [0];

    while ($fibaNumber < $limit) {

        $previous = $current;
        $current = $fibaNumber;
        $fibaNumber = $previous + $current;
        $fibaNumbers[] = $fibaNumber;
    }

    $fibaNumbers = array_slice($fibaNumbers, 0, -1);
    $array = implode(" ", $fibaNumbers);
    return $array;
}