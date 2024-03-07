<?php
/*

Class Ik_Venmo_PaymentGateway
Created:	12/15/2022
Update: 	12/15/2022
Author: 	Gabriel Caroprese

*/

add_action( 'plugins_loaded', 'ik_venmopg_gateway_init', 11 );

function ik_venmopg_gateway_init() {

	class Ik_Venmo_PaymentGateway extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'ik_venmopg';
			$this->icon               = apply_filters('ik_venmopg_gateway_filter_icon', IK_VENMOPG_PLUGIN_DIR_PUBLIC.'\img\venmo-icon.png' );
			$this->has_fields         = false;
			$this->method_title       = __( 'Venmo Payments', 'ik_venmopg' );
			$this->method_description = __( 'Payments through Venmo. Orders are marked as "on hold" upon receipt.','ik_venmopg' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', '' );
			$this->vendor = $this->get_option( 'vendor', '' );
			$this->email = $this->get_option( 'email', get_option('admin_email') );
			$this->qr = $this->get_option( 'qr', '' );
		  
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		  
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
	
	
		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {
	  
			$this->form_fields = apply_filters( 'ik_venmopg_gateway_filter_fields', array(
		  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'ik_venmopg' ), // enable/Disable
					'type'    => 'checkbox',
					'label'   => __( 'Enable payments with Venmo', 'ik_venmopg' ),//enable Venmo Payment
					'default' => 'yes'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'ik_venmopg' ),//Title
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer will see during checkout.', 'ik_venmopg' ),
					'default'     => __( 'Venmo Payments', 'ik_venmopg' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'ik_venmopg' ), //Description
					'type'        => 'textarea',
					'description' => __( 'Pay. Get paid. Shop. Share.', 'ik_venmopg' ),
					'default'     => __( 'Pay. Get paid. Shop. Share.', 'ik_venmopg' ),
					'desc_tip'    => true,
				),
				
				'instructions' => array(
					'title'       => __( 'Instructions', 'ik_venmopg' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'ik_venmopg' ),
					'default'     => __( 'Please, after sending the payment contact us at', 'ik_venmopg' ).' '.get_option('admin_email').' '.__( 'in order to verify the payment. Thank you!', 'ik_venmopg' ),
					'desc_tip'    => true,
				),
				'vendor' => array(
					'title'       => __( 'Vendor ID', 'ik_venmopg' ),
					'type'        => 'text',
					'description' => __( '', 'ik_venmopg' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'qr' => array(
					'title'       => __( 'QR Code Media ID', 'ik_venmopg' ),
					'type'        => 'number',
					'description' => __( 'Get media item ID of image from Wordpress Media Section. You can open the image from there and take it from the URL', 'ik_venmopg' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'email' => array(
					'title'       => __( 'Email to notify payments', 'ik_venmopg' ),
					'type'        => 'email',
					'description' => __( 'Set email to notify payments', 'ik_venmopg' ),
					'default'     => get_option('admin_email'),
					'desc_tip'    => true,
				),			) );
		}
	
	
		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			
			if (isset($_GET['key'])){
			    $key = sanitize_key($_GET['key']);
			    $order_id = absint(wc_get_order_id_by_order_key($key));
			    $order = new WC_Order($order_id);
			    if ($order){
			        $total = $order->get_total();
			    }
			}
            echo '<style>
            #ik_venmo_payments_details{
                margin: 50px 0;
            }
			#ik_venmo_payments_details img{
				max-width: 400px;
			}
            </style>
            <div id="ik_venmo_payments_details">
            <h2>'.__( 'Pay With Venmo', 'ik_venmopg' ).'</h2>';
            if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}
			if ( $this->vendor) {
				$image_qr = wp_get_attachment_image_src( absint($this->qr) );
				if($image_qr){
					$image_qr_src = '<p><img src="'.$image_qr[0].'" alt="qr code payment venmo"></p>';
				} else {
					$image_qr_src = '';
				}	
				
			    echo '<p class="details"><span>'.__( 'Venmo ID:', 'ik_venmopg' ).'</span> '.$this->vendor.'</p>
				<p class="details"><span>'.__( 'Pay:', 'ik_venmopg' ).'</span> '.wc_price($total).'</p>
				<p class="details"><span>'.__( 'Order ID:', 'ik_venmopg' ).'</span> '.$order_id.'</p>'.$image_qr_src;
      
			} else {
			    echo __( 'Contact the seller at ', 'ik_venmopg' ).' '.$this->email.' '.__( 'in order to know how to make the payment.', 'ik_venmopg' );
			}
			echo '</div>';
		}
	
	
		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}
	
	
		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
	
			$order = wc_get_order( $order_id );
			
			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'on-hold', __( 'Waiting for payment confirmation.', 'ik_venmopg' ) );
			
			// Reduce stock levels
			$order->reduce_order_stock();
			
			// Remove cart
			WC()->cart->empty_cart();
			
			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
	
  }
}

?>