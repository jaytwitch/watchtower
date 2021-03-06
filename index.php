<?php
session_start();

define('ROOT',dirname(__FILE__));

// error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// benchmarking - page loading
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

// $settings
require(ROOT.'/config.php');
(@include_once(ROOT.'/config.php')) or
die("config.php required, please copy config.default.php to config.php and edit as required");

// load ZabbixApi
require_once 'lib/ZabbixApi.class.php';
use ZabbixApi\ZabbixApi;

// connect to Zabbix API
$api = new ZabbixApi($settings['zabbix']['url'], $settings['zabbix']['user'], $settings['zabbix']['pass']);

include_once('lib/conversion.php');

// $data for page
$data = array();

// logging out
if(isset($_GET['action'])) {
    if($_GET['action'] == 'logout') {
    	$_SESSION = [];
    	if(isset($_COOKIE[session_name()])) {
    		setcookie(session_name(), '', time()-42000, '/');
    	}
    	session_destroy();

    	// redirect to home page
    	header('Location: '.$settings['global']['basedir']);        
    }
}

// logging in
if(isset($_POST['password'])) {
    if($_POST['password'] == $settings['password']) {
        $_SESSION['auth'] = true;
    }
}

// output
include(ROOT.'/views/header.php');

if((!isset($_SESSION['auth'])) || ($_SESSION['auth'] != true)) {
    include(ROOT.'/views/login.php');
} else {
    include(ROOT.'/data/home.php');
    include(ROOT.'/views/hosts.php');

    ?>
    <script type="text/javascript">
      function updateHosts(){
        $('#hosts').load('data/hosts.php');
      }
      setInterval( "updateHosts()", 10000 );
    </script>
    <?php
}

include(ROOT.'/views/footer.php');

// clearing $data
$data = array();
