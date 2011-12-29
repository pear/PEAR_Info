<?php

include dirname(__FILE__) . '/file_cleanup.inc';
require_once 'PEAR/Config.php';

// write once PEAR system-wide config file for simulation
$config =& PEAR_Config::singleton();
$config->set('php_dir', $peardir);
$config->writeConfigFile($conf_file);

// also writes custom pear system config files
$config->writeConfigFile($custom_file1);
$config->writeConfigFile($custom_file2);
