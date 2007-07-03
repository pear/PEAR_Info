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
 * PEAR_INFO_* is a bit-field. Or each number up to get desired information.
 *
 * Examples:
 * <code>
 * <?php
 * require_once 'PEAR/Info.php';
 * // will display for each channel (list displayed),
 * // a quick package list with only its name and version
 * $options = array('resume' => PEAR_INFO_CHANNELS | PEAR_INFO_PACKAGES_VERSION);
 * $info = new PEAR_Info('', 'c:\wamp\php\pear.ini', '', $options);
 * $info->display();
 * ?>
 * </code>
 *
 * - Show all informations, except for credits
 *
 *   $options = array('resume' => PEAR_INFO_ALL & ~PEAR_INFO_CREDITS);
 *
 * - Show only credits and configuration
 *
 *   $options = array('resume' => PEAR_INFO_CONFIGURATION | PEAR_INFO_CREDITS);
 */
/**
 * The configuration line, pear.ini | .pearrc location, and more.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define ('PEAR_INFO_GENERAL',                  1);
/**
 * PEAR Credits
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define ('PEAR_INFO_CREDITS',                  2);
/**
 * All PEAR settings.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define ('PEAR_INFO_CONFIGURATION',            4);
/**
 * Information on PEAR channels.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define ('PEAR_INFO_CHANNELS',                 8);
/**#@+
 * Information on PEAR channels.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define ('PEAR_INFO_PACKAGES',              4080);
define ('PEAR_INFO_PACKAGES_CHANNEL',      2048);
define ('PEAR_INFO_PACKAGES_SUMMARY',      1024);
define ('PEAR_INFO_PACKAGES_VERSION',       512);
define ('PEAR_INFO_PACKAGES_LICENSE',       256);
define ('PEAR_INFO_PACKAGES_DESCRIPTION',   128);
define ('PEAR_INFO_PACKAGES_DEPENDENCIES',   64);
define ('PEAR_INFO_PACKAGES_XML',            32);
define ('PEAR_INFO_PACKAGES_UPDATE',         16);
/**#@-*/
/**
 * Shows all of the above. This is the default value.
 *
 * @var        integer
 * @since      1.7.0RC1
 */
define ('PEAR_INFO_ALL',                    -1);

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
     * @since  1.7.0RC1
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
     * @param  array   $options          (optional) configure PEAR information output
     * @return void
     * @access public
     * @since  1.0.1
     */
    function PEAR_Info($pear_dir = '', $user_file = '', $system_file = '', $options = null)
    {
        $this->__construct($pear_dir, $user_file, $system_file, $options);
    }

    /**
     * PHP 5 style constructor (ZE2)
     *
     * @param  string  $pear_dir         (optional) The PEAR base install directory
     * @param  string  $user_file        (optional) file to read PEAR user-defined options from
     * @param  string  $system_file      (optional) file to read PEAR system-wide defaults from
     * @param  array   $options          (optional) configure PEAR information output
     * @return void
     * @access private
     * @since  1.7.0RC1
     */
    function __construct($pear_dir = '', $user_file = '', $system_file = '', $options = null)
    {
        // options defined at run-time (default)
        $this->options = array('channels' => array('pear.php.net'), 'resume' => PEAR_INFO_ALL);
        if (isset($options)) {
            // overwrite one to all defaults
            $this->options = array_merge($this->options, $options);
        }

        $this->config =& PEAR_Config::singleton($user_file, $system_file);

        // to keep compatibility with version less or equal than 1.6.1
        if (!empty($pear_dir) && empty($user_file) && empty($system_file)) {
            $this->config->set('php_dir', $pear_dir);
        }
        // to keep compatibility with version less or equal than 1.6.1
        if (defined('PEAR_INFO_PROXY')) {
            $this->config->set('http_proxy', PEAR_INFO_PROXY);
        }

        if (empty($user_file)) {
            if (empty($system_file)) {
                $user_file = $this->config->getConfFile('user');
                if (file_exists($user_file)) {
                    $layer = 'user';
                } else {
                    $system_file = $this->config->getConfFile('system');
                    $layer = 'system';
                }
            } else {
                $layer = 'system';
            }
        } else {
            $layer = 'user';
        }
        // prevent unexpected result if PEAR config file does not exist
        if (!file_exists($user_file) && !file_exists($system_file)) {
            trigger_error("PEAR configuration files '$user_file', '$system_file' does not exist", E_USER_ERROR);
            exit();
        }
        // Get the config's registry object.
        $this->reg = &$this->config->getRegistry($layer);

        // Get list of all channels in your PEAR install, when 'channels' option is empty
        if (isset($this->options['channels']) && empty($this->options['channels'])) {
            $channels = $this->reg->listChannels();
            if (PEAR::isError($channels)) {
                $this->options['channels'] = array('pear.php.net');
            } else {
                $this->options['channels'] = $channels;
            }
        }

        // show general informations such as PEAR version, PEAR logo, and config file used
        if ($this->options['resume'] & PEAR_INFO_GENERAL) {
            if (method_exists($this->reg, 'getPackage')) {
                $pear = $this->reg->getPackage("PEAR");
                $pear_version = $pear->getVersion();
            } else {
                $pear = $this->reg->packageInfo('PEAR');
                $pear_version = $pear['version'];
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

            // Loaded configuration file
            $this->info .= '
<table>
<tr class="v">
    <td class="e">Loaded Configuration File</td>
    <td>{value}</td>
</tr>
</table>

';
            $this->info = str_replace('{value}',
                $this->config->getConfFile($layer),
                $this->info
            );
        }

        if (!isset($_GET['credits'])) {
            if ($this->options['resume'] & PEAR_INFO_CREDITS) {
                $this->info .= '
<h1><a href="{phpself}?credits=true">PEAR Credits</a></h1>
';
                $this->info = str_replace(
                    '{phpself}', htmlentities($_SERVER['PHP_SELF']),
                    $this->info
                );
            }
            if ($this->options['resume'] & PEAR_INFO_CONFIGURATION) {
                $this->info .= $this->getConfig();
            }
            if ($this->options['resume'] & PEAR_INFO_CHANNELS) {
                $this->info .= $this->getChannels();
            }
            if ($this->options['resume'] & PEAR_INFO_PACKAGES) {
                $this->info .= $this->getPackages();
            }

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
     * @since  1.7.0RC1
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
     * @since  1.7.0RC1
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

        // list of channels to scan
        $channel_allowed = $this->options['channels'];

        // check if there are new versions available for packages installed
        if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {

            $latest = array();
            foreach ($channel_allowed as $channel) {
                // Get a channel object.
                $chan =& $this->reg->getChannel($channel);
                if (PEAR::isError($chan)) {
                    $e = '<p class="error">An error has occured. ' . $chan->getMessage()
                       . ' Please try again.</p>';
                    return $e;
                }

                if ($chan->supportsREST($channel) &&
                    $base = $chan->getBaseURL('REST1.0', $channel)) {

                    $rest =& $this->config->getREST('1.0', array());
                    if (is_object($rest)) {
                        $pref_state = $this->config->get('preferred_state');
                        $installed = array_flip($available[$channel]);

                        $l = $rest->listLatestUpgrades($base, $pref_state, $installed,
                                      $channel, $this->reg);
                    } else {
                        $l = false;
                    }
                } else {
                    $r =& $this->config->getRemote();
                    $l = @$r->call('package.listLatestReleases');
                }
                if (is_array($l)) {
                    $latest = array_merge($latest, $l);
                }
            }
        } else {
            $latest = false;
        }

        if ((PEAR::isError($latest)) || (!is_array($latest))) {
            $latest = false;
        }

        $s = '';
        $anchor_suffix = 0;  // make page XHTML compliant
        foreach ($available as $channel => $pkg) {
            if (!in_array($channel, $channel_allowed)) {
                continue;
            }
            // sort package by alphabetic order
            sort($pkg);
            //
            $packages = '';
            $index = array();
            foreach ($pkg as $name) {
                // show general package informations
                $info = &$this->reg->getPackage($name, $channel);
                if (is_object($info)) {
                    $__info = $info->getArray();
                    $installed['package'] = $info->getPackage();
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_CHANNEL) {
                        $installed['channel'] = $channel;
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_SUMMARY) {
                        $installed['summary'] = $info->getSummary();
                    }
                    $installed['version'] = $info->getVersion();
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_VERSION) {
                        $installed['current_release'] = $installed['version']
                            . ' (' . $info->getState() . ') was released on '
                            . $info->getDate();
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_LICENSE) {
                        $installed['license'] = $info->getLicense();
                    }
                    if ($info->getPackagexmlVersion() == '1.0' ) {
                        if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {
                            $installed['lastmodified'] = $info->packageInfo('_lastmodified');
                        }
                        if ($this->options['resume'] & PEAR_INFO_PACKAGES_XML) {
                            $installed['packagexml'] = $info->getPackagexmlVersion();
                            if (isset($__info['packagerversion'])) {
                                $installed['packagerversion'] = $__info['packagerversion'];
                            }
                        }
                    } else {
                        if ($this->options['resume'] & PEAR_INFO_PACKAGES_LICENSE) {
                            $uri = $info->getLicenseLocation();
                            if ($uri) {
                                if (isset($uri['uri'])) {
                                    $installed['license'] = '<a href="' . $uri['uri'] . '">'
                                        . $info->getLicense() . '</a>';
                                }
                            }
                        }
                        if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {
                            $installed['lastmodified'] = $info->getLastModified();
                        }
                        if ($this->options['resume'] & PEAR_INFO_PACKAGES_XML) {
                            $installed['packagexml'] = $info->getPackagexmlVersion();
                            $installed['packagerversion'] = $__info['attribs']['packagerversion'];
                        }
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_DESCRIPTION) {
                        $installed['description'] = $info->getDescription();
                    }
                } else {
                    $installed = $info;
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_CHANNEL) {
                        $installed['channel'] = 'pear.php.net';
                    }
                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_VERSION) {
                        $installed['current_release'] = $info['version']
                            . ' (' . $info['release_state'] . ') was released on '
                            . $info['release_date'];
                    }
                }

                // show dependency list
                $dependencies = '';
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_DEPENDENCIES) {
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
                                     'php' => 'PHP',
                                     'prog'=> 'Prog',
                                     'os'  => 'OS',
                                     'sapi'=> 'SAPI',
                                     'zend'=> 'Zend'
                                     );

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
                } // end deps-list

                if (!isset($old_index)) {
                    $old_index = '';
                }
                $current_index = $name{0};
                if (strtolower($current_index) != strtolower($old_index)) {
                    $packages .= '<a name="' . $current_index . $anchor_suffix . '"></a>';
                    $old_index = $current_index;
                    $index[] = $current_index;
                }

                // prepare package informations template
                $ptpl = '
<h2><a name="pkg_{package}">{package}</a></h2>
<table>
';
                $packages .= str_replace('{package}',
                    trim($installed['package']),
                    $ptpl
                );

                if ($this->options['resume'] & PEAR_INFO_PACKAGES_CHANNEL) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Channel
    </td>
    <td>
        {channel}
    </td>
</tr>
';
                    $packages .= str_replace('{channel}',
                        trim($installed['channel']),
                        $ptpl
                    );
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_SUMMARY) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Summary
    </td>
    <td>
        {summary}
    </td>
</tr>
';
                    $packages .= str_replace('{summary}',
                        nl2br(htmlentities(trim($installed['summary']))),
                        $ptpl
                    );
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_VERSION) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Version
    </td>
    <td>
        {version}
    </td>
</tr>
';
                    $packages .= str_replace('{version}',
                        trim($installed['current_release']),
                        $ptpl
                    );
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_LICENSE) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        License
    </td>
    <td>
        {license}
    </td>
</tr>
';
                    $packages .= str_replace('{license}',
                        trim($installed['license']),
                        $ptpl
                    );
                }
                if ($this->options['resume'] & PEAR_INFO_PACKAGES_DESCRIPTION) {
                    $ptpl = '
<tr class="v">
    <td class="e">
        Description
    </td>
    <td>
        {description}
    </td>
</tr>
';
                    $packages .= str_replace('{description}',
                        nl2br(htmlentities(trim($installed['description']))),
                        $ptpl
                    );
                }
                if (!empty($dependencies)) {
                    $ptpl = '
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
                    $packages .= str_replace('{dependencies}',
                        $dependencies,
                        $ptpl
                    );
                }

                if ($this->options['resume'] & PEAR_INFO_PACKAGES_UPDATE) {
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
        <a href="http://{channel}/get/{package}">{latest_version}</a>({latest_state})
    </td>
</tr>';
                                $packages .= str_replace(
                                    array('{package}',
                                        '{latest_version}',
                                        '{latest_state}',
                                        '{channel}'
                                        ),
                                    array(trim($installed['package']),
                                        $latest[$installed['package']]['version'],
                                        $latest[$installed['package']]['state'],
                                        $channel
                                        ),
                                    $ptpl
                                );
                            }
                        }
                    }

                    if ($this->options['resume'] & PEAR_INFO_PACKAGES_XML) {
                        $ptpl = '
<tr class="v">
    <td class="e">
        Package XML version
    </td>
    <td>
        {packagexml}
    </td>
</tr>';
                        $packagexml = $installed['packagexml'];
                        if (isset($installed['packagerversion'])) {
                            $packagexml .= ' packaged with PEAR version '
                                . $installed['packagerversion'];
                        }
                        $packages .= str_replace('{packagexml}',
                            $packagexml,
                            $ptpl
                        );
                    }
                    $ptpl = '
<tr class="v">
    <td class="e">
        Last Modified
    </td>
    <td>
        {lastmodified}
    </td>
</tr>';
                    $packages .= str_replace('{lastmodified}',
                        date('Y-m-d', $installed['lastmodified']),
                        $ptpl
                    );

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
                array($channel, 'top'.$anchor_suffix), $index_header
            );
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
<h2>PEAR Configuration</h2>
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
     * @since  1.7.0RC1
     */
    function getChannels()
    {
        $channels = $this->reg->listChannels();
        if (PEAR::isError($channels)) {
            $e = '<p class="error">An Error occured while fetching the channel list.'
               . ' Please try again.</p>';
            return $e;
        }
        $channel_allowed = $this->options['channels'];

        $html_pear_channel = '
<h2>PEAR Channels</h2>';

        $anchor_suffix = 0;
        foreach ($channels as $channel) {
            if (!in_array($channel, $channel_allowed)) {
                continue;
            }
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
';
        $channel_allowed = $this->options['channels'];

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

        foreach ($available as $channel => $pkg) {
            if (!in_array($channel, $channel_allowed)) {
                continue;
            }
            $html_pear_credits .= '
<br />
<table border="0" cellpadding="3" width="600">
<tr class="hc"><td colspan="2">Channel {channel}</td></tr>
<tr class="h"><td>Package</td><td>Maintainers</td></tr>';

            $html_pear_credits = str_replace('{channel}', $channel, $html_pear_credits);

            // sort package by alphabetic order
            sort($pkg);
            //
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
        <a href="http://{channel}/{packageURI}">{package}</a>
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
                        '{channel}',
                        '{maintainers}'
                        ),
                    array(trim(strtolower($installed['package'])),
                        trim($installed['package']),
                        $channel,
                        $maintainers
                        ),
                    $ptpl
                );
            }
            $html_pear_credits .= '
</table>
';
        }
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
     * @since  1.7.0RC1
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
     * @since  1.7.0RC1
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
<div>
$body
</div>
</body>
</html>
HTML;
        return $html;
    }

    /**
     * Check if a package is installed
     *
     * @param  string  $name                        Package name
     * @param  string  $version          (optional) The minimal version that should be installed
     * @param  string  $channel          (optional) The package channel distribution
     * @param  string  $user_file        (optional) file to read PEAR user-defined options from
     * @param  string  $system_file      (optional) file to read PEAR system-wide defaults from
     * @static
     * @return bool
     * @access public
     * @since  1.6.0
     */
    function packageInstalled($name, $version = null, $channel = null, $user_file = '', $system_file = '')
    {
        $config =& PEAR_Config::singleton($user_file, $system_file);
        $reg = &$config->getRegistry();

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
    exit();
}
?>
