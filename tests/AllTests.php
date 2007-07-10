<?php
/**
 * PEAR_Info no-regression test suite
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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PEAR_Info_AllTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

chdir(dirname(__FILE__));

require_once 'PEAR_Info_TestCase_DefaultConfig.php';
require_once 'PEAR_Info_TestCase_CustomConfig.php';
require_once 'PEAR_Info_TestCase_Install.php';
require_once 'PEAR_Info_TestCase_Output.php';

/**
 * PEAR_Info no-regression test suite
 *
 * Run all tests from the package root directory:
 * #phpunit PEAR_Info_AllTests tests/AllTests.php
 * or
 * #php tests/AllTests.php
 * or for code coverage testing
 * #phpunit --report tests/coverage PEAR_Info_AllTests tests/AllTests.php
 *
 * After the code coverage test browse the index.html file in tests/coverage.
 * The code coverage is close to 100%.
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

class PEAR_Info_AllTests
{
    /**
     * Runs the test suite
     *
     * @return void
     * @access public
     * @static
     * @since  1.7.0
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Runs the test suite
     *
     * @return object the PHPUnit_Framework_TestSuite object
     * @access public
     * @static
     * @since  1.7.0
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PEAR_Info Test Suite');
        $suite->addTestSuite('PEAR_Info_TestCase_DefaultConfig');
        $suite->addTestSuite('PEAR_Info_TestCase_CustomConfig');
        $suite->addTestSuite('PEAR_Info_TestCase_Install');
        $suite->addTestSuite('PEAR_Info_TestCase_Output');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'PEAR_Info_AllTests::main') {
    PEAR_Info_AllTests::main();
}

?>