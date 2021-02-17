<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
 *  Get control and do processing if action = IPN_Handler  
 * 
*/
function paypal_ipn() {
  global $wp;
  if (isset($_GET['action']) && $_GET['action']=='IPN_Handler') { 
    // wp_mail( "corky@seevinck.com", "paypal_ipn ", " IPN_Handler" );         
    if(check_ipn()) {
        // wp_mail( "corky@seevinck.com", "paypal_ipn - true ", "true" ); 
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

  wp_mail( "corky@seevinck.com", "check_ipn POST= ", print_r($ipn_response, TRUE));  
  if ($ipn_response == false) { 
      // wp_mail( "corky@seevinck.com", "check_ipn false= ", ""); 
      return false;
  } 
  if ($ipn_response && check_ipn_valid($ipn_response)) {
      header('HTTP/1.1 200 OK');        
      // wp_mail( "corky@seevinck.com", "check_ipn sent header ", " ");    
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

  wp_mail( "corky@seevinck.com", "ipn_request ", print_r ( $response, TRUE) ); 

  $posted = stripslashes_deep($ipn_response);
  return;
}

/** 
 * post back the data recieved with ‘cmd’=_notify_validate appended in the 
 * response to validate the response. 
 * 
*/
function check_ipn_valid($ipn_response) {
 // wp_mail( "corky@seevinck.com", "check_ipn_valid ", " wanna send back a response" ); 

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

  //wp_mail( "corky@seevinck.com", "check_ipn_valid - params we wanna send ", print_r($params, TRUE) ); 

  // Post back to get a response
  $response = wp_safe_remote_post($paypal_adr, $params);
  //wp_mail( "corky@seevinck.com", "wp_safe_remote_post response: ", print_r($response, TRUE) ); 

  //  wp_mail( "corky@seevinck.com", "check_ipn_valid - response ", $response ); 
  // check to see if the request was valid
  if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
      // wp_mail( "corky@seevinck.com", "wp_safe_remote_post status - positive: ", print_r($response, TRUE) );
      send_notification_email($ipn_response, $verified = true); // Send the notification  
      return true;
  }
  wp_mail( "corky@seevinck.com", "wp_safe_remote_post status negative: ", print_r($response, TRUE) );
  // Send notification anyway, but warn the user
  send_notification_email($ipn_response, $verified = false); // Send the notification   
  return false;
}
?>