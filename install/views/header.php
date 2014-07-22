<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo lang('title_ionize_installation')?></title>
<link rel="stylesheet" href="assets/css/installer.css" type="text/css" />
<script type="text/javascript" src="../themes/admin/javascript/mootools-core-1.5.0-full-nocompat-yc.js"></script>
<script type="text/javascript">

	window.addEvent('load', function()
	{
		$$('dl input').addEvent('focus', function(e)
		{
			e.stop();
			this.getParent('dl').addClass('focus');
		});
		$$('dl input').addEvent('blur', function(e)
		{
			e.stop();
			this.getParent('dl').removeClass('focus');
		});
	});

</script>

</head>
<body>

<div id="page">
	<div id="content">
		<div class="block">
			<div id="lang">
				<?php foreach($languages as $l) :?>
					<img src="../themes/admin/styles/original/images/world_flags/flag_<?php echo $l ?>.gif" onclick="javascript:location.href='<?php echo $current_url ?>&lang=<?php echo $l ?>';" />
				<?php endforeach ;?>
			</div>
				<img src="assets/images/ionize_logo_install.png" />
				<p class="version">version <strong><?php echo $version ?></strong></p>
		</div>



