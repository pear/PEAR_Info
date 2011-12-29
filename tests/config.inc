<?php

$ds         = DIRECTORY_SEPARATOR;
$dir        = dirname(__FILE__);
$sysconfdir = $dir . $ds . 'sysconf_dir';
$peardir    = $dir . $ds . 'pear_dir';
$userdir    = $dir . $ds . 'user_dir';
$tpldir     = $dir . $ds . 'templates';

putenv("PHP_PEAR_SYSCONF_DIR=" . $sysconfdir);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $u_conf_file   = $peardir . $ds . 'pear.ini';
    $conf_file     = $$conf_dir_var . $ds . 'pearsys.ini';
    $custom_file1  = $peardir . $ds . 'name1.pearsys.ini';
    $custom_file2  = $userdir . $ds . 'name2.pearsys.ini';
} else {
    $u_conf_file   = $peardir . $ds . '.pearrc';
    $conf_file     = $$conf_dir_var . $ds . 'pear.conf';
    $custom_file1  = $peardir . $ds . 'name1.pear.conf';
    $custom_file2  = $userdir . $ds . 'name2.pear.conf';
}

if ('@php_dir@' == '@'.'php_dir'.'@') {
    // This package hasn't been installed.
    // Adjust path to ensure includes find files in working directory.
    set_include_path(dirname(dirname(__FILE__))
        . PATH_SEPARATOR . get_include_path());
}

// we get PEAR_Info class only here due to setting of PEAR_CONFIG_SYSCONFDIR
include_once 'PEAR/Info.php';
