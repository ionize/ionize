<div class="block">
	<ul id="nav">
		<li><a class="done" href="?step=checkconfig&lang=<?php echo $lang ?>"><?php echo lang('nav_check') ?></a></li>
		<li><a class="done" href="?step=database&lang=<?php echo $lang ?>"><?php echo lang('nav_db') ?></a></li>
		<li><a class="done" href="?step=user&lang=<?php echo $lang ?>"><?php echo lang('nav_settings') ?></a></a></li>
		<li><a class="done" href="?step=data&lang=<?php echo $lang ?>"><?php echo lang('nav_data') ?></a></a></li>
		<li><a class="done" href="?step=finish&lang=<?php echo $lang ?>"><?php echo lang('nav_end') ?></a></a></li>
	</ul>
</div>

<div class="block content">
	<h2><?php echo lang('title_finish') ?></h2>
	<p><?php echo lang('finish_text') ?></p>

	<div class="buttons">
		<button type="button" class="button yes right" onclick="javascript:document.location.href='?step=deleteinstaller&goto=site'"><?php echo lang('button_go_to_site') ?></button>
		<button type="button" class="button info left" onclick="javascript:location.href='?step=deleteinstaller&goto=admin'"><?php echo lang('button_go_to_admin') ?></button>
	</div>
</div>

<script type="text/javascript">
	function deleteInstallFolder() {
		document.location.href = '?step=deleteinstaller&lang=&lang=<?php echo $lang ?>';
	}
</script>