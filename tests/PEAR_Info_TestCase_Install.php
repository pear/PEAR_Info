<?php
/**
 * PEAR_Info unit test case for PEAR packages install.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PEAR
 * @package    PEAR_Info
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2007 Laurent Laville
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_Info
 * @since      File available since Release 1.7.0
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestCase_Install::main");
}

require_once 'PHPUnit/Framework.php';

/**
 * Unit test case to check if a package is installed
 *
 * @category   PEAR
 * @package    PEAR_Info
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2007 Laurent Laville
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_Info
 * @since      Class available since Release 1.7.0
 */

class PEAR_Info_TestCase_Install extends PHPUnit_Framework_TestCase
{
    /**
     * Saves content of PHP_PEAR_SYSCONF_DIR environment variable
     *
     * @var    string
     * @access private
     * @since  1.7.0
     */
    private $sysconfdir;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     * @since  1.7.0
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite = new PHPUnit_Framework_TestSuite("PEAR_Info test PEAR packages install");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     * @since  1.7.0
     */
    protected function setUp()
    {
        chdir(dirname(__FILE__));

        $this->sysconfdir = getenv('PHP_PEAR_SYSCONF_DIR');
        putenv("PHP_PEAR_SYSCONF_DIR=" . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sysconf_dir');

        // we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
        require_once '..' . DIRECTORY_SEPARATOR . 'Info.php';
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     * @since  1.7.0
     */
    protected function tearDown()
    {
        putenv("PHP_PEAR_SYSCONF_DIR=" . $this->sysconfdir);
    }

    /**
     * Test if a package named is installed, under pear.php.net channel.
     *
     * Must use the pear config files into the default system directory (PEAR_CONFIG_SYSCONFDIR).
     *
     * @access public
     * @since  1.7.0
     */
    public function testPackageNameInstall()
    {
        $available = PEAR_Info::packageInstalled('Console_Getopt');
        $this->assertTrue($available, 'Package Console_Getopt is not yet installed');
    }

    /**
     * Test if a package is installed with a minimal version, under pear.php.net channel.
     *
     * Must use the pear config files into the default system directory (PEAR_CONFIG_SYSCONFDIR).
     *
     * @access public
     * @since  1.7.0
     */
    public function testPackageNameVersionInstall()
    {
        $available = PEAR_Info::packageInstalled('Console_Getopt', '1.2.2');
        $this->assertTrue($available,
            'Package Console_Getopt is not installed, or version is less than 1.2.2');
    }

    /**
     * Test if a channel/package named is installed.
     *
     * Must use the pear config files into the default system directory (PEAR_CONFIG_SYSCONFDIR).
     *
     * @access public
     * @since  1.7.0
     */
    public function testPackageNameChannelInstall()
    {
        $available = PEAR_Info::packageInstalled('PHPUnit', null, 'pear.phpunit.de');
        $this->assertTrue($available, 'Package PHPUnit is not yet installed');
    }

    /**
     * Test if a channel/package with a minimal version, is installed.
     *
     * Must use the pear config files into the default system directory (PEAR_CONFIG_SYSCONFDIR).
     *
     * @access public
     * @since  1.7.0
     */
    public function testPackageNameVersionChannelInstall()
    {
        $available = PEAR_Info::packageInstalled('PHPUnit', '3.0.0', 'phpunit');
        $this->assertTrue($available,
            'Package phpunit/PHPUnit is not installed, or version is less than 3.0.0');
    }
}

// Call PEAR_Info_TestCase_Install::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestCase_Install::main") {
    PEAR_Info_TestCase_Install::main();
}
?>