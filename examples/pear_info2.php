<?php
/**
 * Generate phpinfo() style PEAR information, with a custom blue skin
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

// Create PEAR_Info object
$info = new PEAR_Info();

// set your own styles, rather than use the default stylesheet
$info->setStyleSheet(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'blueskin.css');

// Display PEAR_Info output
$info->display();
?>