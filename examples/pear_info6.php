<?php
/**
 * Outputs channels list of a PEAR install
 *
 * PHP versions 4 and 5
 *
 * @category PEAR
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Info
 * @ignore
 */

require_once 'PEAR/Info.php';

$options = array('resume' =>  PEAR_INFO_GENERAL | PEAR_INFO_CHANNELS |
               PEAR_INFO_FULLPAGE,
               'channels' => array());

$info = new PEAR_Info('', '', '', $options);
$info->display();
?>