<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

$admin_post_url = esc_url( admin_url("admin-post.php"));
?>
<!-- The below form will collect the donor's name and the giving instructions. 
     After submission of the form, Paypal will send back an IPN that we need to 
     process and acknowledge back to Paypal. Then send and email to the admin 
     notification email address.
-->

<!-- Display list of ministries to donate to (replace cr/nl with <br>) -->  
<h2 class="tfdon-give-to-h2">Ministries you can donate to:</h2> 
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

    if (empty($options['tfdon_organization_name'])) {
        die('Organization name unavailable');
    }
    $tfdon_paypal_email = $options['tfdon_paypal_email'];
    $tfdon_org = $options['tfdon_organization_name'];
  ?>

    <input type="hidden" name="business"
        value="<?php echo $tfdon_paypal_email; ?>"> 

    <input type="hidden" name="cmd" value="_donations">

    <!-- Specify details about the contribution -->
    <!-- <input type="hidden" name="product_name" value="<?php echo $tfdon_org; ?>"> -->

    <input type="hidden" name="currency_code" value="USD">

  <!-- Say you don't want paypal to ask for an address -->
    <input type="hidden" name="no_shipping" value="1">

    <!-- Say you don't want paypal to ask for a note - not allowed with recurring donations -->
    <input type="hidden" name="no_note" value="1">

    <!-- Fields for Donor name, email addr and donation instructions for transaction email -->
    <table class="tfdon-form">
     <!-- <tr>
        <td> -->
          <!-- item_number is a pass-thru value not used by donations - we will use it for the donor name -->
          <!-- <input type="text" name="item_number" class="tfdon-field" maxlength="200" placeholder= "Your Name" required> -->
     <!--   </td>
      </tr> -->
     <!-- <tr>
        <td> -->
         <!-- <input type="email" name="email" class="tfdon-field" maxlength="200"  placeholder= "Your Email Address" required> -->
     <!--   </td>
      </tr> -->
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
          <!-- <input type="text" name="n1" id="n1" onkeyup="sync()"> 
          <input type="text" name="n2" id="n2"/>  -->
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

        <!--   <input type="image" src="< ?php echo $options['tfdon_donate_image']; ?>"  alt="PayPal - The safer, easier way to pay online!" > --> 
        </td>
      </tr>
    </table>
</form>