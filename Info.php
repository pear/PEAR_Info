<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Davey Shafik <davey@pixelated-dreams.com>                   |
// +----------------------------------------------------------------------+
//
// $Id$

session_start();

require_once 'PEAR/Command/Common.php';
require_once 'PEAR/Common.php';
require_once 'PEAR/Remote.php';
require_once 'PEAR/Registry.php';

class PEAR_Info extends PEAR_Command_Common
{
    function PEAR_Info($pear_dir = FALSE)
    {
        $this->config = new PEAR_Config();
        if ($pear_dir != FALSE) {
            $this->config->set('php_dir',$pear_dir);
        }
        $this->r = new PEAR_Remote($this->config);
        $this->reg = new PEAR_Registry($this->config->get('php_dir'));
        // get PEARs packageInfo to show version number at the top of the HTML
        $pear = $this->reg->packageInfo("PEAR");
        $this->list_options = false;
        if ($this->config->get('preferred_state') == 'stable') {
            $this->list_options = true;
        }
        ob_start();
        ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>PEAR :: PEAR_Info()</title>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <style type="text/css">
            body {background-color: #ffffff; color: #000000; white-space: normal;}
            body, td, th, h1, h2 {font-family: sans-serif;}
            a:link {color: #006600; text-decoration: none;}
            a:visited { color: #003300; text-decoration: none;}
            a:hover {text-decoration: underline;}
            table {border-collapse: collapse; width: 600px; max-width: 600px; margin-left: auto; margin-right: auto; border: 0px; padding: 0px;}
            td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
            h1 {font-size: 150%; text-align: center;}
            h2 {font-size: 125%; text-align: center;}
            .p {text-align: left;}
            .e {background-color: #006600; font-weight: bold; color: #FFFFFF; width: 100px;}
            .e a:link { color: #FFFFFF; }
            .e a:visited { color: #FFFFFF; }
            .h {background-color: #339900; font-weight: bold;}
            .v {background-color: #D9D9D9;}
            img {float: right; border: 0px;}
        </style>
    </head>
    <body>
        <table>
            <tr class="h">
                <td>
                    <a href="http://pear.php.net/"><img src="<?php echo $_SERVER['PHP_SELF'];?>?pear_image=true" alt="PEAR Logo" /></a><h1 class="p">PEAR <?php echo $pear['version']; ?></h1>
                </td>
            </tr>
        </table>
    <?php
            if (!isset($_GET['credits'])) {
                echo '<h1><a href="' .$_SERVER['PHP_SELF']. '?credits=true">PEAR Credits</a></h1>';
                // Get packageInfo and Show the HTML for the Packages
                $this->getConfig();
                echo '<br />';
                $this->getPackages();

            } else {
                $this->getCredits();
            }
        ?>
    </body>
</html>
    <?php
    $this->info = ob_get_contents();
    ob_end_clean();
    }


    function getPackages() 
    {
        if (!isset($_SESSION['available'])) {
            $_SESSION['available'] = @$this->r->call('package.listAll', $this->list_options);
            $_SESSION['latest'] = @$this->r->call('package.listLatestReleases');
        }
        $available = $_SESSION['available'];
        $latest = $_SESSION['latest'];
        if (PEAR::isError($available)) {
        	unset($_SESSION['available']);
            echo '<h1 style="font-size: 12px;">An Error occured fetching the package list from the remote server. Please try again.</h1>';
            return FALSE;
        }
        if (!is_array($available)) {
        	unset($_SESSION['available']);
            echo '<h1 style="font-size: 12px;">The package list could not be fetched from the remote server. Please try again.</h1>';
            return FALSE;
        }
        if ((PEAR::isError($latest)) || (!is_array($latest))) {
        	$latest = FALSE;
        }
        $packages = '';
        foreach ($available as $name => $info) {
            $installed = $this->reg->packageInfo($name);
                if (strlen($installed['package']) > 1) {
                if (!isset($old_index)) {
                    $old_index = '';
                }
                $current_index = $name{0};
                if (strtolower($current_index) != strtolower($old_index)) {
                    $packages .= '<a name="' .$current_index. '"></a>';
                    $old_index = $current_index;
                    $this->index[] = $current_index;
                }
                $packages .= '
        <h2><a name="pkg_' .trim($installed['package']). '">' .trim($installed['package']). '</a></h2>
        <table>
            <tr class="v">
                <td class="e">
                    Summary
                </td>
                <td>
                    ' .nl2br(htmlentities(trim($installed['summary']))). '
                </td>
            </tr>
            <tr class="v">
                <td class="e">
                    Version 
                </td>
                <td>
                    ' .trim($installed['version']). '
                </td>
            </tr>
            <tr class="v">
                <td class="e">
                    Description
                </td>
                <td>
                    ' .nl2br(htmlentities(trim($installed['description']))). '
                </td>
            </tr>
            <tr class="v">
                <td class="e">
                    State
                </td>
                <td>
                    ' .trim($installed['release_state']). '
                </td>
            </tr>
            <tr class="v">
                <td class="e">
                    Information
                </td>
                <td>
                    <a href="http://pear.php.net/' .trim(strtolower($installed['package'])). '">http://pear.php.net/' .trim(strtolower($installed['package'])). '</a>
                </td>
            </tr>';
            if ($latest != FALSE) {
                if (version_compare($latest[$installed['package']]['version'],$installed['version'],'>')) {
                    $packages .= '<tr class="v">
                    <td class="e">
                        Latest Version
                    </td>
                    <td>
                        <a href="http://pear.php.net/get/' .trim($installed['package']). '">' .$latest[$installed['package']]['version'] . '</a>
                        ('. $latest[$installed['package']]['state']. ')
                    </td>
                    </tr>';
                }
            }
        $packages .= '          <tr>
                <td colspan="2" class="v"><a href="#top">Top</a></td>
            </tr>
        </table>';
            }
        }
        ?>
        <h2><a name="top">PEAR Packages</a></h2>
        <table style="padding: 3px;">
            <tr>
                <td class="e">
                    Index
                </td>
            </tr>
            <tr>
                <td class ="v" style="text-align: center">
        <?php
        foreach ($this->index as $i) {
            ?>
            | <a href="#<?php echo $i; ?>"><?php echo strtoupper($i); ?></a> 
            <?php
        }
        ?>|
                </td>
            </tr>
        </table>
        <br />
        <?php
        echo $packages;
    }
    
    function getConfig()
    {
    	$keys = $this->config->getKeys();
        sort($keys);
    	?>
    	<h2>PEAR Config</h2>
    	<table>
    	<?php
    	foreach ($keys as $key) {
    		if (($key != 'password') && ($key != 'username')) {
    			?>
    			<tr class="v">
    				<td class="e"><?php echo $key; ?></td>
    				<td><?php echo $this->config->get($key); ?></td>
    			</tr>
    			<?php
    		}
    	}
    	?>
    	</table>
    	<?php
    }

    function getCredits() 
    {
        ?>
        <h1>PEAR Credits</h1>
        <table>
            <tr class="h">
                <td>
                    PEAR Website Team
                </td>
            </tr>
            <tr class="v">
                <td>
                    <a href="http://pear.php.net/account-info.php?handle=ssb">Stig Bakken</a>, 
                    <a href="http://pear.php.net/account-info.php?handle=cox">Thomas V.V.Cox</a>,
                    <a href="http://pear.php.net/account-info.php?handle=mj">Martin Jansen</a>,
                    <a href="http://pear.php.net/account-info.php?handle=cmv">Colin Viebrock</a>,
                    <a href="http://pear.php.net/account-info.php?handle=richard">Richard Heyes</a>
                </td>
            </tr>
        </table>
        <br />
        <table>
            <tr class="h">
                <td>
                    PEAR documentation team
                </td>
            </tr>
            <tr class="v">
                <td>
                    <a href="http://pear.php.net/account-info.php?handle=cox">Thomas V.V.Cox</a>,
                    <a href="http://pear.php.net/account-info.php?handle=mj">Martin Jansen</a>,
                    <a href="http://pear.php.net/account-info.php?handle=alexmerz">Alexander Merz</a>
                </td>
            </tr>
        </table>
        <?php
        if (isset($_SESSION['available'])) {
        	$available = $_SESSION['available'];
        	$latest = $_SESSION['latest'];
        } else {
        	$available = FALSE;
        }
        if (PEAR::isError($available)) {
        	unset($_SESSION['available']);
            echo '<h1 style="font-size: 12px;">An Error occured fetching the credits from the remote server. Please try again.</h1>';
            return FALSE;
        }
        if (!is_array($available)) {
        	unset($_SESSION['available']);
            echo '<h1 style="font-size: 12px;">The credits could not be fetched from the remote server. Please try again.</h1>';
            return FALSE;
        }
        echo '<br /><table border="0" cellpadding="3" width="600">';
        echo '<tr class="h"><td>Package</td><td>Maintainers</td></tr>';
        foreach ($available as $name => $info) {
            $installed = $this->reg->packageInfo($name);
            if (strlen($installed['package']) > 1) {
                ?>
                <tr>
                    <td class="e">
                        <a href="http://pear.php.net/<?php echo trim(strtolower($installed['package'])); ?>"><?php echo trim($installed['package']); ?></a>
                        
                    </td>
                    <td class="v">
                        <?php
                        $maintainers = array();
                        foreach ($installed['maintainers'] as $i) {
                            $maintainers[] = '<a href="http://pear.php.net/account-info.php?handle=' .$i['handle']. '">' .$i['name']. '</a>' .' (' .$i['role']. ')';
                        }
                        echo implode(', ',$maintainers);
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        echo '</table>';
    }

    function pearImage() {
        $pear_image = 'R0lGODlhaAAyAMT/AMDAwP3+/TWaAvD47Pj89vz++zebBDmcBj6fDEekFluvKmu3PvX68ujz4XvBS8LgrNXqxeHw1ZnPaa/dgvv9+cLqj8LmltD2msnuls';
        $pear_image .= '3xmszwmf7+/f///wAAAAAAAAAAACH5BAEAAAAALAAAAABoADIAQAX/ICCOZGmeaKqubOtWWjwJphLLgH1XUu//C1Jisfj9YLEKQnSY3GaixWQqQTkYHM4';
        $pear_image .= 'AMulNLJFC9pEwIW/odKU8cqTfsWoTTtcomU4ZjbR4ZP+AgYKCG0EiZ1AuiossEhwEXRMEg5SVWQ6MmZqKWD0QlqCUEHubpaYlExwRPRZioZZVp7KzKQoS';
        $pear_image .= 'DxANDLsNXA5simd2FcQYb4YAc2jEU80TmAAIztPCMcjKdg4OEsZJmwIWWQPQI4ikIwtoVQnddgrv8PFlCWgYCwkI+fp5dkvJ/IlUKMCy6tYrDhNIIKLFE';
        $pear_image .= 'AWCTxse+ABD4SClWA0zovAjcUJFi6EwahxZwoGqHhFA/4IqoICkyxQSKkbo0gDkuBXV4FRAJkRCnTgi2P28IcEfk5xpWppykFJVuScmEvDTEETAVJ6bEp';
        $pear_image .= 'ypcADPkz3pvKVAICHChkC7siQ08zVqu4Q6hgIFEFZuEn/KMgRUkaBmAQs+cEHgIiHVH5EAFpIgW4+NT6LnaqhDwe/Ov7YOmWZp4MkiAWBIl0kAVsJWuzc';
        $pear_image .= 'YpdiNgddc0E8cKBAu/FElBwagMb88ZZKDRAkWJtkWhHh3wwUbKHQJN3wQAaXGR2LpArv5oFHRR34C7Mf6oLXZNfqBgNI7oOLhj1f8PaGpygHQ0xtP8MDV';
        $pear_image .= 'KwYTSKcgxr9/hS6/pCCAAg5M4B9/sWh1YP9/XSgQWRML/idBfKUc4IBET9lFjggKhDYZAELZJYEBI2BDB3ouNBEABwE8gAwiCcSYgAKqPdEVAG7scM8BP';
        $pear_image .= 'PZ4AIlM+OgjAgpMhRE24OVoBwsIFEGFA7ZkQQBWienWxmRa7XDjKZXhBdAeSmKQwgLuUVLICa6VEKIGcK2mQWoVZHCBXJblJUFkY06yAXlGsPIHBEYdYi';
        $pear_image .= 'WHb+WQBgaIJqqoHFNpgMGB7dT5ZQuG/WbBAIAUEEFNfwxAWpokTIXJAWdgoJ9kRFG2g5eDRpXSBpEIF0oEQFaZhDbaSFANRgqcJoEDRARLREtxOQpsPO9';
        $pear_image .= '06ZUeJgjQB6dZUPBAdwcF8KLXXRVQaKFcsRRLJ6vMiiCNKxRE8ECZKgUA3Va4arOAAqdGRWO7uMZH5AL05gvsjQbg6y4NCjQ1kw8TVGcbdoKGKx8j3bGH';
        $pear_image .= '7nARBArqwi0gkFJBrZiXBQRbHoIgnhSjcEBKfD7c3HMhz+JIQSY3t8GGKW+SUhfUajxGzKd0IoHBNkNQK86ZYEqdzYA8AHQpqXRUm80oHs1CAgMoBxzRq';
        $pear_image .= 'vzs9CIKECC1JBp7enUpfXHApwVYNAfo16c4IrYPLVdSAJVob7IAtCBFQGHcs/RRdiUDPHA33oADEAIAOw==';
        header('content-type: image/gif');
        echo base64_decode($pear_image);
    }

    function show() {
        echo $this->info;
    }
}

if (isset($_GET['pear_image'])) {
    PEAR_Info::pearImage();
    exit;
}
?>