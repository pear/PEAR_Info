<?php
/**
 * Generate phpinfo() style PEAR information, embedded into user-defined html template
 *
 * @author    Laurent Laville
 * @package   PEAR_Info
 * @version   $Id$
 * @ignore
 */

// require the PEAR_Info file
require_once 'PEAR/Info.php';

class PEAR_Info3 extends PEAR_Info
{
    function PEAR_Info3($pear_dir = '', $user_file = '', $system_file = '')
    {
        $this->__construct($pear_dir, $user_file, $system_file);
    }

    function toHtml()
    {
        $styles = basename($this->getStyleSheet(false));

        $body = $this->info;

        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Laurent Laville" />
<title>My PEAR_Info()</title>
<link rel="stylesheet" type="text/css" href="$styles" />
</head>
<body>

<div id="header">
<h1>Laurent-Laville.org</h1>
</div>

<div id="footer">
</div>

<div id="contents">
$body
</div>

</body>
</html>
HTML;
        return $html;
    }
}

// Create PEAR_Info object
$info = new PEAR_Info3();

// set your own styles, rather than use the default stylesheet
$info->setStyleSheet(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pearinfo3.css');

// Display PEAR_Info output
$info->display();
?>