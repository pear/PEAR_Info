<?php
/**
 * PEAR_Info Package Script Generator
 *
 * Generate a new fresh version of package xml 2.0 built with PEAR_PackageFileManager 1.6.0+
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PHP
 * @package    PEAR_Info
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_Info
 * @since      File available since Release 1.7.0b1
 * @ignore
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagefile = 'c:/php/pear/PEAR_Info/package2.xml';

$options = array('filelistgenerator' => 'cvs',
    'packagefile' => 'package2.xml',
    'baseinstalldir' => 'PEAR',
    'simpleoutput' => true,
    'clearcontents' => false,
    'changelogoldtonew' => false,
    'ignore' => array('package.php')
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->generateContents();
$p2->addRelease();
$p2->setReleaseVersion('1.7.0RC3');
$p2->setAPIVersion('1.7.0');
$p2->setReleaseStability('beta');
$p2->setAPIStability('stable');
$p2->setNotes('* changes
- Credits page is now customizable with PEAR_INFO_CREDITS_* constant
- You may have a standalone html page (default behavior) with PEAR_INFO_FULLPAGE
  or just part of html code related to information wanted ; Easy to include in your own pages
- new method that give PEAR members list (president, group, docs, website)

Stable release 1.7.0 is scheduled for July 17. This is the last release candidate.
');
//$p2->setLicense('PHP License 3.01', 'http://www.php.net/license/3_01.txt');
//$p2->addMaintainer('lead', 'farell', 'Laurent Laville', 'pear@laurent-laville.org');
//$p2->addReplacement('Info.php', 'pear-config', '@data_dir@', 'data_dir');
//$p2->addReplacement('Info.php', 'package-info', '@package_name@', 'name');
//$p2->addReplacement('Info.php', 'package-info', '@package_version@', 'version');

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>