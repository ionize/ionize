<?php

/**
 * Account Activation view
 * Gives the users informations about his account activation
 * 
 * This view is used by user->activate()
 * Displays the success or error message regarding activation 
 * and redirect to home page.
 *
 * If you want to personnalize this view for your theme, simply copy this view in 
 * the folder : /themes/your_theme/views/access/
 *
 */

?>
<html>
<body>

<h2><?php echo($title) ?></h2>

<p><?php echo($message) ?></p>

<p><a href="<?php echo base_url() ?>"><?php echo(lang('access_home_page')); ?></a></p>


</body>
</html>