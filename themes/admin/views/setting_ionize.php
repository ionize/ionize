
<form name="ionizeSettingsForm" id="ionizeSettingsForm" method="post">


<!-- Main Column -->
<div id="maincolumn">

	<h2 class="main ionize" id="main-title"><?= lang('ionize_title_ionize_settings') ?></h2>

	<!-- Subtitle -->
	<div class="subtitle">
		<p><?= lang('ionize_onchange_ionize_settings')?></p>
	</div>

	<!-- Visual help : help tips and "Connected" label -->
	<h3 class="toggler1 mt20"><?=lang('ionize_title_visual_help')?></h3>
	
	<div class="element1">

		<dl>
			<dt>
				<label for="show_help_tips" title="<?= lang('ionize_help_help') ?>"><?=lang('ionize_label_show_help_tips')?></label>
			</dt>
			<dd>
				<input class="inputcheckbox" type="checkbox" name="show_help_tips" id="show_help_tips" <?php if (Settings::get('show_help_tips') == '1'):?> checked="checked" <?php endif;?> value="1" />
			</dd>
		</dl>

		<dl>
			<dt>
				<label for="display_connected_label" title="<?= lang('ionize_help_display_connected_label') ?>"><?=lang('ionize_label_display_connected_label')?></label>
			</dt>
			<dd>
				<input class="inputcheckbox" type="checkbox" name="display_connected_label" id="display_connected_label" <?php if (Settings::get('display_connected_label') == '1'):?> checked="checked" <?php endif;?> value="1" />
			</dd>
		</dl>

	</div>


	<!-- Admin panel displayed languages -->
	<h3 class="toggler1"><?=lang('ionize_title_admin_panel_languages')?></h3>
	
	<div class="element1">


		<?php foreach(Settings::get('admin_languages') as $lang) :?>
			<dl>
				<dt>
					<label title="<?= $lang ?>" for="display_lang_<?= $lang ?>"><img src="<?= theme_url() ?>images/world_flags/flag_<?= $lang ?>.gif" alt="<?= $lang ?>" /> </label>
				</dt>
				
				<dd>
					<input <?php if(in_array($lang, $displayed_admin_languages)) :?>checked="checked" <?php endif ;?>id="display_lang_<?= $lang ?>" class="inputcheckbox" name="displayed_admin_languages[]"  type="checkbox" value="<?= $lang ?>" />
				</dd>
			
			</dl>
		<?php endforeach ;?>
	
	</div>


	<!-- Admin panel date and time -->
	<h3 class="toggler1"><?=lang('ionize_title_admin_panel_datetime')?></h3>
	
	<div class="element1">

		<dl>
			<dt><label for="date_format_eu">dd.mm.yyyy</label></dt>
			<dd>
				<input <?php if(Settings::get('date_format') == '%d.%m.%Y') :?>checked="checked" <?php endif ;?>id="date_format_eu" class="inputcheckbox" name="date_format" type="radio" value="%d.%m.%Y" />
			</dd>
		</dl>
		<dl>
			<dt><label for="date_format_us">yyyy.mm.dd</label></dt>
			<dd>
				<input <?php if(Settings::get('date_format') == '%Y.%m.%d') :?>checked="checked" <?php endif ;?>id="date_format_us" class="inputcheckbox" name="date_format" type="radio" value="%Y.%m.%d" />
			</dd>
		</dl>
	
	</div>




</div> <!-- /maincolumn -->

</form>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('setting_ionize_toolbox');

	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler1', 'div.element1', false, 'ionizeSettingsAccordion');

	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
	ION.initLabelHelpLinks('#ionizeSettingsForm');



</script>