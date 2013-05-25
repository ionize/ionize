
<div class="block">
	<ul id="nav">

		<li><a class="active" 	href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
		<li><a class="inactive" href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
		<li><a class="inactive" href="?step=settings&lang=<?php echo $lang ?>"><?php echo lang('nav_settings') ?></a></a></li>
		<li><a class="inactive" href="?step=data&lang=<?php echo $lang ?>"><?php echo lang('nav_data') ?></a></a></li>
		<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>

	</ul>
</div>

<div class="block content">

	<h1><?php echo lang('title_welcome') ?></h1>

	<?php echo lang('welcome_text') ?>

	<h2><?php echo lang('title_system_check') ?></h2>


	<?php if(isset($message)) :?>
		<p class="<?php echo $message_type ?>"><?php echo $message ?></p>
	<?php endif ;?>


	<!-- PHP Version -->
	<ul class="check">
		<li class="<?php if($php_version) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo lang('php_version')?> (<b><?php echo phpversion() ?></b>)</li>
		<li class="<?php if($mysql_support) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo lang('mysql_support')?> </li>
		<li class="<?php if($safe_mode) :?>ok<?php else :?>fail<?php endif ;?>">Safe Mode Off </li>
		<li class="<?php if($file_uploads) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo lang('file_uploads')?></li>
		<li class="<?php if($gd_lib) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo lang('gd_lib')?></li>
	</ul>

	<h2><?php echo lang('title_folder_check') ?></h2>

	<ul class="check">
		<?php foreach($check_folders as $folder => $result) :?>
			<li class="<?php if($result) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo $folder ?></li>
		<?php endforeach ;?>
	</ul>


	<h2><?php echo lang('title_files_check') ?></h2>

	<ul class="check">
		<?php foreach($check_files as $file => $result) :?>
			<li class="<?php if($result) :?>ok<?php else :?>fail<?php endif ;?>"><?php echo $file ?></li>
		<?php endforeach ;?>
	</ul>

	<div class="buttons">
		<?php if ($next) :?>
			<button type="button" class="button yes right" onclick="javascript:location.href='?step=database&lang=<?php echo $lang ?>';"><?php echo lang('button_next_step') ?></button>
		<?php endif ;?>
	</div>
</div>