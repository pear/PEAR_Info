<?php
/**
 * Check packages installed
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

$res = PEAR_Info::packageInstalled('Role_Web', '1.1.0', 'pearified');
var_dump($res);

$res = PEAR_Info::packageInstalled('PEAR_PackageFileManager', '1.6.0');
var_dump($res);

?>