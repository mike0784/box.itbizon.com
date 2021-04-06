<?php
function search(array $data, int $number) : int
{
    $idx1=0; # first item index
    $idx2=count($data)-1; # last item index

    if ((0 == $idx2) && ($data[$idx2] == $number)) {return $idx2;} # one item only

    $counter = 0;
    while($idx1<>$idx2)
    {
        $i = intval(($idx2+$idx1)/2);
        if ($data[$i] == $number) return $i;

        if ($data[$i] > $number) {$idx2=$i;}
        else {$idx1=$i;};

        if ($data[$idx2] == $number) return $idx2;
        if ($data[$idx1] == $number) return $idx1;

        if (1 == $idx2-$idx1 ) break;

        //Fix
        $counter++;
        if($counter > 10)
            return -2;
    }

    return -1; # number not found
}

function weekend (string $begin, string $end) : int
{
    $begin_sec = strtotime($begin);
    $end_sec = strtotime($end);
    $days=($end_sec - $begin_sec)/86400;

    $begin_w = date('w', $begin_sec); # may be 'N'
    $begin_w = ($begin_w)?$begin_w:7;

    $aligned = $days + $begin_w;
    $weeks = floor($aligned/7);
    $sat=0;
    if (7 == $begin_w) {$sat--;} # first sunday
    if (6 == $aligned%7) {$sat++;} # last saturday

    return $weeks*2+$sat;
}

function rgb(int $r, int $g, int $b):int
{
    #return ($r<<16) + ($g<<8) + $b ; # usual bytes order
    return (($r&0xff)) + (($g&0xff)<<8) + (($b&0xff)<<16); # from task
}

function fiborow(int $limit) : string
{
    $a=0;
    $s="0";
    if (0==$limit) {return $s;}
    $b=1;
    $c=1;

    while($limit > $c)
    {
        $s.=" $c"; # before sum

        $c = $b+$a;
        $a = $b;
        $b = $c;
    }

    return $s;
}