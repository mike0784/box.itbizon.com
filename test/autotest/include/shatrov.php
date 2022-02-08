<?php
/**
 * @param array $arr - массив
 * @param int $what - что искать
 *
 * @return int - индекс искомого в массиве или -1
 */
function search( array &$arr , int $what) : int {
    for ( $count = count( $arr ) , $min = 0 , $max = $count - 1 ; $count && ! ( $min > $max ) ; ) {
        $i = intval( ( $min + $max ) / 2 ) ;

        if ( $arr[ $i ] == $what ) {
            return $i ;
        }
        if ( $arr[ $i ] > $what ) {
            $max = $i - 1 ;

            continue ;
        }

        $min = $i + 1 ;
    }

    return - 1 ;
}

define( 'DAY' , 60 * 60 * 24 ) ;
define( 'WEEKEND' , [ 6 , 7 , ] ) ;

function get_n_date( int $timestamp ) : int {
    return date( 'N' , $timestamp ) ;
}

function to_monday( int $timestamp ) : int {
    return $timestamp - get_n_date( $timestamp ) * DAY ;
}

function is_weekend( int $timestamp ) : bool {
    return in_array( get_n_date( $timestamp ) , WEEKEND ) ;
}

function weekend( string $begin , string $end ) : int {
    $begin = strToTime( $begin ) ;
    $end = strToTime( $end ) ;

    if ( $begin < $end ) {
        return 0 ;
    }
    if ( $begin == $end ) {
        return is_weekend( $begin ) ;
    }

    return ceil( ( to_monday( $end ) - to_monday( $begin ) ) / 7 ) ;
}

function rgb( int $r , int $g , int $b ) : int {
    return $r + $g * 0x100 + $b * 0x100 * 0x100 ;
}

function fiborow( int $limit ) : string {//array {
    list( $prev , $current ) = [ 0 , 1 , ] ;

    if ( $prev >= $limit ) {
        $result = [ ] ;
        return implode(' ', $result);
    }
    if ( $current >= $limit ) {
        $result = [ $prev , ] ;
        return implode(' ', $result);
    }

    $result = [ $prev , $current , ] ;

    while ( true ) {
        $next = $prev + $current ;

        if ( $next >= $limit ) {
            break ;
        }

        list( $prev , $current ) = [ $current , $next , ] ;

        $result[] = $current ;
    }

    //return $result ;
    return implode(' ', $result);
}