<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
 *  Get control and do processing if action = IPN Handler  
 * 
*/
function tfdon_paypal_ipn() {
  if (isset($_GET['action']) && $_GET['action']==TFDON_IPN_ID) { 
    tfdon_log("IPN Handler POST sent by PayPal", $_POST); 

    if(tfdon_check_ipn()) {
        tfdon_ipn_request($IPN_status = true);
    } else {
        tfdon_ipn_request($IPN_status = false);
    }
  } 
}

/** 
 * Check for the ipn response whether it is valid or not using
 *    tfdon_check_ipn_valid function. 
 * If valid, send back OK message to PayPal and send notification email 
 *  
*/
function tfdon_check_ipn() {
  $ipn_response = !empty($_POST) ? $_POST : false;
  if ($ipn_response) { 
    tfdon_log("IPN sent by PayPal", $ipn_response); 
  };

  if ($ipn_response == false) { 
    tfdon_log("PayPal IPN Invalid", $ipn_response); 
    return false;
  } 
  if ($ipn_response && tfdon_check_ipn_valid($ipn_response)) {
    header('HTTP/1.1 200 OK');        
    return true;
  }
}

/** 
 * Now, if everything goes fine then we get ipn status true for tfdon_ipn_request  
 * function otherwise false. So. lets’s come to the tfdon_ipn_request function  
*/
function tfdon_ipn_request($IPN_status) {		
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
function tfdon_check_ipn_valid($ipn_response) {
  $options = get_option( 'tfdon_settings' );
// Sandbox or not 
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
  // Post back to PayPal & get response
  $response = wp_safe_remote_post($paypal_adr, $params);

  // check to see if the request was valid
  if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
      tfdon_send_notification_email($ipn_response, $verified = true); // Send the notification  
      tfdon_log("Verification from PayPal - VERIFIED response code ", $response['response']['code']);
      return true;
  }

  // Not verified: Send notification anyway, but warn the user 
  tfdon_send_notification_email($ipn_response, $verified = false);

  // Put entry into log file
  tfdon_log("Verification from PayPal - NOT VERIFIED response code", $response['response']['code']);

  return false;
}
?>