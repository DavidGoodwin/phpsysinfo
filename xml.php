<?php
header('Access-Control-Allow-Origin: *');
/**
 * generate the xml
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   SVN: $Id: xml.php 614 2012-07-28 09:02:59Z jacky672 $
 * @link      http://phpsysinfo.sourceforge.net
 */

/**
 * application root path
 *
 * @var string
 */
define('PSI_APP_ROOT', dirname(__FILE__));

require_once PSI_APP_ROOT . '/includes/autoloader.inc.php';

// if there's crap in $_GET['plugin'] just abort.
if (!empty($_GET['plugin']) && preg_match('/[^A-Za-z]/', $_GET['plugin'])) {
    exit(0);
}

if ((isset($_GET['json']) || isset($_GET['jsonp'])) && !extension_loaded("json")) {
    echo '<Error Message="The json extension to php required!" Function="ERROR"/>';
} else {
    // check what xml part should be generated
    if (!empty($_GET['plugin'])) {
        $output = new WebpageXML($_GET['plugin']);
    } else {
        $output = new WebpageXML();
    }

    // if $output is correct generate output in proper type
    if (isset($_GET['json']) || isset($_GET['jsonp'])) {
        header("Cache-Control: no-cache, must-revalidate");
        $json = $output->getJsonString();
        if (isset($_GET['jsonp'])) {
            header("Content-Type: application/javascript");
            echo (!preg_match('/[^\w\?]/', $_GET['callback']) ? $_GET['callback'] : '') . '(' . $json . ')';
        } else {
            header("Content-Type: application/json");
            echo $json;
        }
    } else {
        $output->run();
    }
}
