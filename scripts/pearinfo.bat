@echo off

rem CLI Script to generate phpinfo() style PEAR information

rem PHP versions 4 and 5

rem LICENSE: This source file is subject to version 3.01 of the PHP license
rem that is available through the world-wide-web at the following URI:
rem http://www.php.net/license/3_01.txt.  If you did not receive a copy of
rem the PHP License and are unable to obtain it through the web, please
rem send a note to license@php.net so we can mail you a copy immediately.

rem @category PEAR
rem @package  PEAR_Info
rem @author   Laurent Laville <pear@laurent-laville.org>
rem @license  http://www.php.net/license/3_01.txt  PHP License 3.01
rem @version  CVS: $Id: pearinfo.bat,v 1.1 2008-03-23 10:15:10 farell Exp $
rem @link     http://pear.php.net/package/PEAR_Info
rem @since    File available since Release 1.8.0

"@php_bin@" -d include_path=".;@php_dir@" -f "@bin_dir@/pearinfo" -- %*