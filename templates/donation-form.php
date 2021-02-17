<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

$admin_post_url = esc_url( admin_url("admin-post.php"));
?>
<!-- The below form will collect the donor's name and the giving instructions. 
     After submission of the form, Paypal will send back an IPN that we need to 
     process and acknowledge back to Paypal. Then send and email to the admin 
     notification email address.
-->
<div class="tfdon-all">
<!-- Display list of ministries to donate to (replace cr/nl with <br>) -->  
  <h2 class="tfdon-give-to-h2"><?php echo $options['tfdon_don_list_hdr']; ?></h2> 
  <div class="tfdon-give-to-div"><?php echo nl2br($options['tfdon_give_to']); ?></div>

  <!-- Now set up Paypal form (sandbox for not) -->
  <?php
  if (isset($options['tfdon_paypal_testing'])) {
    ?><form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post"><?php // sandbox
  } else {
    ?><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><?php // live
  }
  ?>
      <input name="tfdon-submit" type="hidden" value="Submit" />
  <?php 
      if (empty($options['tfdon_paypal_email'])) {
          die('Paypal email unavailable');
      }

      $tfdon_paypal_email = $options['tfdon_paypal_email'];
      // $tfdon_org = $options['tfdon_organization_name'];
    ?>

      <input type="hidden" name="business"
          value="<?php echo $tfdon_paypal_email; ?>"> 

      <input type="hidden" name="cmd" value="_donations">

      <!-- Specify details about the contribution -->
      <input type="hidden" name="currency_code" value="USD">

    <!-- Say you don't want paypal to ask for an address -->
      <input type="hidden" name="no_shipping" value="1">

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
</div>