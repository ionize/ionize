<div class="block">
	<ul id="nav">
		<li><a class="done" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
		<li><a class="active" href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
		<li><a class="inactive"><?php echo lang('nav_settings') ?></a></a></li>
		<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>
	</ul>
</div>

<div class="block">

	<!-- From what version do we migrate ? -->
	<p><?php echo $database_migration_from ?></p>


	<!-- Pay attention, we will migrate the data ! -->
	<p><?php echo $database_migration_text ?></p>



	<form method="post" action="?step=migrate&lang=<?php echo $lang ?>">

		<input type="hidden" name="action" value="save" />

		<div class="buttons">
			<input type="submit" class="button yes right" value="<?php echo $button_label ?>" />
		</div>
	</form>
</div>

