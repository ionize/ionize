<form name="fancyForm" id="fancyForm" action="<?= admin_url() ?>fancyupload/save" method="post">

<div id="sidecolumn">

	<!- Informations -->
	<div class="info">

		<dl class="compact">
			<dt class="small"><label><?=lang('ionize_label_file_uploads')?></label></dt>
			<dd><img src="<?= theme_url() ?>/images/icon_16_<?php if(ini_get('file_uploads') == true) :?>ok<?php else :?>nok<?php endif ;?>.png" /></dd>
		</dl>
		<dl class="compact">
			<dt class="small"><label><?=lang('ionize_label_max_upload_size')?></label></dt>
			<dd><?= ini_get('upload_max_filesize') ?></dd>
		</dl>

		<!-- Upload active ? -->
		<dl class="compact">
			<dt class="small"><label for="fancyupload_active" title="<?= lang('module_fancyupload_label_active') ?>"><?= lang('module_fancyupload_label_active') ?></label></dt>
			<dd>
				<input type="checkbox" id="fancyupload_active" name="fancyupload_active" value="1" <?php if (config_item('fancyupload_active') == '1'):?> checked="checked" <?php endif;?> />
			</dd>
		</dl>

	</div>

	<div id="options">
		
		<!-- Email option -->
		<h3 class="toggler"><?= lang('module_fancyupload_title_email') ?></h3>


		<dl>
			<dt class="small"><label for="fancyupload_send_alert"><?= lang('module_fancyupload_label_send_alert') ?></label></dt>
			<dd><input type="checkbox" id="fancyupload_send_alert" name="fancyupload_send_alert" value="1" <?php if (config_item('fancyupload_send_alert') == '1'):?> checked="checked" <?php endif;?> /></dd>
		</dl>


		<div id="mailSettings">
		
			<!--  Email to send the alert-->
			<dl>
				<dt class="small">
					<label for="fancyupload_email"><?=lang('ionize_label_email')?></label>
				</dt>
				<dd>
					<input id="fancyupload_email" name="fancyupload_email" class="inputtext" type="text" value="<?= config_item('fancyupload_email') ?>" />
				</dd>
			</dl>
					
		</div>		
<!-- Confirmation ti user : Not implemented yet
		<dl>
			<dt class="small"><label for="fancyupload_send_confirmation"><?= lang('module_fancyupload_label_send_confirmation') ?></label></dt>
			<dd><input type="checkbox" id="fancyupload_send_confirmation" name="fancyupload_send_confirmation" value="1" <?php if (config_item('fancyupload_send_confirmation') == '1'):?> checked="checked" <?php endif;?> /></dd>
		</dl>
-->
	</div>


</div>


<div id="maincolumn">


	<p><img src="<?= base_url() ?>modules/Fancyupload/assets/images/fancyupload.png" /></p>

	<p>
		Swiff meets Ajax for powerful and elegant uploads. 
		FancyUpload is a file-input replacement which features an unobtrusive, multiple-file selection menu and queued upload with an animated progress bar. 
		It is easy to setup, is server independent, completely styleable via CSS and XHTML and uses MooTools to work in all modern browsers. 
	</p>
	
	<p>Know more : <a href="http://digitarald.de/project/fancyupload/">http://digitarald.de/project/fancyupload/</a></p>

	<h3><?=lang('module_fancyupload_title_folder')?></h3>

	<!-- For the moment, the type is only "photoqueue", as we didn't got time to implement
		 the other kind of fancyupload
	-->
	<input type="hidden" name="fancyupload_type" value="photoqueue" />

	<!-- Type of Fancyupload : Photoqueue or Complete 
	<dl>
		<dt>
			<label for="fancyupload_type"><?=lang('module_fancyupload_label_type')?></label>
		</dt>
		<dd>
			<select name="fancyupload_type" id="fancyupload_type">
				<option <?php if (config_item('fancyupload_type') == 'attach-a-file'):?>selected="selected"<?php endif;?> value="attach-a-file">Attach a file</option>
				<option <?php if (config_item('fancyupload_type') == 'photoqueue'):?>selected="selected"<?php endif;?> value="photoqueue">Photoqueue</option>
				<option <?php if (config_item('fancyupload_type') == 'single-file-button'):?>selected="selected"<?php endif;?> value="single-file-button">Single file button</option>
			</select>
		</dd>
	</dl>
	-->

	<!-- Folder -->
	<dl>
		<dt><label for="fancyupload_folder" title="<?= lang('module_fancyupload_label_folder_help') ?>"><?= lang('module_fancyupload_label_folder') ?></label></dt>
		<dd>
			<?= $fancyupload_folder ?>
		</dd>
	</dl>

	<!-- Max Upload size in MB -->
	<dl>
		<dt><label for="fancyupload_max_upload" title="<?= lang('module_fancyupload_label_max_upload_help') ?>"><?= lang('module_fancyupload_label_max_upload') ?></label></dt>
		<dd>
			<input id="fancyupload_max_upload" name="fancyupload_max_upload" type="text" class="text w40" value="<?= config_item('fancyupload_max_upload') ?>" /> 
		</dd>
	</dl>

	<!-- Prefix to uploaded file ? -->
	<dl>
		<dt><label for="fancyupload_file_prefix" title="<?= lang('module_fancyupload_label_file_prefix_help') ?>"><?= lang('module_fancyupload_label_file_prefix') ?></label></dt>
		<dd>
			<input id="fancyupload_file_prefix" name="fancyupload_file_prefix" type="checkbox" class="checkbox" value="1" <?php if (config_item('fancyupload_file_prefix') == '1'):?> checked="checked" <?php endif;?>/> 
		</dd>
	</dl>

	<!-- Group -->
	<dl>
		<dt>
			<label for="fancyupload_group"  title="<?= lang('module_fancyupload_label_group_help') ?>"><?=lang('module_fancyupload_label_group')?></label>
		</dt>
		<dd>
			<select name="fancyupload_group">
				<?php foreach($groups as $group) :?>
					
					<?php if($group['level'] > 0) :?>
					
						<option value="<?= $group['id_group'] ?>" <?php if(config_item('fancyupload_group') == $group['id_group']) :?> selected="selected" <?php endif ;?> ><?= $group['group_name'] ?></option>
						
					<?php endif ;?>
				
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	

</div> <!-- /maincolumn -->
</form>

<script type="text/javascript">
	

	/**
	 * Module Panel toolbox
	 * Mandatory, even it's empty !
	 * Initialize the toolbar buttons and remove the "save" button if no parameters is given
	 *
	 */
	MUI.initModuleToolbox('fancyupload','fancyupload_toolbox');


	/**
	 * Init help tips on label
	 *
	 */
	MUI.initLabelHelpLinks('#fancyForm');	


	/**
	 * SMTP form action
	 * see mocha/init-forms.js for more information about this method
	 */
	// MochaUI.setFormSubmit('fancyForm', 'settingsFormSubmit', 'admin/module/fancyupload/fancyupload/save');


	/**
	 * Show / hides Email depending the alert email is activated
	 *
	 */
	$('fancyupload_send_alert').addEvent('change', function(){
		alertEmailStatus();
	});
	
	alertEmailStatus = function()
	{
		if ($('fancyupload_send_alert').getProperty('checked') == true)
		{
			$('mailSettings').setStyle('display', 'block');
		}
		else
		{
			$('mailSettings').setStyle('display', 'none');
		}
	}
	alertEmailStatus();
	
</script>

