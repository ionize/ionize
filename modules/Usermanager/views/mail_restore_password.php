<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><ion:site_title /> - <ion:translation term="module_usermanager_email_registration_title" /></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="<ion:current_lang />" />
		<style type="text/css">
			h1, .headline1 {
				display: block;
				color: #004e97;
				font-family: arial, verdana, sans-serif;
				font-size: 14pt;
				text-align: left;
				line-height: 1.2em;
				font-weight: normal;
			}
			label{
				display: block;
				width: 160px;
				float: left;
				text-align: right;
				margin-right: 10px;
			}
			p{
				margin-bottom: 10px;
			}
			p.footer{
				font-size: 9px;
				margin-top: 20px;
				margin-bottom: 10px;
				color:#999;
			}
			p.lbl{
				height: 15px;
				clear: both;
			}
			label{
				color:#888;
			}
			body{
				color: #2d2f36;
				font-family: arial, verdana, sans-serif;
				font-size: 10pt;
				line-height: 1.2em;
				background-color: #eeeeee;
			}
			a:link, a:visited, a:active{
				color: #3a76af;
				text-decoration: underline;
				font-weight: normal;
			}
			a:hover	{
				color: #135b9e;
				text-decoration: none;
			}
			table tr td {
				text-align: left;
			}
		</style>
	</head>
	<body bgcolor="#eeeeee" background="#eeeeee">
		<center>
			<table border="0" bgcolor="#eeeeee" width="100%" cellpadding="0" cellspacing="0" style="border:0; width: 100%;background-color:#eeeeee;">
				<tr>
					<td width="*" align="center"><center><img src="<ion:base_url />files/website/mail_header.jpg"></center></td>
				</tr>
			</table>
			<table border="0" bgcolor="#eeeeee" width="100%" cellpadding="0" cellspacing="0" style="border:0; width: 100%;background-color:#eeeeee;">
			<tr>
				<td>
				<table border="0" width="700" style="border:0; width: 700px; margin: 0 auto;">
					<tr>
						<td>
							<h1><ion:translation term="module_usermanager_email_registration_hello" />&nbsp;<ion:get var="screen_name" />,</h1>
							
							<p><ion:translation term="module_usermanager_email_restore_password_text1" /> <a href="<ion:base_url />" target="_blank"><ion:site_title /></a>.</p>
							<p><ion:translation term="module_usermanager_email_restore_password_text2" /></p>
							<p><ion:translation term="module_usermanager_email_restore_password_text3" /></p>
							<br/>
							
							<h1><ion:translation term="module_usermanager_email_restore_password_title_new_login" /></h1>
					
							<ion:usermanager request="global" attr="not_email_as_username">
								<p class="lbl">
									<label for="username"><ion:translation term="module_usermanager_field_username" />: </label>
									<span><strong><ion:get var="username" /></strong></span></p>
							</ion:usermanager>
							<ion:usermanager request="global" attr="email_as_username">
								<p class="lbl">
									<label><ion:translation term="module_usermanager_field_email" />: </label>
									<span><strong><ion:get var="email" /></strong></strong></span>
								</p>
							</ion:usermanager>
							<p class="lbl">
								<label><ion:translation term="module_usermanager_field_password" />: </label>
								<span><strong><ion:get var="password" /></strong></span>
							</p>
							<br />
							
							
						</td>
					</tr>
				</table>
				</td>
			</tr>
			</table>
			<table border="0" bgcolor="#eeeeee" width="100%" cellpadding="0" cellspacing="0" style="border:0; width: 100%;background-color:#eeeeee;">
				<tr>
					<td>
						<table bgcolor="#eeeeee" border="0" width="700" style="border:0; width: 700px; margin: 0 auto;background-color:#eeeeee;"><tr><td>
						<tr>
							<td>
								<p class="footer">
									<ion:translation term="module_usermanager_email_registration_text4" />
								</p>
							</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>
