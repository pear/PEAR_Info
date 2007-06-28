<?php
/**
 * This package generate phpinfo() style PEAR information.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PEAR
 * @package    PEAR_Info
 * @author     Davey Shafik <davey@pixelated-dreams.com>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @since      File available since Release 1.0.1
 * @link       http://pear.php.net/package/PEAR_Info
 */

require_once 'PEAR/Remote.php';
require_once 'PEAR/Registry.php';

/**
 * The PEAR_Info class generate phpinfo() style PEAR information.
 *
 * @category   PEAR
 * @package    PEAR_Info
 * @author     Davey Shafik <davey@pixelated-dreams.com>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.1
 */

class PEAR_Info
{
    /**
     * Html code for phpinfo() style PEAR information
     *
     * @var    string
     * @access public
     * @since  1.0.1
     */
    var $info;

    /**
     * Style sheet for the custom layout
     *
     * @var    string
     * @access public
     * @since  1.7.0b1
     */
    var $css;

    /**
     * instance of PEAR_config
     *
     * @var    object
     * @access public
     * @since  1.0.1
     */
    var $config;

    /**
     * instance of PEAR_Registry
     *
     * @var    object
     * @access public
     * @since  1.0.1
     */
    var $reg;

    /**
     * PHP 4 style constructor (ZE1)
     *
     * @param  string  $pear_dir         (optional) The PEAR base install directory
     * @param  string  $user_file        (optional) file to read PEAR user-defined options from
     * @param  string  $system_file      (optional) file to read PEAR system-wide defaults from
     * @return void
     * @access public
     * @since  1.0.1
     */
    function PEAR_Info($pear_dir = '', $user_file = '', $system_file = '')
    {
        $this->__construct($pear_dir, $user_file, $system_file);
    }

    /**
     * PHP 5 style constructor (ZE2)
     *
     * @param  string  $pear_dir         (optional) The PEAR base install directory
     * @param  string  $user_file        (optional) file to read PEAR user-defined options from
     * @param  string  $system_file      (optional) file to read PEAR system-wide defaults from
     * @return void
     * @access private
     * @since  1.7.0b1
     */
    function __construct($pear_dir = '', $user_file = '', $system_file = '')
    {
        $this->config =& PEAR_Config::singleton($user_file, $system_file);

        // to keep compatibility with version less or equal than 1.6.1
        if (!empty($pear_dir) && empty($user_file) && empty($system_file)) {
            $this->config->set('php_dir', $pear_dir);
        }
        // to keep compatibility with version less or equal than 1.6.1
        if (defined('PEAR_INFO_PROXY')) {
            $this->config->set('http_proxy', PEAR_INFO_PROXY);
        }

        // Get the config's registry object.
        if (empty($user_file)) {
            if (empty($system_file)) {
                $layer = null;
            } else {
                $layer = 'system';
            }
        } else {
            $layer = 'user';
        }
        $this->reg = &$this->config->getRegistry($layer);

        // get PEARs packageInfo to show version number at the top of the HTML
        if (method_exists($this->reg, 'getPackage')) {
            $pear = $this->reg->getPackage("PEAR");
            $pear_version = $pear->getVersion();
        } else {
            $pear = $this->reg->packageInfo('PEAR');
            $pear_version = $pear['version'];
        }
        $index = array();
        $this->list_options = false;
        if ($this->config->get('preferred_state') == 'stable') {
            $this->list_options = true;
        }
        $this->info = '
<table>
<tr class="h">
    <td>
        <a href="http://pear.php.net/"><img src="{phpself}?pear_image=true" alt="PEAR Logo" /></a><h1 class="p">PEAR {pearversion}</h1>
    </td>
</tr>
</table>
';
        $this->info = str_replace(
            array('{phpself}', '{pearversion}'),
            array(htmlentities($_SERVER['PHP_SELF']), $pear_version),
            $this->info
            );

        if (!isset($_GET['credits'])) {
            $this->info .= '
<h1><a href="{phpself}?credits=true">PEAR Credits</a></h1>
';
            $this->info = str_replace(
                '{phpself}', htmlentities($_SERVER['PHP_SELF']),
                $this->info
                );

            $this->info .= $this->getConfig();
            $this->info .= $this->getChannels();
            $this->info .= $this->getPackages();

        } else {
            $this->info .= $this->getCredits();
        }
    }

    /**
     * Set PEAR http_proxy for remote calls
     *
     * @param  string  $proxy  HTTP Proxy Server Address
     * @static
     * @return bool
     * @access public
     * @since  1.0.6
     */
    function setProxy($proxy)
    {
        $res = define('PEAR_INFO_PROXY', $proxy);
        return $res;
    }

    /**
     * Returns the custom style sheet to use for layout
     *
     * @param  bool  $content (optional) Either return css filename or string contents
     * @return string
     * @access public
     * @since  1.7.0b1
     */
    function getStyleSheet($content = true)
    {
        if ($content) {
            $styles = file_get_contents($this->css);
        } else {
            $styles = $this->css;
        }
        return $styles;
    }

    /**
     * Set the custom style sheet to use your own styles
     *
     * @param  string  $css (optional) File to read user-defined styles from
     * @return bool    True if custom styles, false if default styles applied
     * @access public
     * @since  1.7.0b1
     */
    function setStyleSheet($css = null)
    {
        // default stylesheet is into package data directory
        if (!isset($css)) {
            $this->css = '@data_dir@' . DIRECTORY_SEPARATOR
                 . '@package_name@' . DIRECTORY_SEPARATOR
                 . 'pearinfo.css';
        }

        $res = isset($css) && file_exists($css);
        if ($res) {
            $this->css = $css;
        }
        return $res;
    }

    /**
     * Retrieve and format PEAR Packages info
     *
     * @return string
     * @access private
     * @since  1.0.1
     */
    function getPackages()
    {
        $available = $this->reg->listAllPackages();
        if (PEAR::isError($available)) {
            $e = '<p class="error">An Error occured while fetching the package list.'
               . ' Please try again.</p>';
            return $e;
        }
        if (!is_array($available)) {
            $e = '<p class="error">The package list could not be fetched from the remote server.'
               . ' Please try again.</p>';
            return $e;
        }

        $mirror = $this->config->get('preferred_mirror');
        $channel = 'pear.php.net';
        // Get a channel object.
        $chan =& $this->reg->getChannel($channel);
        if ($chan->supportsREST($mirror) &&
            $base = $chan->getBaseURL('REST1.0', $mirror)) {

            $rest =& $this->config->getREST('1.0', array());
            if (is_object($rest)) {
                $pref_state = 'stable';
                $installed = array_flip($available[$channel]);

                $latest = $rest->listLatestUpgrades($base, $pref_state, $installed,
                              $channel, $this->reg);
            } else {
                $latest = false;
            }
        } else {
            $r =& $this->config->getRemote();
            $latest = @$r->call('package.listLatestReleases');
        }

        if ((PEAR::isError($latest)) || (!is_array($latest))) {
            $latest = false;
        }

        $s = '';
        $anchor_suffix = 0;  // make page XHTML compliant
        foreach ($available as $channel => $pkg) {
            $packages = '';
            $index = array();
            foreach ($pkg as $name) {
                $info = &$this->reg->getPackage($name, $channel);
                if (is_object($info)) {
                    $installed['package'] = $info->getPackage();
                    $installed['channel'] = $channel;
                    $installed['summary'] = $info->getSummary();
                    $installed['version'] = $info->getVersion();
                    $installed['current_release'] = $installed['version']
                        . ' (' . $info->getState() . ') was released on '
                        . $info->getDate();
                    $installed['license'] = $info->getLicense();
                    if ($info->getPackagexmlVersion() == '2.0' ) {
                        $uri = $info->getLicenseLocation();
                        if ($uri) {
                            if (isset($uri['uri'])) {
                                $installed['license'] = '<a href="' . $uri['uri'] . '">'
                                    . $info->getLicense() . '</a>';
                            }
                        }
                    }
                    $installed['description'] = $info->getDescription();
                } else {
                    $installed = $info;
                    $installed['channel'] = 'pear.php.net';
                    $installed['current_release'] = $info['version']
                        . ' (' . $info['release_state'] . ') was released on '
                        . $info['release_date'];
                }

                $deps = $info->getDeps();
                if (is_array($deps)) {
                    static $_deps_rel_trans = array(
                                 'lt' => '<',
                                 'le' => '<=',
                                 'eq' => '=',
                                 'ne' => '!=',
                                 'gt' => '>',
                                 'ge' => '>=',
                                 'has' => 'has',
                                 'not' => 'not'
                                 );
                    static $_deps_type_trans = array(
                                 'pkg' => 'Package',
                                 'ext' => 'Extension',
                                 'php' => 'PHP'
                                 );

                    $dependencies = '';
                    $ptpl = '
<tr class="w">
    <td>
        {dep_required}
    </td>
    <td>
        {dep_type}
    </td>
    <td>
        {dep_name}
    </td>
    <td>
        {dep_rel}
    </td>
    <td>
        {dep_version}
    </td>
</tr>
';
                    foreach($deps as $dep) {
                        if (!isset($dep['optional'])) {
                            $dep['optional'] = '';
                        }
                        $dependencies .= str_replace(
                            array('{dep_required}',
                                '{dep_type}',
                                '{dep_name}',
                                '{dep_rel}',
                                '{dep_version}',
                                ),
                            array(($dep['optional'] == 'no') ? 'Yes' : 'No',
                                $_deps_type_trans[$dep['type']],
                                isset($dep['name']) ? $dep['name'] : '',
                                $_deps_rel_trans[$dep['rel']],
                                isset($dep['version']) ? $dep['version'] : ''
                                ),
                            $ptpl
                        );
                    }
                    $ptpl = '
<tr class="w">
    <td class="f">
        Required
    </td>
    <td class="f">
        Type
    </td>
    <td class="f">
        Name
    </td>
    <td class="f">
        Relation
    </td>
    <td class="f">
        Version
    </td>
</tr>
';
                    $dependencies = $ptpl . $dependencies;
                }

                if (!isset($old_index)) {
                    $old_index = '';
                }
                $current_index = $name{0};
                if (strtolower($current_index) != strtolower($old_index)) {
                    $packages .= '<a name="' . $current_index . $anchor_suffix . '"></a>';
                    $old_index = $current_index;
                    $index[] = $current_index;
                }
                $ptpl = '
<h2><a name="pkg_{package}">{package}</a></h2>
<table>
<tr class="v">
    <td class="e">
        Channel
    </td>
    <td>
        {channel}
    </td>
</tr>
<tr class="v">
    <td class="e">
        Summary
    </td>
    <td>
        {summary}
    </td>
</tr>
<tr class="v">
    <td class="e">
        Version
    </td>
    <td>
        {version}
    </td>
</tr>
<tr class="v">
    <td class="e">
        License
    </td>
    <td>
        {license}
    </td>
</tr>
<tr class="v">
    <td class="e">
        Description
    </td>
    <td>
        {description}
    </td>
</tr>';

                if (!empty($dependencies)) {
                    $ptpl .= '
<tr class="v">
    <td class="e">
        Dependencies
    </td>
    <td>
        <table class="d">
        {dependencies}
        </table>
    </td>
</tr>';
                }

                $packages .= str_replace(
                    array('{package}',
                        '{channel}',
                        '{summary}',
                        '{version}',
                        '{license}',
                        '{description}',
                        '{dependencies}'
                        ),
                    array(trim($installed['package']),
                        trim($installed['channel']),
                        nl2br(htmlentities(trim($installed['summary']))),
                        trim($installed['current_release']),
                        trim($installed['license']),
                        nl2br(htmlentities(trim($installed['description']))),
                        $dependencies
                        ),
                    $ptpl
                );

                if ($latest != false) {
                    if (isset($latest[$installed['package']])) {
                        if (version_compare($latest[$installed['package']]['version'],
                            $installed['version'], '>')) {
                            $ptpl = '
<tr class="v">
    <td class="e">
        Latest Version
    </td>
    <td>
        <a href="http://pear.php.net/get/{package}">{latest_version}</a>({latest_state})
    </td>
</tr>';
                            $packages .= str_replace(
                                array('{package}',
                                    '{latest_version}',
                                    '{latest_state}'
                                    ),
                                array(trim($installed['package']),
                                    $latest[$installed['package']]['version'],
                                    $latest[$installed['package']]['state']
                                    ),
                                $ptpl
                                );
                        }
                    }
                }
                $packages .= '
<tr>
    <td colspan="2" class="v"><a href="#{top}">Top</a></td>
</tr>
</table>
';
                $packages = str_replace('{top}', 'top'.$anchor_suffix, $packages);
            }

            $index_header = '
<h2><a name="{top}">Installed Packages, Channel {channel}</a></h2>
<table>
<tr>
    <td class="e">
        Index
    </td>
</tr>
<tr>
    <td class ="v" style="text-align: center">
';
            $index_header = str_replace(array('{channel}', '{top}'),
                array($channel, 'top'.$anchor_suffix), $index_header);
            foreach ($index as $i) {
                $index_header .= ' | <a href="#'.$i.$anchor_suffix.'">'.strtoupper($i).'</a>';
            }
            $index_header .= ' |
    </td>
</tr>
</table>

';
            $s .= $index_header . $packages;
            $anchor_suffix++;
        }
        return $s;
    }

    /**
     * Retrieves and formats the PEAR Config data
     *
     * @return string
     * @access private
     * @since  1.0.1
     */
    function getConfig()
    {
        $keys = $this->config->getKeys();
        sort($keys);

        $html_pear_config = '
<h2>PEAR Config</h2>
<table>';
        foreach ($keys as $key) {
            if (($key != 'password') && ($key != 'username') && ($key != 'sig_keyid') && ($key != 'http_proxy')) {
                $html_config = '
<tr class="v">
    <td class="e">{key}</td>
    <td>{value}</td>
</tr>';
                $html_config = str_replace(
                    array('{key}', '{value}'),
                    array($key, $this->config->get($key)),
                    $html_config
                    );
                $html_pear_config .= $html_config;
            }
        }
        $html_pear_config .= '
</table>

';
        return $html_pear_config;
    }

    /**
     * Retrieves and formats the PEAR Channel data
     *
     * @return string
     * @access private
     * @since  1.7.0b1
     */
    function getChannels()
    {
        $channels = $this->reg->listChannels();
        if (PEAR::isError($channels)) {
            $e = '<p class="error">An Error occured while fetching the channel list.'
               . ' Please try again.</p>';
            return $e;
        }

        $html_pear_channel = '
<h2>PEAR Channels</h2>';

        $anchor_suffix = 0;
        foreach ($channels as $channel) {
            $html_pear_channel .= '
<table>';
            $info = $this->reg->channelInfo($channel);
            if (PEAR::isError($info) || is_null($info)) {
                $e = '<p class="error">An Error occured while fetching '
                   . $channel . ' channel data.'
                   . ' Please try again.</p>';
                return $e;
            }

            $data = array('name' => $info['name']);
            if (isset($info['suggestedalias'])) {
                $data['alias'] = $info['suggestedalias'];
            }
            $data['summary'] = $info['summary'];

            foreach ($data as $key => $value) {
                $html_channel = '
<tr class="v">
    <td class="e">{key}</td>
    <td>{value}</td>
</tr>';
                if ($key == 'name') {
                    $value = '<a href="#top' . $anchor_suffix . '">'
                        . $value . '</a>';
                }
                $html_channel = str_replace(
                    array('{key}', '{value}'),
                    array(ucfirst($key), $value),
                    $html_channel
                    );
                $html_pear_channel .= $html_channel;
            }
            $html_pear_channel .= '
</table>
<br />

';
            $anchor_suffix++;
        }

        return $html_pear_channel;
    }

    /**
     * Retrieves and formats the PEAR Credits
     *
     * @return string
     * @access private
     * @since  1.0.1
     */
    function getCredits()
    {
        $html_pear_credits = '
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
';
        $available = $this->reg->listAllPackages();
        if (PEAR::isError($available)) {
            $e = '<p class="error">An Error occured while fetching the credits from the remote server.'
               . ' Please try again.</p>';
            return $e;
        }
        if (!is_array($available)) {
            $e = '<p class="error">The credits could not be fetched from the remote server.'
               . ' Please try again.</p>';
            return $e;
        }
        $html_pear_credits .= '
<table border="0" cellpadding="3" width="600">
<tr class="h"><td>Package</td><td>Maintainers</td></tr>
';
        foreach ($available as $channel => $pkg) {
            foreach ($pkg as $name) {
                $info = &$this->reg->getPackage($name, $channel);
                if (is_object($info)) {
                    $installed['package'] = $info->getPackage();
                    $installed['maintainers'] = $info->getMaintainers();
                } else {
                    $installed = $info;
                }

                $ptpl = '
<tr>
    <td class="e">
        <a href="http://pear.php.net/{packageURI}">{package}</a>
    </td>
    <td class="v">
        {maintainers}
    </td>
</tr>';
                $maintainers = array();
                foreach ($installed['maintainers'] as $i) {
                    $maintainers[] = '<a href="http://pear.php.net/account-info.php?handle='
                                   . $i['handle']. '">'
                                   . htmlentities($i['name'])
                                   . '</a>'
                                   .' (' . $i['role']
                                   . (isset($i['active']) && $i['active'] === 'no' ? ', inactive' : '')
                                   . ')';
                }
                $maintainers = implode(', ',$maintainers);

                $html_pear_credits .= str_replace(
                    array('{packageURI}',
                        '{package}',
                        '{maintainers}'
                        ),
                    array(trim(strtolower($installed['package'])),
                        trim($installed['package']),
                        $maintainers
                        ),
                    $ptpl
                );
            }
        }
        $html_pear_credits .= '</table>';
        return $html_pear_credits;
    }

    /**
     * outputs the PEAR logo
     *
     * @return void
     * @access public
     * @since  1.0.1
     */
    function pearImage()
    {
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

    /**
     * Shows PEAR_Info output
     *
     * @return void
     * @access public
     * @since  1.0.1
     * @deprecated  use display() instead
     */
    function show()
    {
         $this->display();
    }

    /**
     * Displays PEAR_Info output
     *
     * @return void
     * @access public
     * @since  1.7.0b1
     */
    function display()
    {
         echo $this->toHtml();
    }

    /**
     * Returns PEAR_Info output (html code)
     *
     * @return string
     * @access public
     * @since  1.7.0b1
     */
    function toHtml()
    {
        if (!isset($this->css)) {
            // when no user-styles defined, used the default values
            $this->setStyleSheet();
        }
        $styles = $this->getStyleSheet();

        $body = $this->info;

        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>PEAR :: PEAR_Info()</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
<!--
$styles
 -->
</style>
</head>
<body>
$body
</body>
</html>
HTML;
        return $html;
    }

    /**
     * Check if a package is installed
     *
     * @param  string  $name                        Package name
     * @param  string  $version (optional)          The minimal version that should be installed
     * @param  string  $channel (optional)          The package channel distribution
     * @param  string  $pear_user_config (optional) File to read user-defined options from
     * @static
     * @return bool
     * @access public
     * @since  1.6.0
     */
    function packageInstalled($name, $version = null, $channel = null, $pear_user_config = null)
    {
        $config = new PEAR_Config($pear_user_config);
        $reg = new PEAR_Registry($config->get('php_dir'));

        if (is_null($version)) {
            return $reg->packageExists($name, $channel);
        } else {
            $info = &$reg->getPackage($name, $channel);
            if (is_object($info)) {
                $installed['version'] = $info->getVersion();
            } else {
                $installed = $info;
            }
            return version_compare($version, $installed['version'], '<=');
        }
    }
}

if (isset($_GET['pear_image'])) {
    PEAR_Info::pearImage();
    exit;
}
?>