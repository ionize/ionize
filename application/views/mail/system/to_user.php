<?php
/**
 * Ionize
 *
 * Default Email template for : System User Message
 * This email is send to the user when his account was changed by one Administrator
 *
 * IMPORTANT :
 * Because this template is used by the backend, it doesn't use Ionize's Tags
 *
 * Copy this file to /themes/<your_theme>/mail/contact/to_user.php
 * to replace it by yours.
 *
 * IMPORTANT :
 * Do not modify this file.
 * It will be overwritten when migrating to a new Ionize release.
 *
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><ion:data:subject /></title>
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
			margin-top: 0;
			font-weight: normal;
		}
		h2{
			display: block;
			color: #2563A1;
			font-family: arial, verdana, sans-serif;
			font-size: 12pt;
			text-align: left;
			line-height: 1.2em;
			margin: 20px 0 0 0;
			font-weight: normal;
		}
		p{margin: 8px 0;}
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
						<h1><?php echo lang('ionize_mail_user_intro', $username) ?></h1>

						<p><?php echo $message_intro ?></p>

						<p><?php echo $message ?></p>

						<h2><?php echo lang('ionize_mail_account_details') ?></h2>
						<p>
							<?php echo lang('ionize_label_firstname') ?> : <strong><?php echo $firstname ?></strong><br/>
							<?php echo lang('ionize_label_lastname') ?> : <strong><?php echo $lastname ?></strong><br/>
							<?php echo lang('ionize_label_email') ?> : <strong><?php echo $email ?></strong><br/>
							<?php echo lang('ionize_label_role') ?> : <strong><?php echo $role ?></strong><br/>
						</p>

						<p>
							<br/>
							<?php echo lang('ionize_mail_thank_you_for_using_our_website', Settings::get('site_title')) ?>
						</p>
						<p>
							<br/>
							<?php echo lang('ionize_mail_signature', Settings::get('site_title')) ?>
						</p>

						<p style="font-size: 8px;color: #444;">
							<br/>
							<br/>
							<?php echo lang('ionize_mail_automatic_message_warning') ?>
						</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
