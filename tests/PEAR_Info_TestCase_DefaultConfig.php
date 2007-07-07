<?php
/**
 * PEAR_Info unit test case for default configuration files usage.
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
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestCase_DefaultConfig::main");
}

require_once 'PHPUnit/Framework.php';

/**
 * Unit test case for PEAR_Info default configuration files usage.
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

class PEAR_Info_TestCase_DefaultConfig extends PHPUnit_Framework_TestCase
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

        $suite = new PHPUnit_Framework_TestSuite("PEAR_Info test default configuration");
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
        $sysconfdir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sysconf_dir';
        putenv("PHP_PEAR_SYSCONF_DIR=" . $sysconfdir);
        // debug-code: error_log('set PHP_PEAR_SYSCONF_DIR to ' . getenv('PHP_PEAR_SYSCONF_DIR'), 0);

        // we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
        require_once '..' . DIRECTORY_SEPARATOR . 'Info.php';
        // debug-code: error_log('pear_info included from = '. realpath('..' . DIRECTORY_SEPARATOR), 0);

        if (OS_WINDOWS) {
            $conf_file = $sysconfdir . DIRECTORY_SEPARATOR . 'pearsys.ini';
        } else {
            $conf_file = $sysconfdir . DIRECTORY_SEPARATOR . 'pear.conf';
        }

        if (!file_exists($conf_file)) {
            $config =& PEAR_Config::singleton();
            $config->set('php_dir', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pear_dir');
            $config->writeConfigFile($conf_file);
            // debug-code: error_log('write pear config file : ' . $conf_file, 0);
        }
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
        // debug-code:  error_log('restore PHP_PEAR_SYSCONF_DIR to ' . getenv('PHP_PEAR_SYSCONF_DIR'), 0);
    }

    /**
     * Test default class constructor without parameter.
     *
     * Must use the pear config files into the default system directory (PEAR_CONFIG_SYSCONFDIR).
     *
     * @access public
     * @since  1.7.0
     */
    public function testConfigFilesExistInSysConfDir()
    {
        $pearInfo = new PEAR_Info();
        $this->assertTrue(is_a($pearInfo, 'PEAR_Info'));
    }
}

// Call PEAR_Info_TestCase_DefaultConfig::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestCase_DefaultConfig::main") {
    PEAR_Info_TestCase_DefaultConfig::main();
}
?>