<?php
/**
 * Test suite for the PEAR_Info class
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
 * @copyright 2007-2009 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     File available since Release 1.7.0
 */

// Keep tests from running twice when calling this file directly via PHPUnit.
$call_main = false;
if (strpos($_SERVER['argv'][0], 'phpunit') === false) {
    // Called via php, not PHPUnit.  Pass the request to PHPUnit.
    if (!defined('PHPUnit_MAIN_METHOD')) {
        define("PHPUnit_MAIN_METHOD", "PEAR_Info_AllTests::main");
        $call_main = true;
    }
}

if ($fp = @fopen('PHPUnit/Autoload.php', 'r', true)) {
    require_once 'PHPUnit/Autoload.php';
} elseif ($fp = @fopen('PHPUnit/Framework.php', 'r', true)) {
    require_once 'PHPUnit/Framework.php';
    require_once 'PHPUnit/Extensions/PhptTestSuite.php';
} else {
    die('skip could not find PHPUnit');
}
fclose($fp);


/**
 * Test suite class to test standard PEAR_Info API.
 *
 * Run all tests from the package root directory:
 * #phpunit -d error_reporting='E_ALL & ~E_STRICT & ~E_DEPRECATED' tests
 * or
 * #php -d error_reporting='E_ALL & ~E_STRICT & ~E_DEPRECATED' tests/AllTests.php
 *
 * or for code coverage testing
 * #phpunit --coverage-html tests/coverage -d error_reporting='E_ALL & ~E_STRICT & ~E_DEPRECATED' tests
 *
 * After the code coverage test browse the index.html file in tests/coverage.
 * The code coverage is close to 100%.
 *
 * @category  PEAR
 * @package   PEAR_Info
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2009 Laurent Laville
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PEAR_Info
 * @since     Class available since Release 1.7.0
 */
class PEAR_Info_AllTests extends PHPUnit_Extensions_PhptTestSuite
{
    /**
     * Runs the test suite
     *
     * @return void
     * @static
     * @since  1.7.0
     */
    public static function main()
    {
        if (!function_exists('phpunit_autoload')) {
            require_once 'PHPUnit/TextUI/TestRunner.php';
        }
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Runs the test suite
     *
     * @return object the PHPUnit_Framework_TestSuite object
     * @static
     * @since  1.9.3
     */
    public static function suite()
    {
        $dir   = dirname(__FILE__);
        $suite = new PHPUnit_Framework_TestSuite('PEAR_Info Test Suite');
        $suite->addTestSuite(new PEAR_Info_AllTests($dir));
        return $suite;
    }
}

if ($call_main) {
    PEAR_Info_AllTests::main();
}
