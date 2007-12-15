<?php
/**
 * BUG #11524 Fatal error with $pear_dir parameter
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Info
 * @link     http://pear.php.net/bugs/bug.php?id=11524
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// Create PEAR_Info object
$info = new PEAR_Info('c:\wamp\php');

// Display PEAR_Info output
$info->display();
?>