<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
 *  Get control and do processing if action = IPN_Handler  
 * 
*/
function paypal_ipn() {
  global $wp;
  if (isset($_GET['action']) && $_GET['action']=='IPN_Handler') { 
    tfdon_log("IPN_Handler POST sent by PayPal", $_POST); 
    if(check_ipn()) {
        ipn_request($IPN_status = true);
    } else {
        ipn_request($IPN_status = false);
    }
  } 
}

/** 
 * Check for the ipn response whether it is valid or not using check_ipn_valid 
 * function. If valid, send back OK message to PayPal and send notification email 
 *  
*/
function check_ipn() {
  $ipn_response = !empty($_POST) ? $_POST : false;
  if ($ipn_response) { 
    tfdon_log("IPN sent by PayPal", $ipn_response); 
  };

  if ($ipn_response == false) { 
    tfdon_log("PayPal IPN Invalid", $ipn_response); 
    return false;
  } 
  if ($ipn_response && check_ipn_valid($ipn_response)) {
    header('HTTP/1.1 200 OK');        
    return true;
  }
}

/** 
 * Now, if everything goes fine then we get ipn status true for ipn_request  
 * function otherwise false. So. lets’s come to the ipn_request function  
*/
function ipn_request($IPN_status) {		
  $ipn_response = !empty($_POST) ? $_POST : false;
  $ipn_response['IPN_status'] = ( $IPN_status == true ) ? 'Verified' : 'Invalid';

  $posted = stripslashes_deep($ipn_response);
  return;
}

/** 
 * post back the data recieved with ‘cmd’=_notify_validate appended in the 
 * response to validate the response. 
 * 
*/
function check_ipn_valid($ipn_response) {

  $options = get_option( 'tfdon_settings' );

  if (isset($options['tfdon_paypal_testing'])) {
    $paypal_adr = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'; // sandbox mode
  } else {
    $paypal_adr = 'https://ipnpb.paypal.com/cgi-bin/webscr'; // for live
  }
  // Get received values from post data   
  $validate_ipn = array('cmd' => '_notify-validate');
  $validate_ipn += stripslashes_deep($ipn_response);
  // Send back post vars to paypal 
  $params = array(
      'body' => $validate_ipn,
      'sslverify' => false,
      'timeout' => 60,
      'httpversion' => '1.1',
      'compress' => false,
      'decompress' => false,
      'user-agent' => 'tf-paypal-donations-ipn/1.0'
  );
  // Post back to get a response
  $response = wp_safe_remote_post($paypal_adr, $params);

  // check to see if the request was valid
  if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
 
      tfdon_log("Verification from PayPal - VERIFIED response code", $response['response']['code']);
      tfdon_log("Verification from PayPal - VERIFIED", var_export($response, true), "string");
      send_notification_email($ipn_response, $verified = true); // Send the notification  
      return true;
  }
  // Send notification anyway, but warn the user
  
  // Try to put error message out
  $error_string = $response->get_error_message();
  tfdon_log("Verification from PayPal - NOT VERIFIED - Error String", $error_string);

  // Put entries into log file
  tfdon_log("Verification from PayPal - NOT VERIFIED response code", $response['response']['code']);
  tfdon_log("Verification from PayPal - NOT VERIFIED", var_export($response, true)); 
  send_notification_email($ipn_response, $verified = false); // Send the notification  
  return false;
}
?>