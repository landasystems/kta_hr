<?php
header('Content-disposition: attachment; filename=test.xls');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
print_r($models);

?>