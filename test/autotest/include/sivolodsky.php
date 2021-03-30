<?php
function search(array $data, int $number) : int
{

    $result = array_search($number, $data);

    return $result == false ? -1 : $result;
}

function weekend(string $begin, string $end): int
{

    $datetime1 = date_create($begin);

    $datetime2 = date_create($end);

    $interval = date_diff($datetime1, $datetime2);

    $days = intval($interval->format('%a'));

    $NumberDayBegin = intval(date_format($datetime1, 'N'));

    $NumberDayEnd = intval(date_format($datetime2, 'N'));

    if ($days <= 7) {

        $result = 0;

        if ($days == 0 && ($datetime1->format('l') == ('Sunday' || 'Saturday'))) $result++;

        while ($datetime1 <> $datetime2) {

            $dayweek = $datetime1->format('l');

            if (($dayweek == 'Sunday') || ($dayweek == 'Saturday')) $result++;

            $datetime1->modify('+ 1day');
        }
    } else {


        $privSaturday = $datetime1->modify('previous Monday');

        $nextMonday = $datetime2->modify('next Monday');

        $interval = date_diff($privSaturday, $nextMonday);

        $del = 0;

        if ($NumberDayEnd < 6) {
            $del = $del + 2;
        }

        if ($NumberDayEnd > 5) {
            $del = $del + 1;
        }

        if ($NumberDayBegin > 6) {

            $del = $del + 1;
        }

        if ($NumberDayBegin < 5) {

            $del = $del + 2;
        }



        $result =  ((intval($interval->format('%a')) / 7) * 2) - $del;
    }

    return $result;
}

function  rgb(int $r, int $g, int $b)
{

    $hexR =  strlen(strval($r)) == 1 ? "0" . $r : dechex($r);

    $hexG =  strlen(strval($g)) == 1 ? "0" . $g : dechex($g);

    $hexB =  strlen(strval($b)) == 1 ? "0" . $b : dechex($b);;

    $hex = $hexR . $hexG . $hexB;

    return  hexdec($hex);
}

function fiborow(int $limit): string
{

    if ($limit < 1) return '';

    $result = [0, 1];

    while (true) {


        $number = $result[array_key_last($result) - 1] + end($result);
        if ($number < $limit)
            $result[] =  $number;
        else
            break;
    };

    return implode(" ", $result);
}