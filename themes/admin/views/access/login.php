<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo lang('ionize_login') . ' | ' . Settings::get('site_title'); ?></title>
	<meta http-equiv="imagetoolbar" content="no" />
	<link rel="shortcut icon" href="<?php echo theme_url(); ?>images/favicon.ico" type="image/x-icon" />

	<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-core-1.3.2-full-nocompat.js"></script>
	<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-more-1.3.2.1-yc.js"></script>

	<link rel="stylesheet" href="<?php echo theme_url(); ?>css/login.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo theme_url(); ?>css/form.css" type="text/css" />

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

		/*
		 * Reload top window if #desktop object exists
		 * Prevents from having a login window in a panel
		 */
		if ($('desktop'))
		{
			$('desktop').setStyle('display', 'none');
			window.top.location.reload(true);
		}

        window.addEvent('domready', function()
		{
            function fixchromeyellow()
            {
				console.log(navigator.userAgent.toLowerCase().indexOf("safari"));
                if (
					navigator.userAgent.toLowerCase().indexOf("chrome") >= 0
                    ||(navigator.userAgent.toLowerCase().indexOf("safari") >= 0)
				)
                {
                    $$('input["input:-webkit-autofill"]').each(function(el)
                    {
						console.log('coucou');
                        el.clone(true,true).inject(el,"after");
                        el.dispose();
                    });
                }
            }
            window.setTimeout(fixchromeyellow, 50);

        });

	</script>
	

</head>


<body>
	<?php if (ENVIRONMENT == 'development' OR ENVIRONMENT == 'testing'): ?>
		<div id="preprod-flag">
			<?php echo strtoupper(ENVIRONMENT); ?>
		</div>
	<?php endif; ?>

	<!-- Content -->
	<div id="content" class="content" onKeyPress="javascript:doSubmit(event);">
	
		<div id="loginWindow" class=" clearfix">
			
			<div id="version"><?php echo $this->config->item('version'); ?> - Ionize CMS - MIT licence</div>

			<?php if(validation_errors() OR isset($this->login_errors)):?>
				<div class="error">
					<?php echo validation_errors(); ?>
					<?php echo isset($this->login_errors) ? $this->form_validation->_error_prefix.$this->login_errors.$this->form_validation->_error_suffix : ''; ?>
				</div>
			<?php endif; ?>


			<?php echo form_open(current_url(), array('id' => 'login', 'class' => 'login')); ?>
		
		
				<div>
					<label for="username"><?php echo lang('ionize_login_name'); ?></label>
					<?php echo form_input(array('name' 		=> 'username',
										'id' 		=> 'username',
										'value' 	=> set_value('username'),
										'class' => 'inputtext')); ?>
				</div>
		
				<div>
					<label for="password"><?php echo lang('ionize_login_password'); ?></label>
					<?php echo form_password(array(	'name' 		=> 'password',
											'id' 		=> 'password',
											'value' 	=> set_value('password'),
											'class' => 'inputtext')); ?>
				</div>
		
				<div class="action">
					<!-- <?php echo form_checkbox('remember_me', 1); ?> <?php echo lang('ionize_login_remember'); ?> -->
					<button type="submit" name="send" class="submit"><?php echo lang('ionize_login'); ?></button>
				</div>
		
			<?php echo form_close(); ?>

		</div>
	</div>
</body>
</html>

