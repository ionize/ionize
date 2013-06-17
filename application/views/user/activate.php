<?php

/**
 * Account Activation view
 * Gives the users information about his account activation
 * 
 * This view is used by controllers/user->activate()
 * Displays the success or error message regarding activation 
 * and redirect to home page.
 *
 * If you want to personnalize this view for your theme, simply copy this view in 
 * the folder : /themes/your_theme/views/user/activate.php
 *
 */

?>
<html>
<head>
	<script type="text/javascript">
		function redirect(){
			window.location = '<?php echo base_url() ?>';
		}
	</script>
</head>
<body onLoad="setTimeout('redirect()', 5000)">

<h2><?php echo($title) ?></h2>

<p><?php echo($message) ?></p>

<p><a href="<?php echo base_url() ?>"><?php echo(lang('connect_home_page')); ?></a></p>


</body>
</html>