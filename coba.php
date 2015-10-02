<?php

$begin = new DateTime( '2010-05-01' );
$end = new DateTime( '2010-05-10' );

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

foreach ( $period as $dt ){
  echo $dt->format( "Y-m-d" );
}
?>
