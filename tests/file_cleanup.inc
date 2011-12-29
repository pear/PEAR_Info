<?php

include dirname(__FILE__) . '/config.inc';

if (file_exists($conf_file)) {
    unlink($conf_file);
    unlink($custom_file1);
    unlink($custom_file2);
}
