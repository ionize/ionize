<?php
/**
 * Main panel Deny view
 * Called by : MY_Admin()->authority_protect(<resource>, 'authority/deny/main');
 *
 */
?>

<div id="maincolumn">
	<h2 class="main protected"><?php echo lang('ionize_title_resource_protected'); ?></h2>
	<div class="main subtitle">
		<p>
			<?php echo lang('ionize_subtitle_resource_protected'); ?>
		</p>
	</div>
</div>

<script type="text/javascript">

	ION.initToolbox('empty_toolbox');

	if ($('splitPanel_sideColumn'))
		$('splitPanel_sideColumn').close();

</script>