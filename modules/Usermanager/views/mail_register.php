<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><ion:site_title /> - <ion:translation term="module_usermanager_email_registration_title" /></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="<ion:current_lang />" />
		<style type="text/css">
			h1, .headline1
			{
				display: block;
				color: #bcc22a;
				font-family: arial, verdana, sans-serif;
				font-size: 14pt;
				text-align: left;
				line-height: 1.2em;
				font-weight: normal;
			}
			label
			{
				display: block;
				width: 70px;
				float: left;
				text-align: right;
				margin-right: 10px;
			}

			p
			{
				margin-bottom: 10px;
			}

			p.lbl
			{
				height: 15px;
				clear: both;
			}

			body
			{
				color: #2d2f36;
				font-family: arial, verdana, sans-serif;
				font-size: 10pt;
				line-height: 1.2em;
				background-color: #eeeeee;
			}

			.bg_fade
			{
				background-image: url(<ion:theme_url />assets/images/bg_fade.png);
				background-repeat: repeat-x;
			}

			a:link, a:visited, a:active, a:hover
			{
				color: #2d2f36;
				text-decoration: underline;
				font-weight: normal;
			}

			a:hover
			{
				color: #2d2f36;
				text-decoration: none;
			}
		</style>
	</head>
	<body bgcolor="#eeeeee">
		<center>
			<table border="0" width="100%" bgcolor="#000000" cellpadding="0" cellspacing="0" style="border:0; width: 100%; background-color: #000000"><tr>
				<td width="*" align="center"><center><img src="<ion:theme_url />assets/images/email_header.png"></center></td>
			</tr></table>
			<table border="0" width="100%" cellpadding="0" cellspacing="0" style="border:0; width: 100%;"><tr><td class="bg_fade">
				<table border="0" width="880" style="border:0; width: 880px; margin: 0 auto;"><tr><td>
					<h1><ion:translation term="module_usermanager_email_registration_title" /></h1>
					<p><ion:translation term="module_usermanager_email_registration_hello" /> <ion:usermanager request="user" attr="title" is_like="0" from_post_data="register"><ion:translation term="module_usermanager_field_title_mr" /></ion:usermanager><ion:usermanager request="user" attr="title" is_like="1" from_post_data="register"><ion:translation term="module_usermanager_field_title_ms" /></ion:usermanager> <ion:usermanager request="user" attr="screen_name" from_post_data="register" />,</p>
					<p><ion:translation term="module_usermanager_email_registration_thanks" /> <ion:site_title />.</p>
					<p><ion:translation term="module_usermanager_email_registration_text1" /> <ion:site_title /> <ion:translation term="module_usermanager_email_registration_text2" /></p>
					<br />
					<h1><ion:translation term="module_usermanager_email_registration_user_data" /></h1>
					<p><ion:translation term="module_usermanager_email_registration_text3" /> <a href="<ion:usermanager request='global' attr='login_url' />" target="_blank"><ion:translation term="module_usermanager_email_registration_text3_here" /></a> <ion:translation term="module_usermanager_email_registration_text3_end" /></p>
					<ion:usermanager request="global" attr="not_email_as_username">
						<p class="lbl"><label for="username"><ion:translation term="module_usermanager_field_username" />: </label><span><ion:usermanager request="user" attr="username" from_post_data="register" /></span></p>
					</ion:usermanager>
					<ion:usermanager request="global" attr="email_as_username">
						<p class="lbl"><label><ion:translation term="module_usermanager_field_email" />: </label><span><ion:usermanager request="user" attr="email" from_post_data="register" /></span></p>
					</ion:usermanager>
					<p class="lbl"><label><ion:translation term="module_usermanager_field_password" />: </label><span><ion:usermanager request="user" attr="password" from_post_data="register" /></span></p>
					<br />
					<h1><ion:translation term="module_usermanager_email_registration_information" /></h1>
					<p><ion:translation term="module_usermanager_email_registration_text4" /></p>
				</td></tr></table>
			</td></tr></table>
			<table border="0" width="100%" cellpadding="0" cellspacing="0" style="border:0; width: 100%;">
				<tr>
					<td width="*" height="136" style="height: 136px;">&nbsp;</td>
					<td width="872" align="center" rowspan="2"><center><img src="<ion:theme_url />assets/images/email_footer.png"></center></td>
					<td width="*" height="136" style="height: 136px;">&nbsp;</td>
				</tr>
				<tr>
					<td width="*" height="131" bgcolor="#000000" style="background-color: #000000; height: 131px;">&nbsp;</td>
					<td width="*" height="131" bgcolor="#000000" style="background-color: #000000; height: 131px;">&nbsp;</td>
				</tr>
			</table>
		</center>
	</body>
</html>
