=== Plugin Name ===

Contributors:      Corky Seevinck
Plugin Name:       TF Paypal Donations
Tags:              paypal, donations using IPN
Author URI:        http://www.chs-webs.com
Author:            Corky Seevinck
Tested up to:      5.6.1
Version:           1.2

== Description ==

This plugin allows sites to accept user-entered custom donation amounts through Paypal, including recurring donations.  Paypal's buttons have a drawback in that they do not provide a consistent design w.r.t. the ability to send a note. A note is required to allow a donar to give instructions about their donation. e.g. to specify how the total donation should be devided between various funds. This plugin will collect a note specifying donation instructions before going to PayPal. The user then chooses their payment method and whether the donation is recurring or not. The plugin requires that an IPN url is placed into the Paypal configuration for the receiving account. The note is placed in a pass through variable whitch is returned in the IPN. The plugin then builds an email to be sent to the specified receiving account.

The front end view of the plugin is very basic. It contains a block of text which describes the funds to which the donation can be made. There is a basic css file which can be turned on or off throught the admin i/f.

Orginal plugin by Peter VanKoughnett. Too many changes to keep giving him credit as author.

== Installation ==

1. Install the plugin
1. Customize the plugin settings
1. Use the shortcode [tf-paypal-donations] where you want the plugin to output

== Upgrade Notice ==

=1.3=
Add separate page for log display and allow for defining that page in admin
=1.2=
More cleanup and refining of logging feature
Change css tags and function names to be unique (tfdon at start of names)
=1.1=
Cleanup and add logging feature
=1.0=
* First Version

== Screenshots ==
1. Screenshot of front end of plugin output
2. Screenshot of plugin admin interface

== Changelog ==

=1.0.0 =
* First version

== Frequently Asked Questions ==

=How do I customize the look of the plugin?=

I haven't built any tools to customize the look of the plugin yet.  You can disable the plugin CSS and write your own CSS.