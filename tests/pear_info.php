<?php
/* May be required on slower (dial-up) connections
ini_set('default_socket_timeout',600);
ini_set('max_execution_time',600);
ini_set('max_input_time',600); */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// Create PEAR_Info object
$info = new PEAR_Info();

// Display PEAR_Info output
$info->show();

?>