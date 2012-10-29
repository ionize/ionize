<?php
/**
 * Ionize
 *
 * Email template for : MyForm
 * This email is send to the user who just posted the myform form
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
                        <!--
							Dear %s,
						-->
                        <h1><ion:data:lang key="mail_contact_dear" swap="data::name" /></h1>

                        <!--
							Message
							Important : to swap text in the translated string, the 'lang' tag must be called
							with the 'data' tag as parent.
						-->
                        <p><ion:data:lang key="mail_contact_message" swap="global::site_title, data::heard, data::email" autolink="false"/></p>

						<p><ion:lang key="mail_contact_message2" /></p>

                        <p>
                            <ion:lang key="form_label_name"/> : <b><ion:data:name /></b>, <br/>
                            <ion:lang key="form_label_company"/> : <b><ion:data:company /></b> <br/>
                            <ion:lang key="form_label_email"/> : <b><ion:data:email /></b> <br/>
                        </p>

						<p>
							<ion:lang key="mail_contact_thanks" swap="global::site_title" />
						</p>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
