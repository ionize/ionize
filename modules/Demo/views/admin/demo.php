<div id="maincolumn">

	<h2 class="main demo"><?php echo lang('module_demo_title'); ?></h2>

	<div class="subtitle">

		<!-- About this module -->
		<p class="lite">
			<?php echo lang('module_demo_about'); ?>
		</p>

	</div>

	<!-- Will contains the authors list -->
	<div id="moduleDemoAuthorsList"></div>
</div>

<script type="text/javascript">

	// Init the panel toolbox is mandatory
	ION.initModuleToolbox('demo','demo_toolbox');

	// Update the authors list
	ION.HTML(
		'module/demo/author/get_list',		// URL to the controller
		{}, 								// Data send by POST. Nothing
		{'update':'moduleDemoAuthorsList'}	// JS request options
	);

</script>
