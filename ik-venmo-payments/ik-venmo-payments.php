<?php
/*
Plugin Name: IK Venmo Payment Gateway
Plugin URI: https://dvsdesignx.es/
Description: Melio Payment Gateway
Version: 1.1.1
Author: Gabriel Caroprese
Author URI: https://dvsdesignx.es/
Requires at least: 5.3
Requires PHP: 7.2
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$ik_venmopg_dir = dirname( __FILE__ );
$ik_venmopg_public_dir = plugin_dir_url(__FILE__ );
define( 'IK_VENMOPG_PLUGIN_DIR', $ik_venmopg_dir);
define( 'IK_VENMOPG_PLUGIN_DIR_PUBLIC', $ik_venmopg_public_dir);

require_once($ik_venmopg_dir . '/include/class.php');
require_once($ik_venmopg_dir . '/include/hooks.php');

?>