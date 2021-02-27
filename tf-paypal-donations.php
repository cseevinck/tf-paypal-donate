<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
* Plugin Name: TF Paypal Donations
* Description: A plugin to accept Paypal donations using IPN. Shortcode: [tf-paypal-donations]
* Version: 1.2 - Feb 26, 2021 
*/

const TFDON_CURRENT_LOG = "tf_paypal_donate.log";
const TFDON_OLDER_LOG = "tf_paypal_donate_old.log";
const TFDON_IPN_ID = "IPN_Handler";

// define log file function
include('inc/tfdon-log.php');

add_shortcode('tf-paypal-donations', 'tfdon_donations');
/**
 * tfdon_donations
 * 
 * We will get control here at page init and, 
 * when the button on the log form page is clicked
 * 
 */

function tfdon_donations() {  
    if ( !isset($_POST['tfdon_log'] )) {
        tfdon_log("entry @ tfdon_donations for donation form display ", ""); 
        $options = get_option( 'tfdon_settings' );
        ob_start();
        include('templates/donation-form.php');
        return ob_get_clean();
    }
    else {
        tfdon_log("log entry: ", $_POST); 
        tfdon_display_log_file ($_POST["what_file"]);
        return;
    }
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

add_action( 'init', 'tfdon_paypal_ipn' );
include('inc/ipn-handler.php');
include('inc/notification-email.php');

/** 
 * Include code segments for Admin and Admin styles 
 * 
*/   
include('inc/tfdon-admin.php');
include('inc/tfdon-admin-styles.php');
include('inc/tfdon-display-logs.php');