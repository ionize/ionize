
<ul id="nav">

	<li><a href="" class="active"><?php echo lang('nav_check') ?></a></li>
	<li><a class="inactive"><?php echo lang('nav_db') ?></a></li>
	<li><a class="inactive"><?php echo lang('nav_settings') ?></a></a></li>
	<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>
	
</ul>


<!-- Intro text -->
<?php echo lang('welcome_text') ?>

<h2 class="first"><?php echo lang('title_system_check') ?></h2>


<!-- User message -->
<?php if(isset($message)) :?>

	<p class="<?php echo $message_type ?>"><?php echo $message ?></p>

<?php endif ;?>


<!-- PHP Version -->
<dl class="first list">
	<dt class="xlarge left">
		<label for="title"><?php echo lang('php_version')?> (<b><?php echo phpversion() ?></b>)</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($php_version) :	?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="xlarge left">
		<label for="title"><?php echo lang('mysql_support')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($mysql_support) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="xlarge left">
		<label for="title">Safe Mode Off</label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($safe_mode) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="xlarge left">
		<label for="title"><?php echo lang('file_uploads')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($file_uploads) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>

<dl class="list">
	<dt class="xlarge left">
		<label for="title"><?php echo lang('gd_lib')?></label>
	</dt>
	<dd>
		<img src="../themes/admin/images/icon_16_<?php if($gd_lib) :?>ok<?php else :?>delete<?php endif ;?>.png" />
	</dd>
</dl>



<h2><?php echo lang('title_folder_check') ?></h2>

<?php foreach($check_folders as $folder => $result) :?>

	<dl class="list">
		<dt class="xlarge left">
			<label for="title"><?php echo $folder ?></label>
		</dt>
		<dd>
			<img src="../themes/admin/images/icon_16_<?php if($result) :?>ok<?php else :?>delete<?php endif ;?>.png" />
		</dd>
	</dl>

<?php endforeach ;?>


<h2><?php echo lang('title_files_check') ?></h2>

<?php foreach($check_files as $file => $result) :?>

	<dl class="list">
		<dt class="xlarge left">
			<label for="title"><?php echo $file ?></label>
		</dt>
		<dd>
			<img src="../themes/admin/images/icon_16_<?php if($result) :?>ok<?php else :?>delete<?php endif ;?>.png" />
		</dd>
	</dl>

<?php endforeach ;?>




<?php if ($next) :?>
	<button type="button" class="button yes right" onclick="javascript:location.href='?step=database&lang=<?php echo $lang ?>';"><?php echo lang('button_next_step') ?></button>
<?php endif ;?>

<br/>


