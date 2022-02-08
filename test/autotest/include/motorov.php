<?php
function search(array $data, int $number) : int
{
    $i = 0;
    $count = count($data);
    while($i < $count)
    {
        if($data[$i] == $number)return $i;
        $i++;
    }
    return -1;
}
function weekend (string $begin, string $end) : int
{
    $nach = strtotime($begin);
    $kon = strtotime($end);
    $i = 0;
    while((date("w", $nach) != 0) && (date("w", $nach) != 6) && ($i < 7) && ($nach < $kon))		//смещение до ближайшего выходного
    {
        $nach = $nach + (60*60*24);
        $i++;
    }
    $diff = abs($kon - $nach)/(60*60*24);
    $week = intdiv(abs($kon - $nach), (60*60*24*7));	//количество недель
    $ostatok = $diff - $week*7;		//остаток дней
    if($diff == 0)
    {
        if((date("w", $nach) == 0) || (date("w", $nach) == 6))return  1;
        else return 0;
    }
    else{
        $nach = $nach + $week * (60*60*24*7);
        $ss = 0;
        for($i = 0; $i <= $ostatok; $i++)
        {
            if((date("w", $nach) == 0) || (date("w", $nach) == 6))$ss++;
            $nach = $nach + (60*60*24);
        }
        return $week*2 + $ss;
    }
    return 0;
}
function rgb(int $r, int $g, int $b):int
{
    $otvet = 0;
    $otvet = ($otvet | $b) << 8;
    $otvet = ($otvet | $g) << 8;
    $otvet = $otvet | $r;
    return $otvet;
}

function fiborow(int $limit) : string
{
    $otvet = "";
    $n1 = 0;
    $n2 = 1;
    $otvet = (string)$n1." ".(string)$n2;
    for($i = 3; $i < $limit - 2; $i++)
    {
        $n3 = $n1 + $n2;
        $otvet .= " ".(string)$n3;
        $n1 = $n2;
        $n2 = $n3;
    }
    return $otvet;
}