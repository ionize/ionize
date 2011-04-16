<div id="maincolumn">

	<h2 class="main" style="background:url(<?= base_url() ?>modules/Demo/assets/images/icon_48_demo_module.png) no-repeat top left;">Demo module</h2>

	<!-- lang('module_demo_settings_title') : Language translated string
		 
		 These lang elements are stored in the folders : /modules/Demo/language/xx/demo_lang.php
		 
		 They can be used in the admin panel of the module but also on the client side, by tags, etc.
		 
		 Once the module is loaded, these files are also loaded
	-->
		
	<h3><?= lang('module_demo_settings_title')?></h3>
	
	<p><?= lang('module_demo_settings_text')?></p>
	
	
	<!-- This form is send through XHR.
		
		 The javascript MUI.setFormSubmit(), called at the bottom of this file, sends the form to the wished controller
	 -->
	<form id="configForm" name="configForm" method="post">
	
		<!-- True / False Module setting -->
		<dl>
			<dt><label for="module_demo_true_false"><?= lang('module_demo_setting_true_false') ?></label></dt>
			<dd>
				<input class="inputcheckbox" type="checkbox" name="module_demo_true_false" id="module_demo_true_false" <?php if (config_item('module_demo_true_false') == TRUE):?> checked="checked" <?php endif;?> value="1" />
			</dd>
		</dl>	
		
		<!-- String Module setting -->
		<dl>
			<dt><label for="module_demo_string"><?= lang('module_demo_setting_string') ?></label></dt>
			<dd>
				<input class="inputtext w240" type="text" name="module_demo_string" id="module_demo_string" value="<?= config_item('module_demo_string') ?>" />
			</dd>
		</dl>
		
		<!-- Submit button  -->
		<dl class="last">
			<dt>&#160;</dt>
			<dd>
				<input id="submit_config" type="submit" class="submit" value="<?= lang('ionize_button_save') ?>" />
			</dd>
		</dl>
		
	</form>

<!--	
	<h3><?= lang('module_demo_database_title')?></h3>
	

	<div class="right">
		<div class="toolbox divider">
			<input type="button" class="toolbar-button plus" id="addExtend" rel ="demo_module" value="<?= lang('ionize_label_add_field') ?>" />
		</div>
	</div>
	
	<p>You can manage the module table extend fields</p>


	<div id="extend_table"></div>


-->
	
	

</div>


<script type="text/javascript">

	/**
	 * Module Panel toolbox
	 * Mandatory, even it's empty !
	 * Initialize the toolbar buttons and remove the "save" button if no parameters is given
	 *
	 */
	MUI.initModuleToolbox('demo','demo_toolbox');
	// If no toolbox, remove the MUI.initModuleToolbox and uncomment this line
	// MUI.initModuleToolbox('demo','');
	
	
	/**
	 * Settings form send
	 * Sends the form data through XHR (Ajax) and reload (optional) the panel
	 *
	 * see mocha/init-ionize.js for JS method detail
	 *
	 */
	MUI.setFormSubmit(
		'configForm',					// ID of the form to send
		'submit_config',				// ID of the submit button to put the send action on
		'module/demo/demo/save_config' 	// URL of the controller which process data
	);
	
	/**
	 * Get table existing extends fields list
	 *
	 */
	MUI.updateElement({element:'extend_table', url:'extend_table/get_extend_fields_list/demo_module'});
	
	
	/**
	 * Action on button : Add extend field to module table
	 *
	 */
	$('addExtend').addEvent('click', function(e)
	{
		MUI.formWindow('extendtable', 'extendtableForm', 'ionize_title_extend_table_field', 'extend_table/add/' + this.getProperty('rel'), {width:400, height:330});
	});
		


	
	
	
	
</script>