<?php
/**
 * PEAR_Info unit test case to output results.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2008 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     File available since Release 1.7.0
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PEAR_Info_TestCase_Output::main");
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

/**
 * Unit test case to get html code results
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2008 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     Class available since Release 1.7.0
 */

class PEAR_Info_TestCase_Output extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * Saves content of PHP_PEAR_SYSCONF_DIR environment variable
     *
     * @var    string
     * @since  1.7.0
     */
    private $sysconfdir;

    /**
     * Templates directory. Where to find expected output.
     *
     * @var    string
     * @since  1.7.0
     */
    private $tpldir;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     * @static
     * @since  1.7.0
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("PEAR_Info test html output");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @since  1.7.0
     */
    protected function setUp()
    {
        chdir(dirname(__FILE__));
        $ds = DIRECTORY_SEPARATOR;

        $this->tpldir = dirname(__FILE__) . $ds . 'templates';

        $this->sysconfdir = getenv('PHP_PEAR_SYSCONF_DIR');
        putenv("PHP_PEAR_SYSCONF_DIR=" . dirname(__FILE__) . $ds . 'sysconf_dir');

        // we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
        include_once '..' . $ds . 'Info.php';

        $GLOBALS['_PEAR_Config_instance'] = null;

        $this->setOutputCallback(array(&$this, 'normalizeOutput'));
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     * @since  1.7.0
     */
    protected function tearDown()
    {
        putenv("PHP_PEAR_SYSCONF_DIR=" . $this->sysconfdir);
    }

    /**
     * Hook to normalize output buffer
     *
     * @param string $buffer output buffer
     *
     * @return string
     */
    public function normalizeOutput($buffer)
    {
        $tpl = '<tr class="v">\s+<td class="e">\s+'
            . '(Latest Version|Last Modified)'
            . '\s+</td>\s+<td>\s+(.*)+\s+</td>\s+</tr>';

        $buffer = preg_replace("`$tpl`", '', $buffer);

        return str_replace("\r", "", $buffer);
    }

    /**
     * Test the general html output render.
     *
     * @return void
     * @since  1.7.0
     */
    public function testHtmlOutputGeneral()
    {
        $sysconfdir = getenv('PHP_PEAR_SYSCONF_DIR');
        $ds         = DIRECTORY_SEPARATOR;
        if (OS_WINDOWS) {
            $conf_file = $sysconfdir . $ds . 'pearsys.ini';
        } else {
            $conf_file = $sysconfdir . $ds . 'pear.conf';
        }

        $options  = array('resume' =>  PEAR_INFO_GENERAL | PEAR_INFO_FULLPAGE);
        $pearInfo = new PEAR_Info('', '', '', $options);
        // We must specify here the default stylesheet used, because package source
        // did not include the task replacement values
        $css = dirname(__FILE__) . $ds . '..' . $ds . 'pearinfo.css';
        $pearInfo->setStyleSheet($css);
        $styles = $pearInfo->getStyleSheet();

        $html = file_get_contents($this->tpldir . $ds . 'general.tpl');
        $html = str_replace(array('{config_file}', '{script_filename}', '{styles}'),
            array($conf_file, $_SERVER['SCRIPT_FILENAME'], $styles), $html);
        $html = $this->normalizeOutput($html);
        file_put_contents($this->tpldir . $ds . 'general.exp', $html);

        $this->expectOutputString($html);
        $pearInfo->show();
        $html = ob_get_contents();
        file_put_contents($this->tpldir . $ds . 'general.out', $html);
    }

    /**
     * Test the packages html output render.
     *
     * @return void
     * @since  1.7.0
     */
    public function testHtmlOutputPackages()
    {
        $sysconfdir = getenv('PHP_PEAR_SYSCONF_DIR');
        $ds         = DIRECTORY_SEPARATOR;
        if (OS_WINDOWS) {
            $conf_file = $sysconfdir . $ds . 'pearsys.ini';
        } else {
            $conf_file = $sysconfdir . $ds . 'pear.conf';
        }

        $options  = array('resume' =>  PEAR_INFO_FULLPAGE | PEAR_INFO_GENERAL
            | PEAR_INFO_PACKAGES_VERSION | PEAR_INFO_PACKAGES_UPDATE,
            'channels' => array());
        $pearInfo = new PEAR_Info('', '', '', $options);
        // We must specify here the default stylesheet used, because package source
        // did not include the task replacement values
        $css = dirname(__FILE__) . $ds . '..' . $ds . 'pearinfo.css';
        $pearInfo->setStyleSheet($css);
        $styles = $pearInfo->getStyleSheet();

        $html = file_get_contents($this->tpldir . $ds . 'packages.tpl');
        $html = str_replace(array('{config_file}', '{script_filename}', '{styles}'),
            array($conf_file, $_SERVER['SCRIPT_FILENAME'], $styles), $html);
        $html = $this->normalizeOutput($html);
        $this->expectOutputString($html);
        file_put_contents($this->tpldir . $ds . 'packages.exp', $html);

        // we force package update check (PEAR_INFO_PACKAGES_UPDATE)
        // for a better code coverage but without display results
        $html = $pearInfo->toHtml();
        // we get normal output and remove 'Latest version' and 'Last Modified' lines
        $html = $this->normalizeOutput($html);
        echo $html;
        file_put_contents($this->tpldir . $ds . 'packages.out', $html);
    }

    /**
     * Test the credits html output render.
     *
     * @return void
     * @since  1.7.0
     */
    public function testHtmlOutputCredits()
    {
        $sysconfdir = getenv('PHP_PEAR_SYSCONF_DIR');
        $ds         = DIRECTORY_SEPARATOR;
        if (OS_WINDOWS) {
            $conf_file = $sysconfdir . $ds . 'pearsys.ini';
        } else {
            $conf_file = $sysconfdir . $ds . 'pear.conf';
        }

        $options  = array('resume' =>  PEAR_INFO_GENERAL | PEAR_INFO_CREDITS_ALL
            | PEAR_INFO_FULLPAGE);
        $pearInfo = new PEAR_Info('', '', '', $options);
        // We must specify here the default stylesheet used, because package source
        // did not include the task replacement values
        $css = dirname(__FILE__) . $ds . '..' . $ds . 'pearinfo.css';
        $pearInfo->setStyleSheet($css);
        $styles = $pearInfo->getStyleSheet();

        $html = file_get_contents($this->tpldir . $ds . 'credits.tpl');
        $html = str_replace(array('{config_file}', '{script_filename}', '{styles}'),
            array($conf_file, $_SERVER['SCRIPT_FILENAME'], $styles), $html);
        $html = $this->normalizeOutput($html);
        file_put_contents($this->tpldir . $ds . 'credits.exp', $html);

        $this->expectOutputString($html);
        $pearInfo->show();
        $html = ob_get_contents();
        file_put_contents($this->tpldir . $ds . 'credits.out', $html);
    }
}

// Call PEAR_Info_TestCase_Output::main()
// if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "PEAR_Info_TestCase_Output::main") {
    PEAR_Info_TestCase_Output::main();
}
?>