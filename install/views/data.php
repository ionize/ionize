
<ul id="nav">

	<li><a class="done" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
	<li><a class="done" href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
	<li><a class="done" href="?step=user&lang=<?php echo $lang ?>"><?php echo lang('nav_settings') ?></a></a></li>
	<li><a class="active" href="?step=data&lang=<?php echo $lang ?>"><?php echo lang('nav_data') ?></a></a></li>
	<li><a class="inactive"><?php echo lang('nav_end') ?></a></a></li>
	
</ul>


<h2><?php echo lang('title_sample_data') ?></h2>


<form method="post" action="?step=data&lang=<?php echo $lang ?>">

	<input type="hidden" name="action" value="save" />

	<?php echo lang('data_install_intro') ?>

	<input type="submit" class="button right yes" value="<?php echo lang('button_install_test_data') ?>" />
	<input type="button" class="button left" onclick="javascript:location.href='<?php echo $base_url ?>install/?step=finish&lang=<?php echo $lang ?>';" value="<?php echo lang('button_skip_next_step') ?>" />

</form>

<br/><br/>

