--TEST--
PEAR_Info using render options
--FILE--
<?php
$conf_dir_var = 'peardir';
include dirname(__FILE__) . '/file_create.inc';

/**
 * TestCase 1:
 * usage of stylesheet to customize look and feel
 */
$testCase = 'testCustomStyleSheet';

$GLOBALS['_PEAR_Config_instance'] = null;

$pearInfo   = new PEAR_Info($peardir);
$css_exists = $pearInfo->setStyleSheet($tpldir . $ds . 'blueskin.css');

$result = ($css_exists)
    ? 'OK' : 'CSS file does not exists';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 2:
 * display main page with default stylesheet
 */
$testCase = 'testDefaultStyleSheet';

$GLOBALS['_PEAR_Config_instance'] = null;

$options = array('resume' =>  PEAR_INFO_GENERAL |
                              PEAR_INFO_PACKAGES_VERSION |
                              PEAR_INFO_FULLPAGE,
                 'channels' => array());

$pearInfo = new PEAR_Info($peardir, '', '', $options);

if ('@php_dir@' == '@'.'php_dir'.'@') {
    // This package hasn't been installed.  Get the CSS file manually.
    $pearInfo->setStyleSheet(dirname(dirname(__FILE__)) . '/pearinfo.css');
}

$html = $pearInfo->toHtml();

$packages_tpl = file_get_contents($tpldir . $ds . 'packages.tpl');
$packages_tpl = str_replace(
                    array(
                        '{styles}',
                        '{script_filename}',
                        '{config_file}',
                        '{usr_config_file}',
                        '{sys_config_file}'
                    ),
                    array(
                        $pearInfo->getStyleSheet(),
                        __FILE__,
                        $conf_file,
                        $u_conf_file,
                        $conf_file
                    ),
                    $packages_tpl);

if (OS_WINDOWS) {
    $html = str_replace("\r\n", "\n", $html);
}

if (strcasecmp($html, $packages_tpl) == 0) {
    $result = 'OK';
} else {
    $result = 'HTML strings are not same.  See FAILED* files in tests dir.';
    file_put_contents(dirname(__FILE__)
        . "/FAILED-$testCase-HTML.TXT", $html);
    file_put_contents(dirname(__FILE__)
        . "/FAILED-$testCase-TEMPLATE.TXT", $packages_tpl);
}

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 3:
 * display credits page with default stylesheet
 */
$testCase = 'testCreditsWithDefaultStyleSheet';

$GLOBALS['_PEAR_Config_instance'] = null;

$options = array('resume' =>  PEAR_INFO_GENERAL |
                              PEAR_INFO_CREDITS_ALL |
                              PEAR_INFO_FULLPAGE,
                 'channels' => array());

$pearInfo = new PEAR_Info($peardir, '', '', $options);

if ('@php_dir@' == '@'.'php_dir'.'@') {
    // This package hasn't been installed.  Get the CSS file manually.
    $pearInfo->setStyleSheet(dirname(dirname(__FILE__)) . '/pearinfo.css');
}

ob_start();
$pearInfo->show();
$html = ob_get_contents();
ob_end_clean();

$credits_tpl = file_get_contents($tpldir . $ds . 'credits.tpl');
$credits_tpl = str_replace(
                    array(
                        '{styles}',
                        '{script_filename}',
                        '{config_file}',
                        '{usr_config_file}',
                        '{sys_config_file}'
                    ),
                    array(
                        $pearInfo->getStyleSheet(),
                        __FILE__,
                        $conf_file,
                        $u_conf_file,
                        $conf_file
                    ),
                    $credits_tpl);

if (OS_WINDOWS) {
    $html = str_replace("\r\n", "\n", $html);
}

if (strcasecmp($html, $credits_tpl) == 0) {
    $result = 'OK';
} else {
    $result = 'HTML strings are not same.  See FAILED* files in tests dir.';
    file_put_contents(dirname(__FILE__)
        . "/FAILED-$testCase-HTML.TXT", $html);
    file_put_contents(dirname(__FILE__)
        . "/FAILED-$testCase-CREDITS.TXT", $credits_tpl);
}

echo $testCase . ' : ' . $result;
?>
--CLEAN--
<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
$conf_dir_var = 'peardir';
include dirname(__FILE__) . '/file_cleanup.inc';
?>
--EXPECT--
testCustomStyleSheet : OK
testDefaultStyleSheet : OK
testCreditsWithDefaultStyleSheet : OK
