<?php
/**
 * Ionize
 *
 * Default Email template for User registration
 * This email is supposed to be send to the website's email
 *
 * Copy this file to /themes/<your_theme>/mail/register/to_admin.php
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
							<h1><ion:lang key="mail_website_registration_subject" /></h1>

							<p><ion:lang key="mail_website_registration_message" /></p>

							<p>
                                <ion:lang key="form_label_firstname"/> : <ion:data:firstname /><br/>
                                <ion:lang key="form_label_lastname"/> : <ion:data:lastname /><br/>
                                <ion:lang key="form_label_email"/> : <ion:data:email /><br/>
                                IP : <ion:data:ip /><br/>
							</p>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
