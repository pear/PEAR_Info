<?php
/**
 * Outputs lots of PEAR information
 *
 * The output may be customized by passing one or more of the following constants
 * bitwise values summed together in the optional 'resume' options parameter.
 * One can also combine the respective constants or bitwise values together
 * with the or operator.
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @ignore
 */

require_once 'PEAR/Info.php';

error_reporting(E_ALL);

#$options = array('resume' => PEAR_INFO_PACKAGES_CHANNEL);
#$options = array('resume' => PEAR_INFO_PACKAGES_SUMMARY);
#$options = array('resume' => PEAR_INFO_PACKAGES_VERSION);
#$options = array('resume' => PEAR_INFO_PACKAGES_LICENSE);
#$options = array('resume' => PEAR_INFO_PACKAGES_DESCRIPTION);
#$options = array('resume' => PEAR_INFO_PACKAGES_DEPENDENCIES);
#$options = array('resume' => PEAR_INFO_PACKAGES_UPDATE);
#$options = array('resume' => PEAR_INFO_PACKAGES_UPDATE | PEAR_INFO_PACKAGES_XML);
#$options = array('resume' => PEAR_INFO_CHANNELS | PEAR_INFO_PACKAGES_VERSION);
#$options = array('resume' => PEAR_INFO_PACKAGES_VERSION);
#$options = array('resume' => PEAR_INFO_CHANNELS);
#$options = array('resume' => PEAR_INFO_CONFIGURATION);
#$options = array('resume' => PEAR_INFO_CREDITS);
#$options = array('resume' => PEAR_INFO_GENERAL | PEAR_INFO_CREDITS | PEAR_INFO_CONFIGURATION);
$options = array('resume' =>  PEAR_INFO_GENERAL | PEAR_INFO_CHANNELS | PEAR_INFO_PACKAGES_VERSION | PEAR_INFO_FULLPAGE,
    'channels' => array('pear.php.net', '__uri')
);

$info = new PEAR_Info('', 'c:\wamp\php\pear.ini', '', $options);

$info->display();
?>