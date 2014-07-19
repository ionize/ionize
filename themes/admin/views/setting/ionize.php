
<!-- Main Column -->
<div id="maincolumn">

	<h2 class="main ionize" id="main-title"><?php echo lang('ionize_title_ionize_settings'); ?></h2>

	<!-- Subtitle -->
	<div class="subtitle">
		<p><?php echo lang('ionize_onchange_ionize_settings'); ?></p>
	</div>


	<!-- Tabs -->
	<div id="ionizeSettingsTab" class="mainTabs">
		<ul class="tab-menu">
			<li><a><?php echo lang('ionize_title_dashboard'); ?></a></li>
			<li><a><?php echo lang('ionize_title_backend_ui'); ?></a></li>
			<li><a><?php echo lang('ionize_title_visual_help'); ?></a></li>
			<li><a><?php echo lang('ionize_title_admin_panel_languages'); ?></a></li>
			<li><a><?php echo lang('ionize_title_admin_panel_datetime'); ?></a></li>
		</ul>
		<div class="clear"></div>
	</div>

	<div id="ionizeSettingsTabContent">

		<form name="ionizeSettingsForm" id="ionizeSettingsForm" method="post">

			<!-- Dashboard -->
			<div class="tabcontent p20">

				<!-- Shortcuts Block -->
				<dl>
					<dt>
						<label for="display_dashboard_shortcuts" title="<?php echo lang('ionize_help_display_dashboard_shortcuts'); ?>"><?php echo lang('ionize_label_display_dashboard_shortcuts'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_dashboard_shortcuts" id="display_dashboard_shortcuts" <?php if (Settings::get('display_dashboard_shortcuts') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<!-- Modules Block -->
				<dl>
					<dt>
						<label for="display_dashboard_modules" title="<?php echo lang('ionize_help_display_dashboard_modules'); ?>"><?php echo lang('ionize_label_display_dashboard_modules'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_dashboard_modules" id="display_dashboard_modules" <?php if (Settings::get('display_dashboard_modules') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<!-- Users Block -->
				<dl>
					<dt>
						<label for="display_dashboard_users" title="<?php echo lang('ionize_help_display_dashboard_users'); ?>"><?php echo lang('ionize_label_display_dashboard_users'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_dashboard_users" id="display_dashboard_users" <?php if (Settings::get('display_dashboard_users') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<!-- Content Block -->
				<dl>
					<dt>
						<label for="display_dashboard_content" title="<?php echo lang('ionize_help_display_dashboard_content'); ?>"><?php echo lang('ionize_label_display_dashboard_content'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_dashboard_content" id="display_dashboard_content" <?php if (Settings::get('display_dashboard_content') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<!-- Quick Settings Block -->
				<dl>
					<dt>
						<label for="display_dashboard_quick_settings" title="<?php echo lang('ionize_help_display_quick_settings'); ?>"><?php echo lang('ionize_label_display_quick_settings'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_dashboard_quick_settings" id="display_dashboard_quick_settings" <?php if (Settings::get('display_dashboard_quick_settings') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

			</div>

			<!-- Style -->
			<div class="tabcontent p20">

				<dl>
					<dt>
						<label for="backend_ui_style"><?php echo lang('ionize_label_backend_ui_style'); ?></label>
					</dt>
					<dd>
						<select class="select" name="backend_ui_style">
							<?php foreach($styles as $style): ?>
								<option value="<?php echo $style; ?>" <?php if($style == Settings::get('backend_ui_style') ): ?>selected="selected"<?php endif; ?>><?php echo ucfirst($style); ?></option>
							<?php endforeach ;?>
						</select>
					</dd>
				</dl>

			</div>

			<!-- Visual help : help tips and "Connected" label -->
			<div class="tabcontent p20">

				<dl>
					<dt>
						<label for="display_connected_label" title="<?php echo lang('ionize_help_display_connected_label'); ?>"><?php echo lang('ionize_label_display_connected_label'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_connected_label" id="display_connected_label" <?php if (Settings::get('display_connected_label') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<dl>
					<dt>
						<label for="enable_backend_tracker" title="<?php echo lang('ionize_help_enable_backend_tracker'); ?>"><?php echo lang('ionize_label_enable_backend_tracker'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="enable_backend_tracker" id="enable_backend_tracker" <?php if (Settings::get('enable_backend_tracker') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<dl>
					<dt>
						<label for="display_front_offline_content" title="<?php echo lang('ionize_help_display_front_offline_content'); ?>"><?php echo lang('ionize_label_display_front_offline_content'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="display_front_offline_content" id="display_front_offline_content" <?php if (Settings::get('display_front_offline_content') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<dl>
					<dt>
						<label for="notification" title="<?php echo lang('ionize_help_display_notification'); ?>"><?php echo lang('ionize_label_display_notification'); ?></label>
					</dt>
					<dd>
						<input class="inputcheckbox" type="checkbox" name="notification" id="notification" <?php if (Settings::get('notification') == '1'):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

			</div>

			<!-- Admin panel displayed languages -->
			<div class="tabcontent p10">

				<table class="list w280">
					<thead>
						<tr>
							<th></th>
							<th class="center">Lang</th>
							<th class="center">Displayed</th>
							<th class="center">Default</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach(Settings::get('admin_languages') as $lang) :?>
						<tr>
							<td class="center">
								<img src="<?php echo admin_style_url(); ?>images/world_flags/flag_<?php echo $lang; ?>.gif" alt="<?php echo $lang; ?>" class="mt2" />
							</td>
							<td class="center">
								<label for="display_lang_<?php echo $lang; ?>"><?php echo $lang; ?></label>
							</td>
							<td class="center">
								<input <?php if(in_array($lang, $displayed_admin_languages)) :?>checked="checked" <?php endif ;?>id="display_lang_<?php echo $lang; ?>" class="inputcheckbox" name="displayed_admin_languages[]" type="checkbox" value="<?php echo $lang; ?>" />
							</td>
							<td class="center">
								<input <?php if(Settings::get('default_admin_lang') == $lang) :?>checked="checked" <?php endif ;?>id="default_admin_lang_<?php echo $lang; ?>" class="inputcheckbox " name="default_admin_lang" type="radio" value="<?php echo $lang; ?>" />
							</td>
						</tr>
						<?php endforeach ;?>
					</tbody>
				</table>
			</div>

			<!-- Admin panel date and time -->
			<div class="tabcontent p20">

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
		</form>

	</div>


</div> <!-- /maincolumn -->


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
	new TabSwapper({tabsContainer: 'ionizeSettingsTab', sectionsContainer: 'ionizeSettingsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'ionizeSettingsTab' });

</script>