<?php
/**
 * Ionize
 *
 * Default Email template for : User forgot password request
 * This email sends a confirmation link to the user
 *
 * Copy this file to /themes/<your_theme>/mail/user_forgot_password_request.php
 * to replace it by yours.
 *
 * IMPORTANT :
 * Do not modify this file.
 * It will be overwritten when migrating to a new Ionize release.
 *
 */
/**
 * Available tags in this template : <ion:data />
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
							<?php /* Dear .... */ ?>
                            <h1><ion:data:lang key="mail_user_forgot_password_confirm_intro" swap="data::firstname" /></h1>

							<?php /* Hint what the link is for */ ?>
                            <p><ion:lang key="mail_user_forgot_password_confirm_message" swap="global::site_title" /></p>
							<?php /* the new login data */ ?>
                            <p>
                                <ion:lang key="form_label_login"/> : <b><ion:data:username /></b>, <br/>
                                <ion:lang key="form_label_password"/> : <b><ion:data:password /></b> <br/>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
