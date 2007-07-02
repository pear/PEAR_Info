<?php
/**
 * Outputs channels list of a PEAR install
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @ignore
 */

require_once 'PEAR/Info.php';

$options = array('resume' =>  PEAR_INFO_GENERAL | PEAR_INFO_CHANNELS,
    'channels' => array()
);

$info = new PEAR_Info('', '', '', $options);
$info->display();
?>