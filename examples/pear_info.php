<?php
/**
 * Generate default phpinfo() style PEAR information.
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @ignore
 */

/* May be required on slower (dial-up) connections
ini_set('default_socket_timeout', 600);
ini_set('max_execution_time', 600);
ini_set('max_input_time', 600);
*/

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// If you need to set a http_proxy uncomment the line below
// PEAR_Info::setProxy('your.proxy.here');

// Create PEAR_Info object
$info = new PEAR_Info();

// Display PEAR_Info output
$info->display();
?>