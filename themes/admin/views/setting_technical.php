
<!-- Main Column -->
<div id="maincolumn">

	<h2 class="main settings" id="main-title"><?= lang('ionize_title_technical_settings') ?></h2>
	
	<!-- Subtitle -->
	<div class="subtitle">
	</div>


	<!-- Tabs -->
	<div id="settingsTab" class="mainTabs mt30">
		
		<ul class="tab-menu">
			
			<li id="media_settings"><a><?= lang('ionize_title_media_management') ?></a></li>
			<li id="article_settings"><a><?= lang('ionize_title_article_management') ?></a></li>
			<li id="database_settings"><a><?= lang('ionize_title_database') ?></a></li>
			<li id="email_settings"><a><?= lang('ionize_title_mail_send') ?></a></li>
			<li id="system_settings"><a><?= lang('ionize_title_system') ?></a></li>

		</ul>

		<div class="clear"></div>
	
	</div>


	<div id="settingsTabContent">
	
		<!-- Media management -->
		<div class="tabcontent">
			
			<form name="settingsMediasForm" id="settingsMediasForm" method="post">

				<p class="h30"><input id="settingsMediasFormSubmit" type="button" class="submit right" value="<?= lang('ionize_button_save_settings') ?>" /></p>

				<div class="tabsidecolumn">
					
					<h3><?=lang('ionize_title_media_management')?></h3>
				
					<dl class="small">
						<dt>
							<label for="files_path" title="<?=lang('ionize_help_setting_files_path')?>"><?=lang('ionize_label_files_path')?></label>
						</dt>
						<dd>
							<input name="files_path" id="files_path" class="inputtext" type="text" value="<?= Settings::get('files_path') ?>"/>
						</dd>
					</dl>
			
					<dl class="small">
						<dt>
							<label for="picture_max_width" title="<?=lang('ionize_help_setting_picture_max_width')?>"><?=lang('ionize_label_setting_picture_max_width')?></label>
						</dt>
						<dd>
							<input name="picture_max_width" id="picture_max_width" class="inputtext w40" type="text" value="<?= Settings::get('picture_max_width') ?>"/>
						</dd>
					</dl>
					
					<dl class="small">
						<dt>
							<label for="picture_max_height" title="<?=lang('ionize_help_setting_picture_max_height')?>"><?=lang('ionize_label_setting_picture_max_height')?></label>
						</dt>
						<dd>
							<input name="picture_max_height" id="picture_max_height" class="inputtext w40" type="text" value="<?= Settings::get('picture_max_height') ?>"/>
						</dd>
					</dl>
		
					<dl class="small">
						<dt>
							<label for="media_thumb_size" title="<?=lang('ionize_help_media_thumb_size')?>"><?=lang('ionize_label_media_thumb_size')?></label>
						</dt>
						<dd>
							<input name="media_thumb_size" id="media_thumb_size" class="inputtext w40" type="text" value="<?= Settings::get('media_thumb_size') ?>"/>
						</dd>
					</dl>
					
					<dl class="small mt20">
						<dt>
							<label title="<?=lang('ionize_help_media_upload_mode')?>"><?=lang('ionize_label_media_upload_mode')?></label>
						</dt>
						<dd>
							<input type="radio" name="media_upload_mode" id="media_upload_mode1" value="single" <?php if(Settings::get('media_upload_mode') == 'single'):?>checked="checked"<?php endif;?> /><label for="media_upload_mode1"><?=lang('ionize_label_media_upload_mode_single')?></label><br/>
							<input type="radio" name="media_upload_mode" id="media_upload_mode2" value="multiple" <?php if(Settings::get('media_upload_mode') == 'multiple'):?>checked="checked"<?php endif;?>/><label for="media_upload_mode2"><?=lang('ionize_label_media_upload_mode_multiple')?></label>
						</dd>
					</dl>
				</div>
				
				<!-- Allowed Mimes -->
				<div class="tabcolumn">
					
					<h3><?=lang('ionize_title_allowed_mimes')?></h3>
					<p class="mb15"><?= lang('ionize_text_allowed_mimes') ?></p>
	
					<?php
						$filemanager_file_types = explode(',',Settings::get('filemanager_file_types'));
					?>
		
					<?php foreach($mimes as $type => $mime_list) :?>
					
						<h3 class="toggler1"><?= $type ?></h3>
					
						<div class="element1">
		
							<table class="list w340">
								<thead>
									<tr>
										<th class="right"></th>
										<th>Mime</th>
										<th class="center">Allowed ?</th>
									</tr>
								</thead>
								<tbody>
			
									<?php foreach($mime_list as $ext => $mime) :?>
										<tr>
											<td class="right pr10"><?= $ext ?> </td>
											<td>
												<label for="allowed_type_<?= $ext ?>" class="m0"><?= $mime ?></label>
											</td>
											<td class="center">
												<input <?php if(in_array($ext, $filemanager_file_types)) :?>checked="checked" <?php endif ;?>id="allowed_type_<?= $ext ?>" class="inputcheckbox" name="allowed_type[]" type="checkbox" value="<?= $ext ?>" />
											</td>
										</tr>
									<?php endforeach ;?>
			
								</tbody>
							</table>
						
						</div>
					
					<?php endforeach ;?>

					<h3><?=lang('ionize_title_no_source_picture')?></h3>
					<p class="mb15"><?= lang('ionize_text_no_source_picture') ?></p>

					<!-- Thumb name -->
					<dl class="small">
						<dt>
							<label for="no_source_picture"><?=lang('ionize_label_no_source_picture')?></label>
						</dt>
						<dd>
							<input id="no_source_picture" name="no_source_picture" type="text" class="inputtext" value="<?php echo Settings::get('no_source_picture'); ?>" />
						</dd>
					</dl>


				</div>
			
			</form>			

		</div>		
		
		<!-- Article management -->
		<div class="tabcontent">
			
			<form name="articleSettingsForm" id="articleSettingsForm" method="post">
			
				<p class="h30"><input id="articleSettingsFormSubmit" type="button" class="submit right" value="<?= lang('ionize_button_save_settings') ?>" /></p>
				
				<dl>
					<dt>
						&nbsp;
					</dt>
					<dd>
						<p class="lite"><?= lang('ionize_onchange_ionize_settings')?></p>
					</dd>
				</dl>
				<dl class="mb20">
					<!-- TinyMCE Block Format (Select) -->
					<dt>
						<label for="tinyblockformats" title="<?=lang('ionize_help_tinyblockformats')?>"><?=lang('ionize_label_tinyblockformats')?></label>
					</dt>
					<dd>
						<input class="inputtext w360 mb5" id="tinyblockformats" name="tinyblockformats" type="text" value="<?= Settings::get('tinyblockformats') ?>"/><br />
						<a id="texteditor_default_tinyblockformats"><?=lang('ionize_label_restore_tinyblockformats')?></a>
					</dd>
				</dl>
	
				<dl class="mb20">
					<!-- TinyMCE toolbar buttons -->
					<dt>
						<label title="<?=lang('ionize_help_tinybuttons')?>"><?=lang('ionize_label_tinybuttons')?></label>
					</dt>
					<dd>
						1 <input class="inputtext w360 mb5" id="tinybuttons1" name="tinybuttons1" type="text" value="<?= Settings::get('tinybuttons1') ?>"/><br />
						2 <input class="inputtext w360 mb5" id="tinybuttons2" name="tinybuttons2" type="text" value="<?= Settings::get('tinybuttons2') ?>"/><br />
						3 <input class="inputtext w360" id="tinybuttons3" name="tinybuttons3" type="text" value="<?= Settings::get('tinybuttons3') ?>"/><br />
						<a id="texteditor_default"><?=lang('ionize_label_restore_tinybuttons')?></a> | <a target="_blank" href="http://www.tinymce.com/wiki.php/Buttons/controls"><?=lang('ionize_label_help_tinybuttons')?></a>
					</dd>
					
				</dl>
				
	
				<dl class="last mb20">
					<!-- TinyMCE Block Format (Select) -->
					<dt>
						<label title="<?=lang('ionize_help_article_allowed_tags')?>"><?=lang('ionize_label_article_allowed_tags')?></label>
					</dt>
					<dd>
	
						<?php
							$tags = array(
								'tag1' => array('h1','h2','h3','h4','h5','h6','em','img','audio','video'),
								'tag2' => array('iframe','div','span','table','object','form','dl','pre','code','legend'),
								'tag3' => array('dfn','samp','kbd','var','cite','mark','q','hr','big','small'),
								'tag4' => array('link','address','abbr','sub','sup','ins','blockquote','bdi','bdo'),
							);
						?>
	
						<table class="w240 mt0">
							<tbody>
								<tr>
									
									<?php foreach($tags as $key => $tag_array) :?>
								
									<td>
										<table class="list w80 mt0 mr20">
											<tbody>
												<?php foreach($tag_array as $tag) :?>
													<tr><td class="pr10"><label for="tag_<?=$tag?>"><?=$tag?></label></td><td class="center"><input id="tag_<?=$tag?>" name="article_allowed_tags[]" <?php if(in_array($tag, $article_allowed_tags)) :?>checked="checked" <?php endif;?>type="checkbox" value="<?=$tag?>"/></td></tr>
												<?php endforeach ?>
											</tbody>
										</table>
									</td>
									
									<?php endforeach; ?>
									
								</tr>
							</tbody>
						</table>
					
					</dd>
				</dl>
			</form>
		</div>		


		<!-- Database -->
		<div class="tabcontent pt10">

			<form name="databaseForm" id="databaseForm" method="post" action="<?= admin_url() ?>setting/save_database">

				<p class="h30"><input id="submit_database" type="button" class="submit right" value="<?= lang('ionize_button_save_settings') ?>" /></p>

				<dl>
					<dt>
						&nbsp;
					</dt>
					<dd>
						<p class="lite"><?= lang('ionize_onchange_ionize_settings')?></p>
					</dd>
				</dl>

				<!-- Driver -->
				<dl>
					<dt>
						<label for="db_driver"><?=lang('ionize_label_db_driver')?></label>
					</dt>
					<dd>
						<select name="db_driver" id="db_driver" class="select">
							<option <?php if ($this->db->platform() == 'mysql'):?>selected="selected"<?php endif;?>  value="mysql">MySQL</option>
							<option <?php if ($this->db->platform() == 'mysqli'):?>selected="selected"<?php endif;?>  value="mysqli">MySQLi</option>
							<option <?php if ($this->db->platform() == 'mssql'):?>selected="selected"<?php endif;?>  value="mssql">MS SQL</option>
							<option <?php if ($this->db->platform() == 'postgre'):?>selected="selected"<?php endif;?>  value="postgre">Postgre SQL</option>
							<option <?php if ($this->db->platform() == 'oci8'):?>selected="selected"<?php endif;?>  value="oci8">Oracle</option>
							<option <?php if ($this->db->platform() == 'sqlite'):?>selected="selected"<?php endif;?>  value="sqlite">SQLite</option>
							<option <?php if ($this->db->platform() == 'odbc'):?>selected="selected"<?php endif;?>  value="odbc">ODBC</option>
						</select>
					</dd>
				</dl>
				
				<!-- Host -->
				<dl>
					<dt>
						<label for="db_host"><?=lang('ionize_label_db_host')?></label>
					</dt>
					<dd>
						<input id="db_host" name="db_host" class="inputtext w140" type="text" value="<?= $db_host ?>" />
					</dd>
				</dl>

				<!-- Database -->
				<dl>
					<dt>
						<label for="db_name"><?=lang('ionize_label_db_name')?></label>
					</dt>
					<dd>
						<input id="db_name" name="db_name" class="inputtext w140" type="text" value="<?= $db_name ?>" />
					</dd>
				</dl>

				<!-- User -->
				<dl>
					<dt>
						<label for="db_user"><?=lang('ionize_label_db_user')?></label>
					</dt>
					<dd>
						<input id="db_user" name="db_user" class="inputtext w140" type="text" value="<?= $db_user ?>" />
					</dd>
				</dl>

				<!-- Password -->
				<dl>
					<dt>
						<label for="db_pass"><?=lang('ionize_label_db_pass')?></label>
					</dt>
					<dd>
						<input id="db_pass" name="db_pass" class="inputtext w140" type="password" value="" />
					</dd>
				</dl>

			</form>

		</div>
		
		
		<!-- Email -->
		<div class="tabcontent pt10">
		
			<form name="smtpForm" id="smtpForm" method="post" action="<?= admin_url() ?>setting/save_smtp">
			
				<p class="h30"><input id="submit_smtp" type="button" class="submit right" value="<?= lang('ionize_button_save_settings') ?>" /></p>


				<!-- Website email -->
				<dl>
					<dt>
						<label for="site_email"><?=lang('ionize_label_site_email')?></label>
					</dt>
					<dd>
						<input id="site_email" name="site_email" class="inputtext w140" type="text" value="<?= Settings::get('site_email') ?>" />
					</dd>
				</dl>

				<!-- Mail path -->
				<dl>
					<dt>
						<label for="protocol"><?=lang('ionize_label_smtp_protocol')?></label>
					</dt>
					<dd>
						<select name="protocol" id="protocol" onchange="javascript:changeEmailDetails();" class="select">
							<option <?php if ($protocol == 'smtp'):?>selected="selected"<?php endif;?> value="smtp">SMTP</option>
							<option <?php if ($protocol == 'mail'):?>selected="selected"<?php endif;?> value="mail">Mail</option>
							<option <?php if ($protocol == 'sendmail'):?>selected="selected"<?php endif;?>  value="sendmail">SendMail</option>
						</select>
					</dd>
				</dl>
				

				<!-- Mail Path -->
				<div id="emailMailDetails" style="display:none;">
					<dl>
						<dt>
							<label for="mailpath"><?=lang('ionize_label_mailpath')?></label>
						</dt>
						<dd>
							<input id="mailpath" name="mailpath" type="text" class="inputtext w140" value="<?= $mailpath ?>" />
						</dd>
					</dl>
				</div>
				
				<div id="emailSMTPDetails">
					<!-- SMTP Host -->
					<dl>
						<dt>
							<label for="smtp_host"><?=lang('ionize_label_smtp_host')?></label>
						</dt>
						<dd>
							<input id="smtp_host" name="smtp_host" type="text" class="inputtext w140" value="<?= $smtp_host ?>" />
						</dd>
					</dl>
					
					<!-- SMTP User -->
					<dl>
						<dt>
							<label for="smtp_user"><?=lang('ionize_label_smtp_user')?></label>
						</dt>
						<dd>
							<input id="smtp_user" name="smtp_user" type="text" class="inputtext w140" value="<?= $smtp_user ?>" />
						</dd>
					</dl>
				
					<!-- SMTP Pass -->
					<dl>
						<dt>
							<label for="smtp_pass"><?=lang('ionize_label_smtp_pass')?></label>
						</dt>
						<dd>
							<input id="smtp_pass" name="smtp_pass" type="password" class="inputtext w140" value="<?= $smtp_pass ?>" />
						</dd>
					</dl>
				
					<!-- SMTP Port -->
					<dl>
						<dt>
							<label for="smtp_port"><?=lang('ionize_label_smtp_port')?></label>
						</dt>
						<dd>
							<input id="smtp_port" name="smtp_port" type="text" class="inputtext w40" value="<?= $smtp_port ?>" />
						</dd>
					</dl>
				</div>
					
				<!-- Charset -->
				<dl>
					<dt>
						<label for="charset"><?=lang('ionize_label_email_charset')?></label>
					</dt>
					<dd>
						<input id="charset" name="charset" type="text" class="inputtext w140" value="<?= $charset ?>" />
					</dd>
				</dl>
			
				<!-- Mailtype -->
				<dl>
					<dt>
						<label for="mailtype"><?=lang('ionize_label_email_mailtype')?></label>
					</dt>
					<dd>
						<select name="mailtype" id="mailtype" class="select">
							<option <?php if ($mailtype == 'text'):?>selected="selected"<?php endif;?> value="text">Text</option>
							<option <?php if ($mailtype == 'html'):?>selected="selected"<?php endif;?> value="html">HTML</option>
						</select>
					</dd>
				</dl>
			
			</form>
		</div>
		
		
		<!-- System -->
		<div class="tabcontent pt20">
		
		
			<div class="tabsidecolumn">
				
				<h3><?=lang('ionize_title_informations')?></h3>

				<dl class="small compact">
					<dt><label><?=lang('ionize_title_php_version')?></label></dt>
					<dd><?= phpversion() ?></dd>
				</dl>
				<dl class="small compact">
					<dt><label><?=lang('ionize_title_db_version')?></label></dt>
					<dd><?=$this->db->platform().' '.$this->db->version();?></dd>
				</dl>
				<dl class="small compact">
					<dt><label><?=lang('ionize_label_file_uploads')?></label></dt>
					<dd><img src="<?= theme_url() ?>images/icon_16_<?php if(ini_get('file_uploads') == true) :?>ok<?php else :?>nok<?php endif ;?>.png" /></dd>
				</dl>
				<dl class="small compact">
					<dt><label><?=lang('ionize_label_max_upload_size')?></label></dt>
					<dd><?= ini_get('upload_max_filesize') ?></dd>
				</dl>
				<dl class="small compact">
					<dt>&nbsp;</dt>
					<dd><a href="<?=base_url() . config_item('admin_url')?>/desktop/get/phpinfo" target="_blank">Complete PHP Info</a></dd>
				</dl>
			</div>
			
			<div class="tabcolumn">
		
		
				<h3 class="toggler"><?= lang('ionize_title_encryption_key') ?></h3>
		
				<div class="element">

					<form name="keysSettingsForm" id="keysSettingsForm" method="post">

						<!-- Form antispam key -->
						<dl class="mb10">
							<dt>
								<label for="form_antispam_key"><?=lang('ionize_label_antispam_key')?></label>
							</dt>
							<dd>
								<input id="form_antispam_key" name="form_antispam_key" type="text" class="inputtext w300 left" value="<?= $form_antispam_key ?>" />
								<a class="icon left refresh ml5" id="antispamRefresh" title="<?= lang('ionize_label_refresh_antispam_key')?>"></a>
							</dd>
						</dl>
						
						<!-- Encryption key -->
						<dl class="mb10">
							<dt>
								<label for="form_antispam_key"><?=lang('ionize_title_encryption_key')?></label>
							</dt>
							<dd>
								<textarea disabled="disabled" class="w300"><?= config_item('encryption_key') ?></textarea>
							</dd>
						</dl>
						
						<dl class="mb10">
							<dt>&nbsp;</dt>
							<dd>
								<input id="keysSettingsFormSubmit" type="submit" class="submit" value="<?= lang('ionize_button_save') ?>" />
							</dd>
						</dl>
					</form>
					
				</div>
				
				
				<!-- Cache -->
				<h3 class="toggler"><?= lang('ionize_title_cache') ?></h3>
	
				<div class="element">
					<form name="cacheForm" id="cacheForm" method="post" action="<?= admin_url() ?>setting/save_cache">
									
						<!-- Cache Time -->
						<dl>
							<dt>
								<label for="cache_expiration"  title="<?=lang('ionize_help_cache_expiration')?>"><?=lang('ionize_label_cache_expiration')?></label>
							</dt>
							<dd>
								<input id="cache_expiration" name="cache_expiration" class="inputtext w60" type="text" value="<?= config_item('cache_expiration') ?>" />
								<input id="submit_cache" type="submit" class="submit m0" value="<?= lang('ionize_button_save') ?>" />
							</dd>
						</dl>
						
						<!-- Empty cache  -->
						<dl class="mb10">
							<dt>
								<label for="clear_cache"  title="<?=lang('ionize_help_clear_cache')?>"><?=lang('ionize_label_clear_cache')?></label>
							</dt>
							<dd>
								<input id="clear_cache" type="button" class="button m0" value="<?= lang('ionize_button_clear_cache') ?>" />
							</dd>
						</dl>
		
					</form>
				</div>
				
				
				<!-- Admin URL -->
				<h3 class="toggler"><?= lang('ionize_title_admin_url') ?></h3>
				
				<div class="element">
				
					<form name="adminUrlForm" id="adminUrlForm" method="post" action="<?= admin_url() ?>setting/save_admin_url">
		
						<dl>
							<dt>
								<label for="admin_url"><?= lang('ionize_title_admin_url') ?></label>
							</dt>
							<dd>
								<input id="admin_url" name="admin_url" class="inputtext w120" value="<?=config_item('admin_url')?>" /><br/>
								<p class="lite pl10"><?= lang('ionize_onchange_ionize_settings')?></p>
							</dd>
						</dl>
			
						<dl class="mb10">
							<dt>&nbsp;</dt>
							<dd>
								<input id="submit_admin_url" type="submit" class="submit" value="<?= lang('ionize_button_save') ?>" />
							</dd>
						</dl>
		
					</form>
					
				</div>
				
				
				<!-- Maintenance Mode -->
				<h3 class="toggler"><?= lang('ionize_title_maintenance') ?></h3>
				
				<div class="element">
					
					<form name="maintenanceForm" id="maintenanceForm" method="post" action="<?= admin_url() ?>setting/save_maintenance" class="mb20">
		
						<!-- Maintenance ? -->
						<dl>
							<dt>
								<label for="maintenance" title="<?=lang('ionize_label_maintenance_help')?>"><?=lang('ionize_label_maintenance')?></label>
							</dt>
							<dd>
								<input class="inputcheckbox" <?php if (config_item('maintenance') == '1'):?>checked="checked"<?php endif;?> type="checkbox" name="maintenance" id="maintenance" value="1" />
							</dd>
						</dl>
						
						<!-- Maintenance IP restrict -->
						<dl>
							<dt>
								<label for="maintenance_ips" title="<?=lang('ionize_label_maintenance_ips_help')?>"><?=lang('ionize_label_maintenance_ips')?></label>
							</dt>
							<dd>
								<span><?= lang('ionize_label_your_ip') ?> : <?= $_SERVER['REMOTE_ADDR'] ?></span><br/>
								<textarea name="maintenance_ips" id="maintenance_ips" class="h50 w140"><?= (! empty($maintenance_ips)) ? $maintenance_ips : $_SERVER['REMOTE_ADDR'] ?></textarea>
							</dd>
						</dl>
		
						<!-- Maintenance page -->
						<?php if (function_exists('curl_init')) : ?>
							
							<dl>
								<dt>
									<label title="<?=lang('ionize_label_maintenance_page_help')?>"><?=lang('ionize_title_maintenance_page')?></label>
								</dt>
								<dd>
									<div id="maintenancePageContainer"></div>
								</dd>
							</dl>
							
						<?php endif ;?>
						
						<!-- Submit button  -->
						<dl class="mt10">
							<dt>&#160;</dt>
							<dd>
								<input id="submit_maintenance" type="submit" class="submit" value="<?= lang('ionize_button_save') ?>" />
							</dd>
						</dl>
		
						
					</form>

				</div>
			</div>		
		</div>		
	</div>
</div> <!-- /maincolumn -->


<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('empty_toolbox');


	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler', 'div.element', true, 'settingsAccordion1');
	ION.initAccordion('.toggler1', 'div.element1', false, 'settingsAccordion2');

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#settingsForm');
	ION.initLabelHelpLinks('#cacheForm');
	ION.initLabelHelpLinks('#maintenanceForm');


	var settingsTab = new TabSwapper({tabsContainer: 'settingsTab', sectionsContainer: 'settingsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'settingsTab' });


	/**
	 * Forms actions
	 * see ionize-form.js for more information about this method
	 */
	ION.setFormSubmit('databaseForm', 'submit_database', 'setting/save_database/true', 'mainPanel', 'setting/technical');
	ION.setFormSubmit('smtpForm', 'submit_smtp', 'setting/save_smtp/true', 'mainPanel', 'setting/technical');
	ION.setFormSubmit('cacheForm', 'submit_cache', 'setting/save_cache', 'mainPanel', 'setting/technical');
	ION.setFormSubmit('maintenanceForm', 'submit_maintenance', 'setting/save_maintenance', 'mainPanel', 'setting/technical');
	ION.setFormSubmit('settingsMediasForm', 'settingsMediasFormSubmit', 'setting/save_medias');
	ION.setFormSubmit('articleSettingsForm', 'articleSettingsFormSubmit', 'setting/save_article');
	ION.setFormSubmit('keysSettingsForm', 'keysSettingsFormSubmit', 'setting/save_keys');

	ION.initRequestEvent($('clear_cache'), 'setting/clear_cache');


	/**
	 * Admin URL form action
	 * see ionize-form.js for more information about this method
	 */
	ION.addConfirmation(
		'changeAdminUrl', 
		'submit_admin_url',
		function()
		{
			ION.sendData('setting/save_admin_url', $('adminUrlForm'))
		},
		Lang.get('ionize_confirm_change_admin_url')
	);
	


	$('antispamRefresh').addEvent('click', function(e)
	{
		e.stop();
		var key = ION.generateKey(32);
		$('form_antispam_key').value = key;
		$('form_antispam_key').highlight();
	});


	/**
	 * Restore tinyButtons toolbar to default config
	 *
	 */
	$('texteditor_default').addEvent('click', function()
	{
		$('tinybuttons1').value = 'pdw_toggle,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,bullist,numlist,|,link,unlink,image';
		$('tinybuttons2').value = 'fullscreen, undo,redo,|,pastetext,selectall,removeformat,|,media,charmap,hr,blockquote,nonbreaking,|,template,|,codemirror';
		$('tinybuttons3').value = 'tablecontrols';
	
	});

	$('texteditor_default_tinyblockformats').addEvent('click', function()
	{
		$('tinyblockformats').value = 'p,h2,h3,h4,h5,pre';
	});


	/**
	 * Show / hides Email details depending on the selected protocol
	 *
	 */
	changeEmailDetails = function()
	{
		if ($('protocol').value == 'mail')
		{
			$('emailSMTPDetails').setStyle('display', 'none');
			$('emailMailDetails').setStyle('display', 'block');
		}
		else
		{
			$('emailSMTPDetails').setStyle('display', 'block');
			$('emailMailDetails').setStyle('display', 'none');		
		}
	}
	changeEmailDetails();

	
	
	
	/**
	 * Make each tree page draggable to the maintenance page container
	 *
	 */
	if ($('maintenancePageContainer'))
	{
		// Get the maintenance page
		ION.HTML(admin_url + 'setting/get_maintenance_page', {}, {'update': 'maintenancePageContainer'});
		
		
		// Callbak when page is dropped
		setMaintenancePage = function(element, droppable, event)
		{
			ION.HTML(admin_url + 'setting/set_maintenance_page', {'id_page': element.getProperty('rel')}, {'update': 'maintenancePageContainer'});
		}
		
		// Make tree pages draggable
		$$('.treeContainer .page a.title').each(function(item, idx)
		{
			ION.addDragDrop(item, '.dropPageAsMaintenancePage', 'setMaintenancePage');
		});	
		
		// Add the get event, so events are added when pages are retrieved (click on plus)
		$$('.treeContainer').each(function(tree, idx)
		{
			tree.retrieve('tree').addEvent('get', function()
			{
				$$('.treeContainer .page a.title').each(function(item, idx)
				{
					ION.addDragDrop(item, '.dropPageAsMaintenancePage', 'setMaintenancePage');
				});	
			});
		});	
	}


	/**
	 * Views form
	 * see ionize-form.js for more information about this method
	 */
	// ION.setFormSubmit('settingsForm', 'settingsFormSubmit', 'setting/save_technical');

	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('settingsFormSubmit');



</script>
