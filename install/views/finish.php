
<ul id="nav">

	<li><a class="done" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
	<li><a class="done" href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
	<li><a class="done" href="?step=user&lang=<?php echo $lang ?>"><?php echo lang('nav_settings') ?></a></a></li>
	<li><a class="done" href="?step=data&lang=<?php echo $lang ?>"><?php echo lang('nav_data') ?></a></a></li>
	<li><a class="done" href="?step=finish&lang=<?php echo $lang ?>"><?php echo lang('nav_end') ?></a></a></li>
	
</ul>


<h1><?php echo lang('title_finish') ?></h1>

<br/>

<p class="error"><?php echo lang('finish_text') ?></p>

<br/>

<button type="button" class="button yes right" onclick="javascript:location.href='<?php echo $base_url ?>';"><?php echo lang('button_go_to_site') ?></button>
<button type="button" class="button right" onclick="javascript:location.href='<?php echo $base_url.config_item('admin_url') ?>';"><?php echo lang('button_go_to_admin') ?></button>

