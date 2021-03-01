<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
* 
* This is used to display the admin options page for this plugin
*
*/

add_action( 'admin_menu', 'tfdon_add_admin_menu' );
add_action( 'admin_init', 'tfdon_define_section_and_fields' );

function tfdon_add_admin_menu(  ) { 
	add_menu_page( 'TF Paypal Donations', 'TF Paypal Donations', 'manage_options', 'tfdon_paypal_donations', 'tfdon_options_page' );
}

function tfdon_define_section_and_fields(  ) { 
	// Array with names and descriptions of fields to be used in the admin pages
	$tfdon_fields = array (
			"don_list_hdr" => "Donations List Header",
			"give_to" => "Donations List", 
			"paypal_email" => "Paypal Email Account", 
			"notification_to_email" => "Notification To Email",
			"donate_image" => "Donate image URL",  
			"disable_css" => "Disable Plugin CSS", 
			"paypal_testing" => "Use Paypal Sandbox for testing", 
			"log" => "Turn on debug logging", 
			"log_display" => "URL of page for log file displays",  
			"ipn_url" => "URL to use in PayPal setup for IPN"
	);

	register_setting( 'tfdon_pluginPage', 'tfdon_settings' );

	add_settings_section(
	'tfdon_pluginPage_section', 
	__( '', 'wordpress' ), 
	'tfdon_settings_section_callback', 
	'tfdon_pluginPage'
	);

	foreach ($tfdon_fields as $key => $value) {
		add_settings_field( 
			'tfdon_' . $key, 
			__( $value, 'wordpress' ), 
			'tfdon_' . $key . '_render', 
			'tfdon_pluginPage', 
			'tfdon_pluginPage_section' 
			);
		}
}

function tfdon_give_to_render(  ) { 

	$options = get_option( 'tfdon_settings' );
	?>
	<textarea name='tfdon_settings[tfdon_give_to]' rows="10" cols="70" maxlength="1000" class='wide' required><?php if(isset($options['tfdon_give_to']))
	{echo $options['tfdon_give_to'];} ?></textarea>
	<?php
}

function tfdon_paypal_email_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='email' name='tfdon_settings[tfdon_paypal_email]' value='<?php if(isset($options['tfdon_paypal_email']))
	{echo $options['tfdon_paypal_email'];} ?>' class='wide' required>
	<?php
}

function tfdon_notification_to_email_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
		<input type='email' name='tfdon_settings[tfdon_notification_to_email]' value='<?php if(isset($options['tfdon_notification_to_email']))
		{echo $options['tfdon_notification_to_email'];} ?>' class='wide' required>
	<?php
}

function tfdon_notification_from_email_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='email' name='tfdon_settings[tfdon_notification_from_email]' value='<?php if(isset($options['tfdon_notification_from_email']))
	{echo $options['tfdon_notification_from_email'];} ?>' class='wide' required>
	<?php
}

function tfdon_notification_reply_to_email_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='email' name='tfdon_settings[tfdon_notification_reply_to_email]' value='<?php if(isset($options['tfdon_notification_reply_to_email']))
	{echo $options['tfdon_notification_reply_to_email'];} ?>' class='wide' required>
	<?php
}

function tfdon_don_list_hdr_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='text' name='tfdon_settings[tfdon_don_list_hdr]' value='<?php if(isset($options['tfdon_don_list_hdr']))
	{echo $options['tfdon_don_list_hdr'];} ?>' class='wide' required style=''>
	<?php
}

function tfdon_donate_image_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='text' name='tfdon_settings[tfdon_donate_image]' 
	value='<?php if(isset($options['tfdon_donate_image']))
	{echo $options['tfdon_donate_image'];} else{ echo("https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif");} ?>' class='wide'>
	<?php
}

function tfdon_disable_css_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='checkbox' id='tfdon_disable_css' name='tfdon_settings[tfdon_disable_css]' 
	<?php checked( isset($options['tfdon_disable_css']), 1 ); ?> value='1'>
	<?php
}

function tfdon_log_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	
	$upload_dir = wp_upload_dir();
  $upload_dir = $upload_dir['basedir'];
  $file_curr  = $upload_dir . '/' . TFDON_CURRENT_LOG;
	?>
	<input type='checkbox' id='tfdon_log' name='tfdon_settings[tfdon_log]' 
	<?php checked( isset($options['tfdon_log']), 1 ); ?> value='1'>  <?php echo "File location & names: " .$upload_dir . '/' . TFDON_CURRENT_LOG
					 . " and ... " . TFDON_OLDER_LOG; ?>
	<?php
}

function tfdon_log_display_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='text' name='tfdon_settings[tfdon_log_display]' 
	value='<?php if(isset($options['tfdon_log_display']))
	{echo $options['tfdon_log_display'];} ?>' class='wide' required>
	<?php
}

function tfdon_paypal_testing_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<input type='checkbox' id='tfdon_paypal_testing' name='tfdon_settings[tfdon_paypal_testing]' 
	<?php checked( isset($options['tfdon_paypal_testing']), 1 ); ?> value='1'>
	<?php
}

function tfdon_ipn_url_render(  ) { 
	$options = get_option( 'tfdon_settings' );
	?>
	<?php echo site_url()."/?action=". TFDON_IPN_ID; ?><br>
	For instructions see: https://developer.paypal.com/docs/api-basics/notifications/ipn/IPNSetup/
	<?php
}

function tfdon_settings_section_callback(  ) { 
	echo __( '', 'wordpress' );
}

function tfdon_options_page(  ) { 
	?>
<div id='tfdon-admin'>
	<form action='options.php' method='post'>
		<input type="hidden" name="destination" value="<?php echo admin_url('admin.php?page=tfdon_donations')?>"/>
			<h2>TF Paypal Donations<br>Setup Page</h2>
			<?php
			settings_fields( 'tfdon_pluginPage' );
			do_settings_sections( 'tfdon_pluginPage' );
			tfdon_log("log in admin 1: ", $_POST); 
			?>
		<div class='tfdon_form_section'>  
			<?php	submit_button();
			tfdon_log("log in admin 2: ", $_POST); 
			?>
	 	</div> 
	</form>
</div>
<?php
}
?>