<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Ik Venmo PG Hooks
Class Ik_Venmo_PaymentGateway
Created:	12/15/2022
Update: 	12/15/2022
Author: 	Gabriel Caroprese
*/

// if plugin WooCommerce is not installed a message will show up and the plugin will deactivate itself
add_action( 'admin_notices', 'ik_venmopg_plugin_dependencies' );
function ik_venmopg_plugin_dependencies() {
    if (!class_exists('WC_Order')) {
    echo '<div class="error"><p>' . __( 'Warning! The plugin  <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>, is needed in order to make IK Venmo Payment Gateway work.' ) . '</p></div>';
        $pluginURL = 'ik_venmo_payments/ik_venmo_payments.php';
        deactivate_plugins($pluginURL);
    }
}

// Add the gateway to WC Available Gateways
function ik_venmopg_add_to_gateways( $gateways ) {
	$gateways[] = 'Ik_Venmo_PaymentGateway';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'ik_venmopg_add_to_gateways' );


//Add edit links for payment gateway
function ik_venmopg_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ik_venmopg' ) . '">' . __( 'Set Up', 'ik_venmopg' ) . '</a>'
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . IK_VENMOPG_PLUGIN_DIR, 'ik_venmopg_plugin_links' );
?>