<?php
/**
 * Ionize Ajaxform module
 *
 * Default Email template for : User Message
 * This email is send to the user from the contact form
 *
 * Copy this file to /themes/<your_theme>/mail/contact/to_user_ajax.php
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
 *
 */
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
						<?php
						/*
						 * Dear
						 */
						?>
						<h1><?php echo lang('mail_user_contact_intro', $name) ?></h1>

						<?php
						/*
						 * Thank you !
						 */
						?>
						<p><?php echo lang('mail_user_contact_message') ?></p>

						<p>
							<br/>
							<b><?php echo Settings::get('site_title') ?></b>
						</p>

						<p style="font-size: 8px;color: #444;">
							<br/>
							<br/>
							<?php echo lang('mail_automatic_message_warning') ?>
						</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>
