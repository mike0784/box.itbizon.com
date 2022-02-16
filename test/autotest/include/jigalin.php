<?php
function search(array $data, int $number)
{
    /*$midl = count($data) % 2;
    $i = 0;
    $j = count($data) - 1;

    while ($data[$midl] <> $number && $i <= $j)
    {
        if ($number > $data[$midl])
        {
            $i = $midl + 1;
        }
        else
        {
            $j = $midl - 1;
            $midl = ($i + $j) % 2;
        }
    }

    if ($i > $j)
    {
        return -1;
    }
    else
    {
        return $midl;
    }*/
    return -1;
}
function weekend(string $begin, string $end)
{
    $endDate = strtotime($end);
    $begDate = strtotime($begin);

    $days = ($endDate - $begDate) / 86400 + 1;

    $sub = $days % 6;
    $vos = $days % 7;

    $colV = $sub + $vos;

    if(date("N", $begin) == 6 || date("N", $begin) == 7)
    {
        $colV = $colV + 1;
    }

    return $colV;
}
function rgb(int $r, int $g, int $b)
{
    $res = 0;

    $res = $b;
    $res = $res << 8 | $g;
    $res = $res << 8 | $r;

    return $res;
}
function fiborow(int $limit)
{
    if($limit < 1)
    {
        return 'error';
    }
    if($limit == 1)
    {
        return '0 1 1';
    }

    $res = '0 1 1 ';

    $a1 = 1;
    $a2 = 1;

    do
    {
        $b = $a1 + $a2;

        $res = $res.$b.' ';

        $a1 = $a2;
        $a2 = $b;
    }
    while($a2 <= $limit);

	return $res;
}