
<ul id="nav">

	<li><a class="done" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
	<li><a class="done" href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
	<li><a class="active" href="?step=settings&lang=<?php echo $lang ?>"><?php echo lang('nav_settings') ?></a></a></li>
	<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>
	
</ul>



<!-- User message -->
<?php if(isset($message)) :?>

	<p class="<?php echo $message_type ?>"><?php echo $message ?></p>

<?php endif ;?>


<form method="post" action="?step=settings&lang=<?php echo $lang ?>">
	
	<input type="hidden" name="action" value="save" />

	<h2><?php echo lang('settings_default_lang_title') ?></h2>
	<p><?php echo lang('settings_default_lang_text') ?></p>

	<!-- Default lang code -->
	<dl>
		<dt>
			<label for="lang_code" class="required"><?php echo lang('lang_code')?></label>
		</dt>
		<dd>
			<input name="lang_code" id="lang_code" type="text" class="inputtext w40" value="<?php echo $lang_code ?>"></input>
		</dd>
	</dl>

	<!-- Default lang name -->
	<dl>
		<dt>
			<label for="lang_name"><?php echo lang('lang_name')?></label>
		</dt>
		<dd>
			<input name="lang_name" id="lang_name" type="text" class="inputtext w120" value="<?php echo $lang_name ?>"></input>
		</dd>
	</dl>


	<!-- Admin URL -->
	<h2><?php echo lang('settings_admin_url_title') ?></h2>
	<p><?php echo lang('settings_admin_url_text') ?></p>

	<dl>
		<dt style="width:200px;">
			<label for="admin_url"><?= BASEURL ?></label>
		</dt>
		<dd>
			<input name="admin_url" id="admin_url" type="text" class="inputtext w120" value="<?php echo $admin_url ?>"></input>
		</dd>
	</dl>


	<input type="submit" class="button yes right" value="<?php echo lang('button_save_next_step') ?>" />

</form>

