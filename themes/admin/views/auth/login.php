<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo lang('ionize_login') . ' | ' . Settings::get('site_title'); ?></title>
	<meta http-equiv="imagetoolbar" content="no" />
	<link rel="shortcut icon" href="<?php echo theme_url(); ?>images/favicon.ico" type="image/x-icon" />

    <script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-core-1.5.0-full-nocompat-yc.js"></script>
    <script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-more-1.5.0-yc.js"></script>

	<link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/login.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/form.css" type="text/css" />

	<script type="text/javascript">
	
		var MUI;

		function doSubmit(e) 
		{
			var code;
			if (!e) var e = window.event;
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			var character = String.fromCharCode(code);
	
			if (code==13)
			{
				var ojbNom =	document.getElementById("username");
				var objPass =	document.getElementById("password");
							
				if (ojbNom.value!="" && objPass.value!="")
				{
					var formObj = document.getElementById("login");
					formObj.submit();
				}
			}
		}

		// Reload top window if #desktop object exists
		// Prevents from having a login window in a panel
		if ($('desktop'))
		{
			$('desktop').setStyle('display', 'none');
			window.top.location.reload(true);
		}

        window.addEvent('domready', function()
		{
			document.getElementById('username').focus();

			$(document.body).addClass('bg-' + Math.floor(Math.random()*(6-1+1)+1));

			// fix chrome forcing yellow background of input
			window.setTimeout(function() {
				if (
					navigator.userAgent.toLowerCase().indexOf("chrome") >= 0
					||(navigator.userAgent.toLowerCase().indexOf("safari") >= 0)
				) {
					$$('input["input:-webkit-autofill"]').each(function(el)
						{
					   el.clone(true,true).inject(el,"after");
					   el.dispose();
					});
				}
			}, 50);
        });
	</script>
</head>

<body onKeyPress="doSubmit(event);">
	<div class="bg-overlay grid-20"></div>

	<?php if (ENVIRONMENT != 'production'): ?>
		<div id="preprod-flag">
			<?php echo strtoupper(ENVIRONMENT); ?>
		</div>
	<?php endif; ?>


	<div id="loginWindow" class=" clearfix">
		<div class="bg-login"></div>
		<div id="logo"></div>
		<div id="version"><?php echo $this->config->item('version'); ?> - Ionize CMS - MIT licence</div>

		<?php if( ! empty($error)):?>
			<div class="error">
					<?php echo $error ?>
			</div>
		<?php endif; ?>


		<?php echo form_open(current_url(), array('id' => 'login', 'class' => 'login')); ?>

			<div>
				<?php echo form_input(array(
					'name' 		=> 'username',
					'id' 		=> 'username',
					'value' 	=> set_value('username'),
					'class' => 'inputtext',
					'placeholder' => lang('ionize_login_name')
				)); ?>
			</div>

			<div>
				<?php echo form_password(array(
					'name' 		=> 'password',
					'id' 		=> 'password',
					'value' 	=> set_value('password'),
					'class' => 'inputtext',
					'placeholder' => lang('ionize_login_password')
				)); ?>
			</div>

			<div class="action">
				<input type="submit" class="submit" value="<?php echo lang('ionize_login'); ?>" />
			</div>

		<?php echo form_close(); ?>

	</div>
</body>
</html>

