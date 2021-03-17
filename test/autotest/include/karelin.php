<?php
function search(array $data, int $number) : int {
    $numberDataElements = count( $data );
    $result = -1;
    if ( $numberDataElements == 0 ) {
        return $result;
    }
    if ( $numberDataElements > 1 ) {
        $chunkedArrays = array_chunk( $data, count( $data ) / 2, true );
        foreach ( $chunkedArrays as $arrayToSearch ) {
            $result = search( $arrayToSearch, $number );
            if ( $result != -1 ) {
                break;
            }
        }
    } else if ( reset( $data ) === $number ) {
        $keys = array_keys( $data );
        $result = reset( $keys );
    }
    return $result;
}
function weekend (string $begin, string $end) : int {
    $period = new DatePeriod(
        new DateTime( $begin ),
        new DateInterval('P1D'),
        new DateTime( $end )
    );

    $counter = 0;
    foreach ( $period as $key => $value ) {
        if ( $value->format('N') >= 6 ) {
            $counter++;
        }
    }

    return $counter;
}
function rgb(int $r, int $g, int $b):int {
    return $r*pow(256, 2) + $g*pow(256, 1) + $b*pow(256, 0);
}
function fiborow(int $limit) : string {
    $first = 0;
    $second = 1;
    $result = $first . " " . $second;
    while( $first + $second <= $limit ) {
        $s = $first;
        $first = $second;
        $second = $first + $s;
        $result .= " " . $second;
    }
    return $result;
}