<?php
/**
 * Ionize
 *
 * Default Email template for Contact Form
 * This email is supposed to be send to the website's email
 *
 * Copy this file to /themes/<your_theme>/mail/contact/to_admin_ajax.php
 * to replace it by yours.
 *
 * IMPORTANT :
 * Do not modify this file.
 * It will be overwritten when migrating to a new Ionize release.
 *
 * Receives vars :
 *
 * $subject :	Email subject
 * $name :		Form field : name of the user
 *
 * ...	:		All other form fields

 */

// Load the SFS Helper, to get the URL used to declare the message as spam to the SFS Server
// (needs one API key)
get_instance()->load->helper('sfs_helper');

$spam_url = NULL;

if (function_exists('get_sfs_declare_spam_url'))
	$spam_url = get_sfs_declare_spam_url();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $subject ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="<ion:current_lang />" />
	<style type="text/css">
		body{
			color: #000;
			font-family: arial, verdana, sans-serif;
			font-size: 10pt;
			line-height: 1.2em;
			background-color: #fff;
		}
		h1{
			display: block;
			color: #2563A1;
			font-family: arial, verdana, sans-serif;
			font-size: 14pt;
			text-align: left;
			line-height: 1.2em;
			font-weight: normal;
			margin-top: 0;
		}
		p{margin-bottom: 10px;}
		a:link, a:visited, a:active, a:hover{
			color: #098ED1;
			text-decoration: underline;
			font-weight: normal;
		}
		a:hover	{
			color: #2563A1;
			text-decoration: none;
		}
	</style>
</head>
<body>

<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="bg_fade">
			<table border="0" width="880">
				<tr>
					<td>
						<p><?php echo lang('mail_website_contact_message', Settings::get('site_title')) ?></p>

						<p>
							<b><?php echo lang('form_label_name') ?></b> : <?php echo $name ?><br/>
							<b><?php echo lang('form_label_email') ?></b> : <?php echo $email ?><br/>
						</p>
						<p>
							<b><?php echo lang('form_label_message') ?></b> : <br/>
							<?php echo $message ?>
						</p>

						<?php
						/*
						 * Declare as Spam
						 * The module SFS must be installed and properly configured.
						 *
						 */
						?>
						<?php if ( ! is_null($spam_url)): ?>
							<p>
								<br />
								<br />
								<br />
								Declare as SPAM : <?php echo $spam_url ?>
							</p>
						<?php endif; ?>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>
