
<ul id="nav">

	<li><a href="" class="active"><?php echo lang('nav_check') ?></a></li>
	<li><a class="inactive"><?php echo lang('nav_db') ?></a></li>
	<li><a class="inactive"><?php echo lang('nav_settings') ?></a></a></li>
	<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>
	
</ul>


<!-- Intro text -->
<?php echo lang('welcome_text') ?>

<h2><?php echo lang('title_system_check') ?></h2>


<!-- User message -->
<?php if(isset($message)) :?>

	<p class="<?php echo $message_type ?>"><?php echo $message ?></p>

<?php else :?>
	
	<?php echo lang('system_check_text') ?>

<?php endif ;?>


<!-- PHP Version -->
<dl class="first list">
	<dt class="large">
		<label for="title"><?php echo lang('php_version')?> (<b><?php echo phpversion() ?></b>)</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($php_version) :	?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large">
		<label for="title"><?php echo lang('mysql_support')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($mysql_support) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large">
		<label for="title"><?php echo lang('file_uploads')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($file_uploads) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large">
		<label for="title"><?php echo lang('gd_lib')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($gd_lib) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>


<!--<h3><?php echo lang('title_check_writing_rights') ?></h3>-->

<?php echo lang('write_check_text') ?>


<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_config_dir')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_config_dir) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_config_dir')?>config.php</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_config_config) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_config_dir')?>database.php</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_config_database) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_config_dir')?>email.php</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_config_email) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_config_dir')?>language.php</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_config_language) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_files')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_files) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="large left">
		<label for="title"><?php echo lang('write_themes')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($write_themes) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<?php if ($next) :?>
	<button type="button" class="button yes right" onclick="javascript:location.href='?step=database&lang=<?php echo $lang ?>';"><?php echo lang('button_next_step') ?></button>
<?php endif ;?>

<br/>


