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
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007 Laurent Laville
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     File available since Release 1.7.0
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestCase_CustomConfig::main");
}

require_once 'PHPUnit/Framework.php';

/**
 * Unit test case for PEAR_Info custom configuration files usage.
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007 Laurent Laville
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     Class available since Release 1.7.0
 */
class PEAR_Info_TestCase_CustomConfig extends PHPUnit_Framework_TestCase
{
    /**
     * Tells what is your local primary PEAR install directory (for simulation)
     *
     * @var    string
     * @since  1.7.0
     */
    private $peardir;

    /**
     * Tells what is your local secondary PEAR install directory (for simulation)
     *
     * @var    string
     * @since  1.7.0
     */
    private $userdir;

    /**
     * Runs the test methods of this class.
     *
     * @static
     * @return void
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("PEAR_Info "
            . "test custom configuration");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        chdir(dirname(__FILE__));
        $ds = DIRECTORY_SEPARATOR;

        // we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
        // in PEAR Info TestCase DefaultConfig
        include_once '..' . $ds . 'Info.php';

        $this->peardir = dirname(__FILE__) . $ds . 'pear_dir';
        $this->userdir = dirname(__FILE__) . $ds . 'user_dir';

        // prepare a simulation of a PEAR install
        if (OS_WINDOWS) {
            $conf_file    = $this->peardir . $ds . 'pearsys.ini';
            $custom_file1 = $this->peardir . $ds . 'name1.pearsys.ini';
            $custom_file2 = $this->userdir . $ds . 'name2.pearsys.ini';
        } else {
            $conf_file    = $this->peardir . $ds . 'pear.conf';
            $custom_file1 = $this->peardir . $ds . 'name1.pear.conf';
            $custom_file2 = $this->userdir . $ds . 'name2.pear.conf';
        }

        // write once PEAR system-wide config file for simulation
        if (!file_exists($conf_file)) {
            $config =& PEAR_Config::singleton();
            $config->set('php_dir', $this->peardir);
            $config->writeConfigFile($conf_file, 'system');

            // also writes custom pear system config files
            $config->writeConfigFile($custom_file1, 'system');
            $config->writeConfigFile($custom_file2, 'system');
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
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
     * @return void
     * @since  1.7.0
     */
    public function testConfigFilesExistWithDefaultNameInPearDir()
    {
        $GLOBALS['_PEAR_Config_instance'] = null;

        $pearInfo = new PEAR_Info($this->peardir);
        $this->assertNotNull($pearInfo->reg);
    }

    /**
     * Test class constructor with first 3 parameters
     * ($pear_dir, $user_file, and $system_file).
     *
     * Will try to detect if user config files and/or system config files
     * are available into $pear_dir directory.
     *
     * @return void
     * @since  1.7.0
     */
    public function testConfigFilesExistWithCustomNameInPearDir()
    {
        $ds = DIRECTORY_SEPARATOR;
        // both files are not necessary, we creates only pear system file
        if (OS_WINDOWS) {
            $systemFile = $this->peardir . $ds . 'name1.pearsys.ini';
        } else {
            $systemFile = $this->peardir . $ds . 'name1.pear.conf';
        }
        $userFile                         = '';
        $GLOBALS['_PEAR_Config_instance'] = null;

        $pearInfo = new PEAR_Info($this->peardir, $userFile, $systemFile);
        $this->assertNotNull($pearInfo->reg);
    }

    /**
     * Test class constructor with parameters ($user_file, and $system_file).
     *
     * Will try to detect if user config files and/or system config files
     * are available into user directory.
     *
     * @return void
     * @since  1.7.0
     */
    public function testConfigFilesExistInUserDir()
    {
        $ds = DIRECTORY_SEPARATOR;
        // both files are not necessary, we creates only pear system file
        if (OS_WINDOWS) {
            $userFile   = $this->userdir . $ds . 'name2.pear.ini';
            $systemFile = $this->userdir . $ds . 'name2.pearsys.ini';
        } else {
            $userFile   = $this->userdir . $ds . 'name2.pearrc';
            $systemFile = $this->userdir . $ds . 'name2.pear.conf';
        }
        $GLOBALS['_PEAR_Config_instance'] = null;

        $pearInfo = new PEAR_Info('', $userFile, $systemFile);
        $this->assertNotNull($pearInfo->reg);
    }

    /**
     * Test class constructor with only $pear_dir parameter.
     *
     * No user or system file exists into pear directory.
     * Will display error to prevent unexpected behavior.
     *
     * @return void
     * @since  1.7.0
     * @see    http://pear.php.net/bugs/bug.php?id=11524  Bug #11524
     */
    public function testNoConfigFilesFoundIntoPearDir()
    {
        $peardir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pear2_dir';

        $GLOBALS['_PEAR_Config_instance'] = null;

        $pearInfo = new PEAR_Info($peardir);
        $this->assertNull($pearInfo->config,
            'Standard config files found into pear directory');
    }

    /**
     * Test class constructor with only $user_file and $system_file parameters.
     *
     * No such (name3) user or system file exists into user directory.
     * Will display an error to prevent unexpected behavior.
     *
     * @return void
     * @since  1.7.0
     */
    public function testNoConfigFilesFoundIntoUserDir()
    {
        $ds = DIRECTORY_SEPARATOR;
        if (OS_WINDOWS) {
            $userFile   = $this->userdir . $ds . 'name3.pear.ini';
            $systemFile = $this->userdir . $ds . 'name3.pearsys.ini';
        } else {
            $userFile   = $this->userdir . $ds . 'name3.pearrc';
            $systemFile = $this->userdir . $ds . 'name3.pear.conf';
        }
        $GLOBALS['_PEAR_Config_instance'] = null;

        $pearInfo = new PEAR_Info('', $userFile, $systemFile);
        $this->assertNull($pearInfo->reg,
            'Custom config files exist into user directory');
    }
}

// Call PEAR_Info_TestCase_CustomConfig::main()
// if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestCase_CustomConfig::main") {
    PEAR_Info_TestCase_CustomConfig::main();
}
?>