<?php
/**
 * BUG #11524 Fatal error with $pear_dir parameter
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @link      http://pear.php.net/bugs/bug.php?id=11524
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// Create PEAR_Info object
$info = new PEAR_Info('c:\wamp\php');

// Display PEAR_Info output
$info->display();
?>