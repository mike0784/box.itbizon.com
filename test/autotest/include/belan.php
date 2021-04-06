<?php
function search(array $data, int $number) : int
{
    if ($number < $data[0] || $number > $data[count($data) - 1])
        return -1;
    if ($number == $data[0])
        return 0;
    $s = implode('`', $data)."`";
    $c = substr_count($s, '`', 1, strpos($s, "`$number`"));
    return $c == 0 ? -1 : $c;
} //search

function weekend(string $begin, string $end) : int
{
    $d1 = new DateTime($begin);
    $d2 = new DateTime($end);
    if ($d1 > $d2) return -1;
    $between = $d2->diff($d1)->format('%a');
    $b_day = (int) $d1->format('N');
    $e_day = (int) $d2->format('N');
    if ($e_day < 6) $between = $between - $e_day;
    return (int)((($between + $b_day) / 7) * 2 - ($b_day == 7 ? 1 : 0));
} //weekend

function rgb(int $r, int $g, int $b) : int
// Ошибка в условии - перед наименованиями входных параметров отсутствует знак $
{
    return ($r << 16) + ($g << 8) + $b;
} //rgb

function fiborow(int $limit) : string
{
    if ($limit == 0) return '0';
    if ($limit > 0)
    {
        $res = '0 1 1';
        $n1 = 1;
        $n2 = 2;
        while ($n2 <= $limit)
        {
            $res = $res." $n2";
            $n2 = $n1 + $n2;
            $n1 = $n2 - $n1;
        }
        return $res;
    }
    if ($limit < 0)
    {
        $res = '-1 1 0';
        $n1 = -1;
        $n2 = 2;
        while (abs($n2) <= abs($limit))
        {
            $res = "$n2 ".$res;
            $n2 = $n1 - $n2;
            $n1 = $n1 - $n2;
        }
        return $res;
    }
} //fiborow