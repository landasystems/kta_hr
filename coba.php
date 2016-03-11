<?php
//date_default_timezone_set("Asia/Bangkok");
echo date_default_timezone_get();
echo '<br/>';
$date1 = new DateTime(date('Y-m-d', strtotime("2015-11-09T17:00:00.000Z")));
$date2 = new DateTime(date('Y-m-d', strtotime("2015-11-10T16:59:59.999Z")));

print_r($date1);
echo '<br/>';
print_r($date2);

//print_r( new DateTime);
//echo date("Y-m-d H:i:s");
//phpinfo();
//echo date('Y-m-d', strtotime('+' . 0 . ' days', strtotime("2015-11-09T17:00:00.000Z")));
//echo date('Y-m-d',strtotime("2015-11-09T17:00:00.000Z"));
?>

hahahahahaha