<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/*
Template Name: TFDON Log Display
 *
 *
 * This template contains two forms:
 * 
 * 1. To collect giving instructions. 
 *    After submission of the form, Paypal will send back an IPN 
 *    that we need to process and acknowledge back to Paypal. 
 *    Then to send and email to the admin notification email address
 *    containing donation details. 
 * 
 * 2. A form that only displays if an admin user is logged in. This
 *    form provides a choice of which log file to display (for 
 *    troubleshooting purposes).
 * 
 */ 
 
?>
<!-- Display list of ministries to donate to (replace cr/nl with <br>) -->  
  <h2 class="tfdon-give-to-h2"><?php echo $options['tfdon_don_list_hdr']; ?></h2> 
  <div class="tfdon-give-to-div"><?php echo nl2br($options['tfdon_give_to']); ?></div>

  <!-- Now set up Paypal form (sandbox for not) -->
  <?php
  if (isset($options['tfdon_paypal_testing'])) {
    ?>
    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
  <?php // sandbox 
  } else {
    ?>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post"> 
  <?php // live  
  }
      ?>
      <?php 
      if (empty($options['tfdon_paypal_email'])) {
          die('TF Paypal Donations Setup Page: Paypal email unavailable');
      }
      $tfdon_paypal_email = $options['tfdon_paypal_email'];
      ?>
      <input type="hidden" name="business" value="<?php echo $tfdon_paypal_email; ?>"> 

      <input type="hidden" name="cmd" value="_donations">

      <!-- Specify details about the contribution -->
      <input type="hidden" name="currency_code" value="USD">

      <!-- Say you don't want paypal to ask for an address -->
      <input type="hidden" name="no_shipping" value="1">

      <input type="hidden" name="item_number" value="The Fellowship"> <!--  did not help -->

      <!-- Say you don't want paypal to ask for a note - not allowed with recurring donations --> 
      <input type="hidden" name="no_note" value="1">

      <!-- Fields for Donor name, email addr and donation instructions for transaction email -->
      <table class="tfdon-form">
        <tr>
          <td>
            <textarea name="custom" id="cust" class="tfdon-field" rows="4" cols="40" maxlength="127" onkeyup="sync()" placeholder= "Donation Instructions - General Fund will be used if this field is left empty"></textarea>
            <!-- for different interfaces we need to use (steal) different fields to get the instructions to paypal -->
            <input type="hidden" id="prod" name="product_name" value="">
            <input type="hidden" id="item" name="item_name" value="">
            <input type="hidden" id="trans" name="transaction_subject" value="">
            <!-- script to keep the various fields in sync when "custom" is changed-->
            <script>
            function sync()
            {
              var n1 = document.getElementById('cust');
              var n2 = document.getElementById('prod');
              var n3 = document.getElementById('item');
              var n4 = document.getElementById('trans');
              n4.value = n3.value = n2.value = n1.value; // make them all the same value
            }
            </script>
          </td>
        </tr>
        <tr>
          <td>
            <!-- now the submit button -->
            <input type="image" name="submit" id="btn"
            src="<?php echo $options['tfdon_donate_image']; ?>"
            alt="PayPal - The safer, easier way to pay online!">
            <img alt="" width="1" height="1"
            src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif">
          </td>
        </tr>
      </table>
  </form>

<?php 
// Log  file form only if admin account logged in and its not a delete action
$current_user = wp_get_current_user();
if (user_can( $current_user, 'administrator' ) && (!isset ($_GET['file']))) { 
  ?>
  <div id="tfdon-log">	
    <h2>Log File View</h2>
    <p>This logging feature will keep up to two files. When the first file (tf_paypal_donate.log) reaches a predefined size, it will be renamed to tf_paypal_donate_old.log and a new tf_paypal_donate.log will be created. Use the selectors below to view a file or to delete all files.</p>
    <form method="POST" action="" class="tfdon-log-form">
      <p>Select file:</p><br class="tfdon-vis">
        <input name="tfdon_log" type="hidden" value="Submit" />
        <div class="tfdon-radio">
          <input type="radio" id="Current File" name="what_file" value="<?php echo TFDON_CURRENT_LOG?>" checked>
          <label class="tfdon-radio-1" for="Current File">Current File</label>
        </div>  
        <div class="tfdon-radio">      
          <input type="radio" id="Older File" name="what_file" value="<?php echo TFDON_OLDER_LOG?>">
          <label class="tfdon-radio-2" for="Older File">Older File</label><br class="tfdon-vis">
        </div>  
        <input type="submit" class="button button-primary tfdon-button" value="Click to view" alt="log File choice">  
    </form>
    <form method="POST" action="" class="tfdon-log-delete-form">
      <p>Delete files (Do this to isolate a problem) :</p><br class="tfdon-vis">
        <input name="tfdon_log_delete" type="hidden" value="Submit" /> 
        <input type="submit" class="button button-primary tfdon-button" value="Click to Delete Log Files" alt="log File delete">  
    </form>
  </div> 
  <?php
  }
  if ((isset ($_GET['file'])) && ($_GET['file'] == 'deleted')) {
  ?>
  <div id="tfdon-log">
    <h2>Log Files Deleted</h2>
  </div>  
  <!-- Remove query string from URL -->
  <script>
    var newURL = location.href.split("?")[0];
    window.history.pushState('object', document.title, newURL);
  </script>
  <?php
  }
  ?>