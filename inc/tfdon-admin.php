<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
add_action( 'admin_menu', 'tfdon_add_admin_menu' );
add_action( 'admin_init', 'tfdon_settings_init' );

function tfdon_add_admin_menu(  ) { 
	add_menu_page( 'TF Paypal Donations', 'TF Paypal Donations', 'manage_options', 'tfdon_paypal_donations', 'tfdon_options_page' );
}

function tfdon_settings_init(  ) { 
	register_setting( 'tfdon_pluginPage', 'tfdon_settings' );

	add_settings_section(
	'tfdon_pluginPage_section', 
	__( '', 'wordpress' ), 
	'tfdon_settings_section_callback', 
	'tfdon_pluginPage'
	);

	add_settings_field( 
		'tfdon_organization_name', 
		__( 'Organization Name', 'wordpress' ), 
		'tfdon_organization_name_render', 
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section' 
	);

	add_settings_field( 
		'tfdon_give_to', 
		__( 'Possible Donations List', 'wordpress' ), 
		'tfdon_give_to_render', 
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section' 
	);

	add_settings_field( 
		'tfdon_paypal_email', 
		__( 'Paypal Email Account', 'wordpress' ), 
		'tfdon_paypal_email_render', 
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section' 
	);

	add_settings_field( 
		'tfdon_notification_to_email', 
		__( 'Notification To Email', 'wordpress' ), 
		'tfdon_notification_to_email_render', 
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section' 
	 );

	add_settings_field( 
		'tfdon_disable_css_handle', 
		__( 'Disable Plugin CSS', 'wordpress' ), 
		'tfdon_disable_css_render', 
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section' 
	);

	add_settings_field( 
		'tfdon_paypal_testing_handle', 
		__( 'Use Paypal Sandbox for testing', 'wordpress' ), 
		'tfdon_paypal_testing_render', 
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section' 
	);

	add_settings_field( 
		'tfdon_donate_image', 
		__( 'Donate image URL', 'wordpress' ), 
		'tfdon_donate_image_render',
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section'  
	);

		add_settings_field( 
		'tfdon_ipn_url', 
		__( 'PayPal IPN URL', 'wordpress' ), 
		'tfdon_ipn_url_render',
		'tfdon_pluginPage', 
		'tfdon_pluginPage_section'  
	);
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

function tfdon_organization_name_render(  ) { 

	$options = get_option( 'tfdon_settings' );
	?>
	<input type='text' name='tfdon_settings[tfdon_organization_name]' value='<?php if(isset($options['tfdon_organization_name']))
	{echo $options['tfdon_organization_name'];} ?>' class='wide' required style=''>
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

function tfdon_ipn_url_render(  ) { 
	$PaypalUrl = site_url()."/?action=IPN_Handler";
	$options = get_option( 'tfdon_settings' );
	echo $PaypalUrl;
	?>
	<p class="tfdon_ipn_instructions"><a href="https://developer.paypal.com/docs/api-basics/notifications/ipn/IPNSetup/" target="_blank">Click Here</a> for instructions on setting up Paypal IPN using this URL</p>
	<?php
}

function tfdon_disable_css_render(  ) { 

	$options = get_option( 'tfdon_settings' );
	?>
	<input type='checkbox' id='tfdon_disable_css' name='tfdon_settings[tfdon_disable_css]' 
	<?php checked( isset($options['tfdon_disable_css']), 1 ); ?> value='1'>
	<?php
}

function tfdon_paypal_testing_render(  ) { 

	$options = get_option( 'tfdon_settings' );
	?>
	<input type='checkbox' id='tfdon_paypal_testing' name='tfdon_settings[tfdon_paypal_testing]' 
	<?php checked( isset($options['tfdon_paypal_testing']), 1 ); ?> value='1'>
	<?php
}

function tfdon_settings_section_callback(  ) { 
	echo __( '', 'wordpress' );
}

function tfdon_options_page(  ) { 
	?>
	<form action='options.php' method='post'>
		<input type="hidden" name="destination" value="<?php echo admin_url('admin.php?page=tfdon_donations')?>"/>

		<div id='tfdon-admin'>
			<h2>TF Paypal Donations<br>Setup Page</h2>
			<?php
			settings_fields( 'tfdon_pluginPage' );
			do_settings_sections( 'tfdon_pluginPage' );
			?> 
			</div>
			<div class='tfdon_form_section'> 
			<?php
			submit_button();
			?>
		</div>
	</form>
	<?php
}
?>