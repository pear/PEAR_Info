<?php
/**
 * PEAR_Info unit test case to output results.
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
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestCase_Output::main");
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

/**
 * Unit test case to get html code results
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

class PEAR_Info_TestCase_Output extends PHPUnit_Extensions_OutputTestCase
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
     * Templates directory. Where to find expected output.
     *
     * @var    string
     * @access private
     * @since  1.7.0
     */
    private $tpldir;

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

        $suite = new PHPUnit_Framework_TestSuite("PEAR_Info test html output");
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
        xdebug_start_trace(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'trace.pear_info.out');

        $this->tpldir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates';

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
        xdebug_stop_trace();
        putenv("PHP_PEAR_SYSCONF_DIR=" . $this->sysconfdir);
    }

    /**
     * Test the html output render.
     *
     * @access public
     * @since  1.7.0
     */
    public function testHtmlOutputGeneral()
    {
        $sysconfdir = getenv('PHP_PEAR_SYSCONF_DIR');
        if (OS_WINDOWS) {
            $conf_file = $sysconfdir . DIRECTORY_SEPARATOR . 'pearsys.ini';
        } else {
            $conf_file = $sysconfdir . DIRECTORY_SEPARATOR . 'pear.conf';
        }

        $options = array('resume' =>  PEAR_INFO_GENERAL);
        $pearInfo = new PEAR_Info('', '', '', $options);
        // We must specify here the default stylesheet used, because package source
        // did not include the task replacement values
        $css = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'pearinfo.css';
        $pearInfo->setStyleSheet($css);

        $html = file_get_contents($this->tpldir . DIRECTORY_SEPARATOR . 'general.tpl');
        $html = str_replace(array('{config_file}', '{script_filename}'),
            array($conf_file, $_SERVER['SCRIPT_FILENAME']), $html);
        file_put_contents($this->tpldir . DIRECTORY_SEPARATOR . 'general.exp', $html);

        $this->expectOutputString($html);
        $pearInfo->show();

        $now = ob_get_contents();
        file_put_contents($this->tpldir . DIRECTORY_SEPARATOR . 'general.out', $now);
    }
}

// Call PEAR_Info_TestCase_Output::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestCase_Output::main") {
    PEAR_Info_TestCase_Output::main();
}
?>