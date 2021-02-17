<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
 *  Get control and do processing if action = IPN_Handler 
 * 
 *  $verified=true means the IPN was verified by PayPal 
 *  $verified=false means the IPN was not verified by PayPal 
*/
function send_notification_email($ipn, $verified) {
    $options = get_option( 'tfdon_settings' );
    $output = [];

    // wp_mail( "corky@seevinck.com", "send_notification_email", " start" );  
    // Send notification email with Name, Email Address and Giving Instructions 
    // item_number is a pass-thru value not used by donations - we will use it for the donor name

    $output[] = ($verified ? "Verified by PayPal" : "*** Not verified by PayPal! Double check this transaction ***");

    // Insert message body depending on the transaction type  
    $transaction = $ipn["txn_type"];
    switch ($transaction) {
    case "web_accept":
        web_accept_data($output, $ipn);
        break;
    case "recurring_payment":
        recurring_payment_data($output, $ipn);
        break;
    case "recurring_payment_profile_created":
        recurring_payment_profile_created_data($output, $ipn);
        break;
    default:
        unknown_ipn_data($output, $ipn);
    }
    
    $message = join("\r\n",$output);

    $tfdon_paypal_email = $options['tfdon_paypal_email'];
    // $tfdon_org = $options['tfdon_organization_name'];
    
    $recipient = $options['tfdon_notification_to_email'];

    $subject = "Disbursement details for donation from: " . $ipn["first_name"] . " " . $ipn["last_name"];
    $from = $options['tfdon_notification_from_email'];
    $replyto = $options['tfdon_notification_reply_to_email'];
    
    // Don't know how to build the headers variable -- use default 
    //$headers = 'From: ' . $tfdon_paypal_email . ' <' . $from . '>' . "\r\n";
    //$headers .= 'Reply-To: ' . $tfdon_paypal_email . ' <' . $replyto . '>' . "\r\n";
    //echo "<br>org = " . $tfdon_org . "<br>";
    //echo "<br>from = " . $from . "<br>";
    //echo "<br>replyto = " . $replyto . "<br>";
    //echo "<br>headers = " . $headers . "<br>";

    // echo "<br>recipient = " . $recipient . "<br>";
    // echo "<br>subject = " . $subject . "<br>";
    // echo "<br>message = " . $message . "<br>";
    wp_mail( $recipient, $subject, $message );
}
/** 
 *  Build the message part for txn_type (transaction type) = web_accept
 *  This is for a regular non-recurring donation
 *  $ipn - ipn data structure
 *  $output - output array already set up  
 * 
*/
function web_accept_data(&$output, $ipn) {
    $output[] = "Paypal transaction for regular (non-recurring) donation";
    $output[] = "Donor PayPal Name = " . $ipn["first_name"] . " " . $ipn["last_name"];
    $output[] = "Donor PayPal Email Address = " . $ipn["payer_email"];
    $output[] = "Total Donation Amount = " . $ipn["mc_gross"];
    $output[] = "PayPal Payment Fee = " . $ipn["mc_fee"];

    if ($ipn["transaction_subject]"] == "") {
        $ipn["transaction_subject]"] = "Field left blank";
    }
    $output[] = "Giving Instructions = " . $ipn["transaction_subject"];
}

/** 
 *  Build the message part for txn_type (transaction type) = recurring_payment
 *  This is for a recurring donation
 *  $ipn - ipn data structure
 *  $output - output array already set up  
 * 
*/
function recurring_payment_data(&$output, $ipn) {
    $output[] = "Paypal transaction for recurring payment";
    $output[] = "Donor PayPal Name = " . $ipn["first_name"] . " " . $ipn["last_name"];
    $output[] = "Donor PayPal Email Address = " . $ipn["payer_email"];
    $output[] = "Total Donation Amount = " . $ipn["payment_gross"];
    $output[] = "PayPal Payment Fee = " . $ipn["payment_fee"];

    if ($ipn["transaction_subject"] == "") {
        $ipn["transaction_subject"] = "Field left blank";
    }
    $output[] = "Giving Instructions = " . $ipn["transaction_subject"];
}

/** 
 *  Build the message part for txn_type (transaction type) = recurring_payment_profile_created
 *  This is for the creation of a recurring donation
 *  $ipn - ipn data structure
 *  $output - output array already set up   
 *                                              
*/
function recurring_payment_profile_created_data(&$output, $ipn) {
    $output[] = "Paypal transaction for the creation of a recurring payment";
    $output[] = "Donor PayPal Name = " . $ipn["first_name"] . " " . $ipn["last_name"];
    $output[] = "Donor PayPal Email Address = " . $ipn["payer_email"];
    $output[] = "Donation Amount per cycle = " . $ipn["amount_per_cycle"];

    if ($ipn["product_name"] == "") {
        $ipn["product_name"] = "Field left blank";
    }
    $output[] = "Giving Instructions = " . $ipn["product_name"];
}


/** 
 *  Build the message part for unknown transaction type or a transaction type that we did not plan for
 *  Send out the relevant information so we can figure this out. 
 *  $ipn - ipn data structure
 *  $output - output array already set up   
 *                                              
*/
function unknown_ipn_data($output, $ipn) {
    $output[] = "This is a Paypal IPN message we did not plan for. Please let support have a copy of this message";
    $output[] = print_r ($ipn, TRUE);
}
?>