<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= Settings::get('site_title') ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<link rel="shortcut icon" href="<?= theme_url() ?>images/favicon.ico" type="image/x-icon" />

	<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-1.2.4-core-nc.js"></script>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-1.2.4.4-more-yc.js"></script>

	<link rel="stylesheet" href="<?= theme_url() ?>css/login.css" type="text/css" />
	<link rel="stylesheet" href="<?= theme_url() ?>css/form.css" type="text/css" />

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


	</script>
	

</head>


<body>

	<!--Content -->
	<div id="content" class="content" onKeyPress="javascript:doSubmit(event);">
	
		<div id="loginWindow" class=" clearfix">
			
			<div id="version"><?= $this->config->item('version') ?> - Ionize CMS - MIT licence</div>

			<?php if(validation_errors() OR isset($this->login_errors)):?>
				<div class="error">
					<?=validation_errors()?>
					<?=isset($this->login_errors) ? $this->form_validation->_error_prefix.$this->login_errors.$this->form_validation->_error_suffix : ''?>
				</div>
			<?php endif ?>


			<?=form_open(current_url(), array('id' => 'login', 'class' => 'login'))?>
		
		
				<div>
					<label for="username"><?=lang('ionize_login_name')?></label>
					<?=form_input(array('name' 		=> 'username',
										'id' 		=> 'username',
										'value' 	=> set_value('username'),
										'class' => 'inputtext'))?>
				</div>
		
				<div>
					<label for="password"><?=lang('ionize_login_password')?></label>
					<?=form_password(array(	'name' 		=> 'password',
											'id' 		=> 'password',
											'value' 	=> set_value('password'),
											'class' => 'inputtext'))?>
				</div>
		
				<div class="action">
					<!-- <?=form_checkbox('remember_me', 1)?> <?=lang('ionize_login_remember')?> -->
					<button type="submit" name="submit" class="submit"><?=lang('ionize_login')?></button>
					<!-- <p class="fake_label"><small><?=anchor('user/forgot', lang('ionize_forgot_password'))?></small></p> -->
				</div>
		
			<?=form_close()?>


		</div>
	
	</div>
	<!-- Content : end -->
</body>

</html>

