<?php
/**
 * PEAR_Info unit test case for custom configuration files usage.
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
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestCase_CustomConfig::main");
}

require_once 'PHPUnit/Framework.php';

chdir(dirname(__FILE__));

/**
 * Unit test case for PEAR_Info custom configuration files usage.
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
class PEAR_Info_TestCase_CustomConfig extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite = new PHPUnit_Framework_TestSuite("PEAR_Info test custom configuration");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        // we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
        // in PEAR Info TestCase DefaultConfig
        require_once '..' . DIRECTORY_SEPARATOR . 'Info.php';
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * Test class constructor with only $pear_dir parameter.
     *
     * Will try to detect if default user config files (pear.ini | .pearrc),
     * and/or default system config files (pearsys.ini | pear.conf) are available
     * into $pear_dir directory.
     *
     * @access public
     * @since  1.7.0
     */
    public function testConfigFilesExistWithDefaultNameInPearDir()
    {
        $pearDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pear_dir';
        $pearInfo = new PEAR_Info($pearDir);
        $this->assertTrue(is_a($pearInfo, 'PEAR_Info'));
    }

    /**
     * Test class constructor with first 3 parameters ($pear_dir, $user_file, and $system_file).
     *
     * Will try to detect if user config files (named pear.custom.ini),
     * and/or system config files (named pearsys.custom.ini) are available
     * into $pear_dir directory.
     *
     * @access public
     * @since  1.7.0
     */
    public function testConfigFilesExistWithCustomNameInPearDir()
    {
        $pearDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pear_dir' . DIRECTORY_SEPARATOR;
        $userFile = $pearDir . 'pear.custom.ini';
        $systemFile = $pearDir . 'pearsys.custom.ini';
        $pearInfo = new PEAR_Info($pearDir, $userFile, $systemFile);
        $this->assertTrue(is_a($pearInfo, 'PEAR_Info'));
    }

    /**
     * Test class constructor with parameters ($user_file, and $system_file).
     *
     * Will try to detect if user config files (named pear.custom.ini),
     * and/or system config files (named pearsys.custom.ini) are available.
     *
     * @access public
     * @since  1.7.0
     */
    public function testConfigFilesExistInUsrConfDir()
    {
        $userDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'user_dir' . DIRECTORY_SEPARATOR;
        $userFile = $userDir . 'pear.custom.ini';
        $systemFile = $userDir . 'pearsys.custom.ini';

        $pearInfo = new PEAR_Info('', $userFile, $systemFile);
        $this->assertTrue(is_a($pearInfo, 'PEAR_Info'));
    }
}

// Call PEAR_Info_TestCase_CustomConfig::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestCase_CustomConfig::main") {
    PEAR_Info_TestCase_CustomConfig::main();
}
?>