<?php
function search(array $data, int $number) : int {
    $numberIndex = -1;
    if (!empty($data)) {
        foreach ($data as $key => $value) {
            if ($value == $number) {
                $numberIndex = $key;
                break;
            }
        }
    }
    return $numberIndex;
}

function weekend (string $begin, string $end) : int {
    $begin = strtotime($begin);
    $end = strtotime($end);

    $weekendsCount = 0;
    for($i=$begin; $i<=$end;$i=$i+86400) {
        if(date("w",$i) == 0 || date("w",$i) == 6) {
            $weekendsCount+= 1;
        }
    }
    return $weekendsCount;
}

function rgb(int $r, int $g, int $b):int {
    $bitNumber = 256;
    $rgbToDecimal = $r * $bitNumber * $bitNumber + $g * $bitNumber + $b;
    // RGB has 24 bits 23 - 0
    return $rgbToDecimal;
}

function fiborow(int $limit) : string {

    $fiborowStr = '';
    $num1 = 0;
    $num2 = 1;
    $index = 0;
    if ($limit >= 1) {
        $fiborowStr .= $num1;
    }
    while ($index < $limit){
        $num3 = $num2 + $num1;
        $num1 = $num2;
        $num2 = $num3;
        $index = $num3;
        $fiborowStr .= ' '.$num1;
    }
    return $fiborowStr;
}