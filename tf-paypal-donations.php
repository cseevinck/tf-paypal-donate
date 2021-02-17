<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
* Plugin Name: TF Paypal Donations
* Description: A plugin to accept Paypal donations using IPN. Shortcode: [tf-paypal-donations]
* Version: 1.0 - Feb 13, 2021 
*/

add_shortcode('tf-paypal-donations', 'tfdon_donations');

function tfdon_donations() {  
    $options = get_option( 'tfdon_settings' );
    
    if ( !isset($_POST['tfdon-submit'])) {
        ob_start();
        include('templates/donation-form.php');
        return ob_get_clean();
    };
}

function tfdon_register_styles() {
    wp_enqueue_style('tf-paypal-donations-styles', plugin_dir_url( __FILE__ ) . 'css/tfdonstyle.css' );
};

$options = get_option( 'tfdon_settings' );
$disable_css = $options['tfdon_disable_css'];
if(!$disable_css) {
	add_action('wp_enqueue_scripts','tfdon_register_styles');
};

/** 
 *  Define a paypal ipn listener
 *  from: https://webkul.com/blog/paypal-ipn-integration-wordpress/  
 * 
*/

add_action( 'init', 'paypal_ipn' );
include('inc/ipn-handler.php');
include('inc/notification-email.php');

/** 
 * Include code segments for Admin and Admin styles 
 * 
*/   
include('inc/tfdon-admin.php');
include('inc/tfdon-admin-styles.php');