<html>
<head>
<title>Email from <?php echo site_url(); ?></title>
<style type="text/css">

    /*
        Add / Edit your email HTML and CSS Styles here.
        CodeIgniter will strip HTML for email clients that are not able to read HTML emails
    */
	body {
		background-color: #fff;
		padding: 10px;
		font-family: Lucida Grande, Verdana, Sans-serif;
		font-size: 12px;
		color: #000;
	}

</style>
</head>
<body>
    <div id="content">

    	<h1>Hello,</h1>

        <div id="message">
			<?php echo $message_body; ?>
		</div>

        <hr />
        <p>Thank you,<br />
        <?php echo anchor('', Access()->reg_site_name); ?>.</p>
	</div>
</body>
</html>