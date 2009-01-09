<?php
/**
 * PEAR_Info Package Script Generator
 *
 * Generate a new fresh version of package xml 2.0
 * built with PEAR_PackageFileManager 1.6.0+
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category PHP
 * @package  PEAR_Info
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Info
 * @since    File available since Release 1.7.0b1
 * @ignore
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagefile = 'c:/php/pear/PEAR_Info/package2.xml';

$options = array('filelistgenerator' => 'cvs',
    'packagefile' => 'package2.xml',
    'baseinstalldir' => 'PEAR',
    'addhiddenfiles' => true,
    'simpleoutput' => true,
    'clearcontents' => false,
    'changelogoldtonew' => false,
    'ignore' => array(__FILE__)
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->addRelease();
$p2->generateContents();
$p2->setReleaseVersion('1.9.1');
$p2->setAPIVersion('1.8.0');
$p2->setReleaseStability('stable');
$p2->setAPIStability('stable');
$p2->setNotes('
* bugs
- additional fixed for CLI output about request #14543

* QA
- add more unit tests to improve code coverage from 61 to 82%
');
//$p2->setLicense('PHP License 3.01', 'http://www.php.net/license/3_01.txt');
//$p2->setPearinstallerDep('1.5.4');
/*$p2->addPackageDepWithChannel('optional',
                                'PHPUnit', 'pear.phpunit.de', '3.1.4');*/
/*$p2->addPackageDepWithChannel('required',
                              'Console_Getargs', 'pear.php.net', '1.3.3'); */
/*$p2->addMaintainer('lead', 'farell',
                     'Laurent Laville', 'pear@laurent-laville.org'); */
//$p2->addReplacement('Info.php', 'pear-config', '@data_dir@', 'data_dir');
//$p2->addReplacement('Info.php', 'package-info', '@package_name@', 'name');
/*$p2->addReplacement('AllTests.php',
                    'package-info', '@package_version@', 'version'); */
/*$p2->addReplacement('PEAR_Info_TestSuite_Standard.php',
                    'package-info', '@package_version@', 'version'); */
//$p2->addReplacement('Cli.php', 'package-info', '@package_version@', 'version');
//$p2->addReplacement('pearinfo', 'pear-config', '@php_bin@', 'php_bin');
//$p2->addReplacement('pearinfo.bat', 'pear-config', '@php_bin@', 'php_bin');
//$p2->addReplacement('pearinfo.bat', 'pear-config', '@php_dir@', 'php_dir');
//$p2->addReplacement('pearinfo.bat', 'pear-config', '@bin_dir@', 'bin_dir');

if (isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>