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
require_once('inc/tfdon-log.php');

add_shortcode('tf-paypal-donations', 'tfdon_shortcode_control');
/**
 * tfdon_shortcode_control
 * 
 * We will get control here at page init and, 
 * when the buttons on the log form page is clicked
 * 
 */

function tfdon_shortcode_control() {  
    tfdon_log("ttfdon_shortcode_control ", $_POST); // log file entry
    $options = get_option( 'tfdon_settings' );

    // display donations form if this is not log file display 
    if (( !isset ($_POST['tfdon_log'])) && ( !isset ($_POST['tfdon_log_delete']))){
        tfdon_log("tfdon_shortcode_control for donation form display ", ""); 
        ob_start();
        require_once('templates/donation-form.php');
        return ob_get_clean();
    }
    // redirect to log file display page 
    else if ((isset ($_POST['tfdon_log'])) && ($_POST['tfdon_log'] == 'Submit')) {
        tfdon_log("tfdon_shortcode_control - Redirect to: ", $options['tfdon_log_display']); 
        wp_redirect( $options['tfdon_log_display'] . '?file=' . $_POST["what_file"] );
        return; 
    }
    // See if this is a delete files request 
    else if ((isset ($_POST['tfdon_log_delete'])) && ($_POST['tfdon_log_delete'] == 'Submit')) {
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];

        if(file_exists($upload_dir . '/' . TFDON_CURRENT_LOG)){
            unlink($upload_dir . '/' . TFDON_CURRENT_LOG);
        }
        if(file_exists($upload_dir . '/' . TFDON_OLDER_LOG)){
            unlink($upload_dir . '/' . TFDON_OLDER_LOG);
        }
        wp_redirect($_SERVER['HTTP_REFERER'] . '?file=deleted' );
        exit(); 
    }
    else {
        tfdon_log("tfdon_shortcode_control: Unknown event= ", $_POST); 
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

require_once('inc/ipn-handler.php');
require_once('inc/notification-email.php');

/** 
 * Include code segments for admin and admin styles and log file display
 * 
*/   
require_once('inc/tfdon-admin.php');
require_once('inc/tfdon-admin-styles.php');
require_once('inc/tfdon-display-logs.php');