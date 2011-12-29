--TEST--
PEAR_Info using custom configuration
--FILE--
<?php
$conf_dir_var = 'peardir';
include dirname(__FILE__) . '/file_create.inc';

/**
 * TestCase 1:
 * class constructor with only $pear_dir parameter
 *
 * Will try to detect if default user config files (pear.ini | .pearrc),
 * and/or default system config files (pearsys.ini | pear.conf) are available
 * into $peardir directory.
 */
$testCase = 'testConfigFilesExistWithDefaultNameInPearDir';

$GLOBALS['_PEAR_Config_instance'] = null;

$pear_dir = $peardir;

// try to load PEAR system config ($conf_file) from system dir
$pearInfo = new PEAR_Info($pear_dir);

$result = (!is_null($pearInfo->reg))
    ? 'OK' : 'System PEAR configuration files does not exist';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 2:
 * class constructor with 3 parameters ($pear_dir, $user_file, and $system_file)
 *
 * Will try to detect if user config files and/or system config files
 * are available into $peardir directory.
 */
$testCase = 'testConfigFilesExistWithCustomNameInPearDir';

$GLOBALS['_PEAR_Config_instance'] = null;

$pear_dir    = $peardir;
$user_file   = '';
$system_file = $custom_file1;

// try to load PEAR system config ($custom_file1) from pear dir
$pearInfo = new PEAR_Info($pear_dir, $user_file, $system_file);

$result = (!is_null($pearInfo->reg))
    ? 'OK' : 'User PEAR configuration files does not exist';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 3:
 * class constructor with parameters ($user_file, and $system_file)
 *
 * Will try to detect if user config files and/or system config files
 * are available into user directory.
 */
$testCase = 'testConfigFilesExistInUserDir';

$GLOBALS['_PEAR_Config_instance'] = null;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $user_file   = $userdir . $ds . 'name2.pear.ini';
    $system_file = $userdir . $ds . 'name2.pearsys.ini';
} else {
    $user_file   = $userdir . $ds . 'name2.pearrc';
    $system_file = $userdir . $ds . 'name2.pear.conf';
}
$pear_dir = '';

// try to load PEAR system config ($system_file) from user dir,
// because user config ($user_file) does not exists (volontary)
$pearInfo = new PEAR_Info($pear_dir, $user_file, $system_file);

$result = (!is_null($pearInfo->reg))
    ? 'OK' : 'User PEAR configuration files does not exist';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 4:
 * class constructor with only $pear_dir parameter
 *
 * No user or system file exists into pear directory.
 * Will display error to prevent unexpected behavior.
 */
$testCase = 'testNoConfigFilesFoundIntoPearDir';

$GLOBALS['_PEAR_Config_instance'] = null;

$pear_dir = $dir . $ds . 'pear2_dir';

$pearInfo = new PEAR_Info($pear_dir);

$result = (is_null($pearInfo->reg))
    ? 'OK' : 'User PEAR configuration files does not exist';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 5:
 * class constructor with only $user_file and $system_file parameters
 *
 * No such (name3) user or system file exists into user directory.
 * Will display an error to prevent unexpected behavior.
 */
$testCase = 'testNoConfigFilesFoundIntoUserDir';

$GLOBALS['_PEAR_Config_instance'] = null;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $user_file   = $userdir . $ds . 'name3.pear.ini';
    $system_file = $userdir . $ds . 'name3.pearsys.ini';
} else {
    $user_file   = $userdir . $ds . 'name3.pearrc';
    $system_file = $userdir . $ds . 'name3.pear.conf';
}
$pear_dir = '';

$pearInfo = new PEAR_Info($pear_dir, $user_file, $system_file);

$result = (is_null($pearInfo->reg))
    ? 'OK' : strip_tags($pearInfo->info);

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 6:
 * class constructor with only $options parameter
 */
$testCase = 'testShowResultsWithRenderOptions';

$GLOBALS['_PEAR_Config_instance'] = null;

$pear_dir    = $peardir;
$user_file   = '';
$system_file = '';
$options     = array(
    'resume' => PEAR_INFO_FULLPAGE |
                PEAR_INFO_GENERAL | PEAR_INFO_CHANNELS | PEAR_INFO_PACKAGES_VERSION |
                PEAR_INFO_CREDITS_PACKAGES,
    'channels' => array()
);

// try to load PEAR system config from pear dir
$pearInfo = new PEAR_Info($pear_dir, $user_file, $system_file, $options);

$result = (!is_null($pearInfo->reg))
    ? 'OK' : 'KO';

echo $testCase . ' : ' . $result;
echo "\n";

/**
 * TestCase 7:
 * class constructor with invalid $peardir parameter
 */
$testCase = 'testInvalidPearDir';

$GLOBALS['_PEAR_Config_instance'] = null;

$pear_dir = $dir . $ds . 'invalid_pear_dir';

$pearInfo = new PEAR_Info($pear_dir);

$result = (is_null($pearInfo->reg))
    ? 'OK' : 'Valid PEAR directory found';

echo $testCase . ' : ' . $result;
?>
--CLEAN--
<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
$conf_dir_var = 'peardir';
include dirname(__FILE__) . '/file_cleanup.inc';
?>
--EXPECT--
testConfigFilesExistWithDefaultNameInPearDir : OK
testConfigFilesExistWithCustomNameInPearDir : OK
testConfigFilesExistInUserDir : OK
testNoConfigFilesFoundIntoPearDir : OK
testNoConfigFilesFoundIntoUserDir : OK
testShowResultsWithRenderOptions : OK
testInvalidPearDir : OK
