<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
ob_clean(); 
ob_start(); // this makes wp_redirect work
/** 
* Plugin Name: TF Paypal Donations
* Description: A plugin to accept Paypal donations using IPN. Shortcode: [tf-paypal-donations]
* Version: 1.3 - March 1, 2021 
*/

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
    tfdon_log("entry @ tfdon_donations ", ""); // log file entry
    $options = get_option( 'tfdon_settings' );

    // display donations form if this is not log file display 
    if ( !isset($_POST['tfdon_log'] )) {
        tfdon_log("entry @ tfdon_donations for donation form display ", ""); 
        ob_start();
        include('templates/donation-form.php');
        return ob_get_clean();
    }
    // redirect to log file display page 
    else {
        if ((isset ($_POST['tfdon_log'])) && ($_POST['tfdon_log'] == 'Submit')) {
            tfdon_log("log entry: ", $_POST); 
            tfdon_log("Redirect to: ", $options['tfdon_log_display']); 
            wp_redirect( $options['tfdon_log_display'] . '?file=' . $_POST["what_file"] );
            exit();
        }
        return;
    }
}

/**
 * Assign the correct template to the log display page. 
 */
function tfdon_change_page_template($template) {
    $options = get_option( 'tfdon_settings' );
    if (get_permalink(get_the_ID()) == $options['tfdon_log_display']){    
        $template = plugin_dir_path(__FILE__) . 'templates/log-display.php';
    }
    return $template;
}
add_filter('template_include', 'tfdon_change_page_template', 99);

/**
 * Enqueue plugin styles
 */
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
 * Include code segments for admin and admin styles and log file display
 * 
*/   
include('inc/tfdon-admin.php');
include('inc/tfdon-admin-styles.php');
include('inc/tfdon-display-logs.php');