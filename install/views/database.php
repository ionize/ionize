
<ul id="nav">

	<li><a class="done" 		href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
	<li><a class="active" 		href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
	<li><a class="inactive" 	href="?step=settings&lang=<?php echo $lang ?>"><?php echo lang('nav_settings') ?></a></a></li>
	<li><a class="inactive" 	href="?step=data&lang=<?php echo $lang ?>"><?php echo lang('nav_data') ?></a></a></li>
	<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>
	
</ul>


<!-- User message -->
<?php if(isset($message)) :?>

	<p class="<?php echo $message_type ?>"><?php echo $message ?></p>

<?php endif ;?> 


<?php echo lang('db_create_text') ?>

<h2><?php echo lang('title_database_settings') ?></h2>

<form method="post" action="?step=database&lang=<?php echo $lang ?>">

	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="db_driver" value="mysql" />

	<!-- Driver 
	<dl>
		<dt>
			<label for="db_driver"><?php echo lang('database_driver')?></label>
		</dt>
		<dd>
			<select name="db_driver" id="db_driver" class="inputselect">
				<option <?php if($db_driver == 'mysql') :?>selected="selected"<?php endif; ?> value="mysql">MySQL</option>
				<option <?php if($db_driver == 'mysqli') :?>selected="selected"<?php endif; ?> value="mysqli">MySQLi</option>
				<option <?php if($db_driver == 'mssql') :?>selected="selected"<?php endif; ?> value="mssql">MS SQL</option>
				<option <?php if($db_driver == 'postgre') :?>selected="selected"<?php endif; ?> value="postgre">Postgre</option>
				<option <?php if($db_driver == 'oci8') :?>selected="selected"<?php endif; ?> value="oci8">Oracle</option>
				<option <?php if($db_driver == 'sqlite') :?>selected="selected"<?php endif; ?> value="sqlite">SQLite</option>
				<option <?php if($db_driver == 'odbc') :?>selected="selected"<?php endif; ?> value="odbc">ODBC</option>
			</select>
		</dd>
	</dl>
	
	-->
	
	<dl>
		<dt>
			<label for="db_hostname"><?php echo lang('database_hostname')?></label>
		</dt>
		<dd>
			<input name="db_hostname" id="db_hostname" type="text" class="inputtext" value="<?php if($db_hostname != '') {echo $db_hostname;} else {echo 'localhost';} ?>"></input>
		</dd>
	</dl>
	
	<dl>
		<dt>
			<label for="db_name"><?php echo lang('database_name')?></label>
		</dt>
		<dd>
			<input name="db_name" id="db_name" type="text" class="inputtext" value="<?php echo $db_name ?>"></input>
		</dd>
	</dl>
	
	<dl>
		<dt>
			<label for="db_username"><?php echo lang('database_username')?></label>
		</dt>
		<dd>
			<input name="db_username" id="db_username" type="text" class="inputtext" value="<?php echo $db_username ?>"></input>
		</dd>
	</dl>
	
	<dl>
		<dt>
			<label for="db_password"><?php echo lang('database_password')?></label>
		</dt>
		<dd>
			<input name="db_password" id="db_password" type="text" class="inputtext" value=""></input>
		</dd>
	</dl>
	
	<br/>
	
	<input type="submit" class="button right yes" value="<?php echo lang('button_save_next_step') ?>" />
	
	<br/>

</form>
