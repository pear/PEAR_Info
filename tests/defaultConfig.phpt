--TEST--
PEAR_Info using default configuration
--FILE--
<?php
$conf_dir_var = 'sysconfdir';
include dirname(__FILE__) . '/file_create.inc';

/**
 * TestCase 1:
 * default class constructor without parameter
 *
 * Must use the pear config files into the default system directory
 * (PEAR_CONFIG_SYSCONFDIR).
 */
$testCase = 'testConfigFilesExistInSysConfDir';

$pearInfo = new PEAR_Info();

$result = (!is_null($pearInfo->reg))
    ? 'OK' : 'System PEAR configuration files does not exist';

echo $testCase . ' : ' . $result;
?>
--CLEAN--
<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
$conf_dir_var = 'sysconfdir';
include dirname(__FILE__) . '/file_cleanup.inc';
?>
--EXPECT--
testConfigFilesExistInSysConfDir : OK
