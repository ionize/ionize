

<div class="toolbox divider">
	<a id="reloadDemoModulePanel" href="<?= admin_url() ?>module/demo/demo/index" rel="<?= config_item('module_name') ?>" class="icon refresh"></a>
</div>

<script type="text/javascript">

	/**
	 * Options show / hide button
	 *
	 */
//	MUI.initSideColumn();


	/**
	 * Refresh the Demo module's admin panel
	 *
	 */
	$('reloadDemoModulePanel').addEvent('click', function(e)
	{
		e.stop();
		
		MUI.updateContent({
			element: $('mainPanel'),
			title: this.getProperty('rel'),
			url : this.getProperty('href')
		});
	});


	/**
	 * Form action
	 * see mocha/init-forms.js for more information about this method
	 */
//	MochaUI.setFormSubmit('demoForm', 'demoFormSubmit', 'module/demo/demo/save');


</script>
