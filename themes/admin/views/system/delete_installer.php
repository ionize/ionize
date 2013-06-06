<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo lang('title_ionize_installation'); ?></title>

<link rel="stylesheet" href="<?php echo base_url(); ?>install/assets/css/installer.css" type="text/css" />

</head>

<body>

<div id="page">

	<div id="content">
		<div class="block">

			<img src="<?php echo base_url(); ?>install/assets/images/ionize_logo_install.png" />

			<h1><?php echo lang('title_delete_installer'); ?></h1>

			<p><?php echo lang('ionize_message_delete_installer'); ?></p>

			<div class="buttons">
				<button type="button" class="button yes left" onclick="javascript:location.href='<?php echo admin_url(); ?>';"><?php echo lang('button_delete_installer_done_admin'); ?></button>
				<button type="button" class="button yes right" onclick="javascript:location.href='<?php echo base_url(); ?>';"><?php echo lang('button_delete_installer_done_site'); ?></button>
			</div>

		</div>
	</div>

</div>

</body>
</html>
