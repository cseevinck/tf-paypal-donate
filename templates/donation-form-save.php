<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

$admin_post_url = esc_url( admin_url("admin-post.php"));

?>
<!-- The below form will collect the donor's name and email and the giving 
     instructions. After submission of the form, "process_form_data" in tfdon-donations.php will get control. There a Paypal request form will 
     and that form will be auto submitted. The admin will need to reconcile 
     the email details with the paypal transactions. There is a chance that
     user will not complete the paypal transaction which will result in an 
     orphan email which could be used to reach out to the user to offer help.
-->

<!-- Display list of ministries to donate to (replace cr/nl with <br>) -->  
<h2 class="tfdon-give-to-h2">Ministries you can donate to:</h2> 
<div class="tfdon-give-to-div"><?php echo nl2br($options['tfdon_give_to']); ?></div>

<form method="POST" action="" class='tfdon-donations-form donation-form'>
    <input name="tfdon-submit" type="hidden" value="Submit" />
 <?php 
    if (empty($options['tfdon_paypal_email'])) {
        die('Paypal email unavailable');
    }

    if (empty($options['tfdon_organization_name'])) {
        die('Organization name unavailable');
    }
?>
    <!-- Fields for Donor name, email addr and Donation instructions
      for transaction email -->
    <table>
      <tr>
        <td>
          <input type="text" name="donor_name" class="tfdon-field" maxlength="200" placeholder= "Your Name" required>
        </td>
      </tr>
      <tr>
        <td>
          <input type="email" name="email_address" class="tfdon-field" maxlength="200"  placeholder= "Your Email Address" required>
        </td>
      </tr>
      <tr>
        <td>
          <textarea name="donation_instructions" class="tfdon-field" rows="5" cols="60" maxlength="500" placeholder= "Donation Instructions - General Fund will be used if this field is left empty"></textarea>
        </td>
      </tr>
      <tr>
        <td>
          <!-- now the submit button -->
           <input type="image" src="<?php echo $options['tfdon_donate_image']; ?>"  alt="PayPal - The safer, easier way to pay online!" >
        </td>
      </tr>
    </table>
</form>