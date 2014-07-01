
<!-- Main Column -->
<div id="maincolumn">

	<h2 class="main settings" id="main-title"><?php echo lang('ionize_title_technical_settings'); ?></h2>
	
	<!-- Subtitle -->
	<div class="subtitle">
	</div>


	<!-- Tabs -->
	<div id="settingsTab" class="mainTabs mt30">
		
		<ul class="tab-menu">
			
			<li id="media_settings"><a><?php echo lang('ionize_title_media_management'); ?></a></li>
			<li id="article_settings"><a><?php echo lang('ionize_title_article_management'); ?></a></li>
			<li id="database_settings"><a><?php echo lang('ionize_title_database'); ?></a></li>
			<li id="email_settings"><a><?php echo lang('ionize_title_mail_send'); ?></a></li>
			<!--
			<li id="api_settings"><a><?php /*echo lang('ionize_title_api'); */?></a></li>
			-->
			<li id="system_settings"><a><?php echo lang('ionize_title_system'); ?></a></li>

		</ul>

		<div class="clear"></div>
	
	</div>


	<div id="settingsTabContent">
	
		<!-- Media management -->
		<div class="tabcontent">
			
			<form name="settingsMediasForm" id="settingsMediasForm" method="post">

				<p class="h30"><input id="settingsMediasFormSubmit" type="button" class="submit right" value="<?php echo lang('ionize_button_save_settings'); ?>" /></p>

				<div class="tabsidecolumn">
					
					<h3><?php echo lang('ionize_title_media_management'); ?></h3>
				
					<dl class="small">
						<dt>
							<label for="files_path" title="<?php echo lang('ionize_help_setting_files_path'); ?>"><?php echo lang('ionize_label_files_path'); ?></label>
						</dt>
						<dd>
							<input name="files_path" id="files_path" class="inputtext" type="text" value="<?php echo Settings::get('files_path'); ?>"/>
						</dd>
					</dl>

                    <dl class="small">
                        <dt>
                            <label for="upload_autostart" title="<?php echo lang('ionize_help_upload_autostart'); ?>"><?php echo lang('ionize_label_setting_upload_autostart'); ?></label>
                        </dt>
                        <dd>
                            <input <?php if($upload_autostart == 1) :?>checked="checked" <?php endif ;?> name="upload_autostart" id="upload_autostart"  class="inputcheckbox" type="checkbox" value="1" />
                        </dd>
                    </dl>

                    <dl class="small">
                        <dt>
                            <label><?php echo lang('ionize_label_upload_mode'); ?></label>
                        </dt>
                        <dd>
                            <input type="radio" name="upload_mode" id="upload_mode1" value="" <?php if(Settings::get('upload_mode') == ''):?>checked="checked"<?php endif;?> /><label for="upload_mode1" title="<?php echo lang('ionize_help_upload_mode_auto'); ?>"><?php echo lang('ionize_label_upload_mode_auto'); ?></label><br/>
                            <input type="radio" name="upload_mode" id="upload_mode2" value="html4" <?php if(Settings::get('upload_mode') == 'html4'):?>checked="checked"<?php endif;?>/><label for="upload_mode2" title="<?php echo lang('ionize_help_upload_mode_html4'); ?>"><?php echo lang('ionize_label_upload_mode_html4'); ?></label><br/>
                            <input type="radio" name="upload_mode" id="upload_mode3" value="html5" <?php if(Settings::get('upload_mode') == 'html5'):?>checked="checked"<?php endif;?>/><label for="upload_mode3" title="<?php echo lang('ionize_help_upload_mode_html5'); ?>"><?php echo lang('ionize_label_upload_mode_html5'); ?></label>
                        </dd>
                    </dl>

                    <dl class="small">
						<dt>
							<label for="resize_on_upload"><?php echo lang('ionize_label_setting_resize_on_upload'); ?></label>
						</dt>
						<dd>
                            <input <?php if($resize_on_upload == 1) :?>checked="checked" <?php endif ;?> name="resize_on_upload" id="resize_on_upload"  class="inputcheckbox" type="checkbox" value="1" />
						</dd>
					</dl>
					
					<dl class="small">
						<dt>
							<label for="picture_max_width" title="<?php echo lang('ionize_help_setting_picture_max_width'); ?>"><?php echo lang('ionize_label_setting_picture_max_width'); ?></label>
						</dt>
						<dd>
							<input name="picture_max_width" id="picture_max_width" class="inputtext w40" type="text" value="<?php echo Settings::get('picture_max_width'); ?>"/>
						</dd>
					</dl>

					<dl class="small">
						<dt>
							<label for="picture_max_height" title="<?php echo lang('ionize_help_setting_picture_max_height'); ?>"><?php echo lang('ionize_label_setting_picture_max_height'); ?></label>
						</dt>
						<dd>
							<input name="picture_max_height" id="picture_max_height" class="inputtext w40" type="text" value="<?php echo Settings::get('picture_max_height'); ?>"/>
						</dd>
					</dl>
		
					<dl class="small">
						<dt>
							<label for="media_thumb_size" title="<?php echo lang('ionize_help_media_thumb_size'); ?>"><?php echo lang('ionize_label_media_thumb_size'); ?></label>
						</dt>
						<dd>
							<input name="media_thumb_size" id="media_thumb_size" class="inputtext w40" type="text" value="<?php echo Settings::get('media_thumb_size'); ?>"/>
						</dd>
					</dl>

					<dl class="small">
						<dt>
							<label for="media_thumb_unsharp"><?php echo lang('ionize_label_thumb_unsharp'); ?></label>
						</dt>
						<dd>
							<input <?php if($media_thumb_unsharp == 1) :?>checked="checked" <?php endif ;?> name="media_thumb_unsharp" id="media_thumb_unsharp"  class="inputcheckbox" type="checkbox" value="1" />
						</dd>
					</dl>

				</div>
				
				<!-- Allowed Mimes -->
				<div class="tabcolumn">
					
					<h3><?php echo lang('ionize_title_allowed_mimes'); ?></h3>
					<p class="mb15"><?php echo lang('ionize_text_allowed_mimes'); ?></p>
	
					<?php
						$filemanager_file_types = explode(',',Settings::get('filemanager_file_types'));
					?>
		
					<?php foreach($mimes as $type => $mime_list) :?>

                        <?php ksort($mime_list); ?>
					
						<h3 class="toggler1"><?php echo $type; ?></h3>
					
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
											<td class="right pr10"><?php echo $ext; ?> </td>
											<td>
												<label for="allowed_type_<?php echo $ext; ?>" class="m0">
													<?php if (is_array($mime)): ?>
														<?php echo(implode('<br/>', $mime)); ?>
													<?php else: ?>
														<?php echo $mime; ?>
													<?php endif;?>
												</label>
											</td>
											<td class="center">
												<input <?php if(in_array($ext, $filemanager_file_types)) :?>checked="checked" <?php endif ;?>id="allowed_type_<?php echo $ext; ?>" class="inputcheckbox" name="allowed_type[]" type="checkbox" value="<?php echo $ext; ?>" />
											</td>
										</tr>
									<?php endforeach ;?>
			
								</tbody>
							</table>
						
						</div>
					
					<?php endforeach ;?>

					<h3><?php echo lang('ionize_title_no_source_picture'); ?></h3>
					<p class="mb15"><?php echo lang('ionize_text_no_source_picture'); ?></p>

					<!-- Thumb name -->
					<dl class="small">
						<dt>
							<label for="no_source_picture"><?php echo lang('ionize_label_no_source_picture'); ?></label>
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
			
				<p class="h30"><input id="articleSettingsFormSubmit" type="button" class="submit right" value="<?php echo lang('ionize_button_save_settings'); ?>" /></p>
				
				<dl>
					<dt>
						&nbsp;
					</dt>
					<dd>
						<p class="lite"><?php echo lang('ionize_onchange_ionize_settings'); ?></p>
					</dd>
				</dl>
				<dl class="mb20">
					<!-- TinyMCE Block Format (Select) -->
					<dt>
						<label for="tinyblockformats" title="<?php echo lang('ionize_help_tinyblockformats'); ?>"><?php echo lang('ionize_label_tinyblockformats'); ?></label>
					</dt>
					<dd>
						<input class="inputtext w360 mb5" id="tinyblockformats" name="tinyblockformats" type="text" value="<?php echo Settings::get('tinyblockformats'); ?>"/><br />
						<a id="texteditor_default_tinyblockformats"><?php echo lang('ionize_label_restore_tinyblockformats'); ?></a>
					</dd>
				</dl>
	
				<dl class="mb20">
					<!-- TinyMCE toolbar buttons -->
					<dt>
						<label title="<?php echo lang('ionize_help_tinybuttons'); ?>"><?php echo lang('ionize_label_tinybuttons'); ?></label>
					</dt>
					<dd>
						1 <input class="inputtext w360 mb5" id="tinybuttons1" name="tinybuttons1" type="text" value="<?php echo Settings::get('tinybuttons1'); ?>"/><br />
						2 <input class="inputtext w360 mb5" id="tinybuttons2" name="tinybuttons2" type="text" value="<?php echo Settings::get('tinybuttons2'); ?>"/><br />
						3 <input class="inputtext w360" id="tinybuttons3" name="tinybuttons3" type="text" value="<?php echo Settings::get('tinybuttons3'); ?>"/><br />
						<a id="texteditor_default"><?php echo lang('ionize_label_restore_tinybuttons'); ?></a> | <a target="_blank" href="http://www.tinymce.com/wiki.php/Buttons/controls"><?php echo lang('ionize_label_help_tinybuttons'); ?></a>
					</dd>
					
				</dl>
				
				<dl class="mb20">
					<!-- TinyMCE toolbar buttons -->
					<dt>
						<label title="<?php echo lang('ionize_help_tinybuttons'); ?>"><?php echo lang('ionize_label_small_tinybuttons'); ?></label>
					</dt>
					<dd>
						1 <input class="inputtext w360 mb5" id="smalltinybuttons1" name="smalltinybuttons1" type="text" value="<?php echo Settings::get('smalltinybuttons1'); ?>"/><br />
						2 <input class="inputtext w360 mb5" id="smalltinybuttons2" name="smalltinybuttons2" type="text" value="<?php echo Settings::get('smalltinybuttons2'); ?>"/><br />
						3 <input class="inputtext w360" id="smalltinybuttons3" name="smalltinybuttons3" type="text" value="<?php echo Settings::get('smalltinybuttons3'); ?>"/><br />
						<a id="small_texteditor_default"><?php echo lang('ionize_label_restore_tinybuttons'); ?></a> | <a target="_blank" href="http://www.tinymce.com/wiki.php/Buttons/controls"><?php echo lang('ionize_label_help_tinybuttons'); ?></a>
					</dd>

				</dl>


				<dl class="last mb20">
					<!-- TinyMCE Block Format (Select) -->
					<dt>
						<label title="<?php echo lang('ionize_help_article_allowed_tags'); ?>"><?php echo lang('ionize_label_article_allowed_tags'); ?></label>
					</dt>
					<dd>
	
						<?php
							$tags = array(
								'tag1' => array('h1','h2','h3','h4','h5','h6','em','img','audio','video'),
								'tag2' => array('iframe','div','span','table','object','form','dl','pre','code','legend'),
								'tag3' => array('dfn','samp','kbd','var','cite','mark','q','hr','big','small'),
								'tag4' => array('link','address','abbr','sub','sup','ins','blockquote','bdi','bdo','i'),
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
													<tr><td class="pr10"><label for="tag_<?php echo $tag?>"><?php echo $tag?></label></td><td class="center"><input id="tag_<?php echo $tag?>" name="article_allowed_tags[]" <?php if(in_array($tag, $article_allowed_tags)) :?>checked="checked" <?php endif;?>type="checkbox" value="<?php echo $tag?>"/></td></tr>
												<?php endforeach; ?>
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

	        <div class="tabsidecolumn">

                <!-- Database backup -->
                <h3><?php echo lang('ionize_title_db_backup'); ?></h3>

                <p>

                    <a class="button light" id="bdBackup" href="<?php echo admin_url(); ?>setting/backup_database"><i class="icon-database"></i><?php echo lang('ionize_label_db_backup'); ?></a>
                </p>

			</div>

            <div class="tabcolumn">

                <form name="databaseForm" id="databaseForm" method="post" action="<?php echo admin_url(); ?>setting/save_database">

                    <p class="h30"><input id="submit_database" type="button" class="submit right" value="<?php echo lang('ionize_button_save_settings'); ?>" /></p>

                    <dl>
                        <dt>
                            &nbsp;
                        </dt>
                        <dd>
                            <p class="lite"><?php echo lang('ionize_onchange_ionize_settings'); ?></p>
                        </dd>
                    </dl>

                    <!-- Driver -->
                    <dl>
                        <dt>
                            <label for="db_driver"><?php echo lang('ionize_label_db_driver'); ?></label>
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
                            <label for="db_host"><?php echo lang('ionize_label_db_host'); ?></label>
                        </dt>
                        <dd>
                            <input id="db_host" name="db_host" class="inputtext w140" type="text" value="<?php echo $db_host; ?>" />
                        </dd>
                    </dl>

                    <!-- Database -->
                    <dl>
                        <dt>
                            <label for="db_name"><?php echo lang('ionize_label_db_name'); ?></label>
                        </dt>
                        <dd>
                            <input id="db_name" name="db_name" class="inputtext w140" type="text" value="<?php echo $db_name; ?>" />
                        </dd>
                    </dl>

                    <!-- User -->
                    <dl>
                        <dt>
                            <label for="db_user"><?php echo lang('ionize_label_db_user'); ?></label>
                        </dt>
                        <dd>
                            <input id="db_user" name="db_user" class="inputtext w140" type="text" value="<?php echo $db_user; ?>" />
                        </dd>
                    </dl>

                    <!-- Password -->
                    <dl>
                        <dt>
                            <label for="db_pass"><?php echo lang('ionize_label_db_pass'); ?></label>
                        </dt>
                        <dd>
                            <input id="db_pass" name="db_pass" class="inputtext w140" type="password" value="" />
                        </dd>
                    </dl>

                </form>
			</div>

		</div>
		
		<!-- Email -->
		<div class="tabcontent pt10">
			<form name="emailForm" id="emailForm" method="post" action="<?php echo admin_url(); ?>setting/save_emails_settings">
			
				<p class="h30"><input id="submit_email" type="button" class="submit right" value="<?php echo lang('ionize_button_save_settings'); ?>" /></p>

				<h3><?php echo lang('ionize_title_email_server'); ?></h3>

				<dl class="mb10 mt20">
					<dt>
						<label for="site_email" title="<?php echo lang('ionize_help_site_email'); ?>"><?php echo lang('ionize_label_site_email'); ?></label>
					</dt>
					<dd>
						<input id="site_email" name="site_email" class="inputtext w240" type="text" value="<?php echo Settings::get('site_email'); ?>" />
					</dd>
				</dl>

				<!-- Protocol -->
				<dl>
					<dt>
						<label for="emailProtocol"><?php echo lang('ionize_label_smtp_protocol'); ?></label>
					</dt>
					<dd>
						<select name="protocol" id="emailProtocol" class="select">
							<option <?php if ($protocol == 'smtp'):?>selected="selected"<?php endif;?> value="smtp">SMTP</option>
							<option <?php if ($protocol == 'mail'):?>selected="selected"<?php endif;?> value="mail">Mail</option>
							<option <?php if ($protocol == 'sendmail'):?>selected="selected"<?php endif;?>  value="sendmail">SendMail</option>
						</select>
					</dd>
				</dl>

				<!-- Mail Path -->
				<div id="emailSendmailDetails">
					<dl>
						<dt>
							<label for="mailpath"><?php echo lang('ionize_label_mailpath'); ?></label>
						</dt>
						<dd>
							<input id="mailpath" name="mailpath" type="text" class="inputtext w140" value="<?php echo $mailpath; ?>" />
						</dd>
					</dl>
				</div>

				<!-- SMTP Details -->
				<div id="emailSMTPDetails">
					<!-- SMTP Host -->
					<dl>
						<dt>
							<label for="smtp_host"><?php echo lang('ionize_label_smtp_host'); ?></label>
						</dt>
						<dd>
							<input id="smtp_host" name="smtp_host" type="text" class="inputtext w140" value="<?php echo $smtp_host; ?>" />
						</dd>
					</dl>
					
					<!-- SMTP User -->
					<dl>
						<dt>
							<label for="smtp_user"><?php echo lang('ionize_label_smtp_user'); ?></label>
						</dt>
						<dd>
							<input id="smtp_user" name="smtp_user" type="text" class="inputtext w140" value="<?php echo $smtp_user; ?>" />
						</dd>
					</dl>
				
					<!-- SMTP Pass -->
					<dl>
						<dt>
							<label for="smtp_pass"><?php echo lang('ionize_label_smtp_pass'); ?></label>
						</dt>
						<dd>
							<input id="smtp_pass" name="smtp_pass" type="password" class="inputtext w140" value="<?php echo $smtp_pass; ?>" />
						</dd>
					</dl>
				
					<!-- SMTP Port -->
					<dl>
						<dt>
							<label for="smtp_port"><?php echo lang('ionize_label_smtp_port'); ?></label>
						</dt>
						<dd>
							<input id="smtp_port" name="smtp_port" type="text" class="inputtext w40" value="<?php echo $smtp_port; ?>" />
						</dd>
					</dl>

                    <!-- SMTP Timeout -->
                    <dl>
                        <dt>
                            <label for="smtp_timeout"><?php echo lang('ionize_label_smtp_timeout'); ?></label>
                        </dt>
                        <dd>
                            <input id="smtp_timeout" name="smtp_timeout" type="text" class="inputtext w40" value="<?php echo $smtp_timeout; ?>" />
                        </dd>
                    </dl>
				</div>
					
				<!-- Charset -->
				<dl>
					<dt>
						<label for="charset"><?php echo lang('ionize_label_email_charset'); ?></label>
					</dt>
					<dd>
						<input id="charset" name="charset" type="text" class="inputtext w140" value="<?php echo $charset; ?>" />
					</dd>
				</dl>
			
				<!-- Mailtype -->
				<dl>
					<dt>
						<label for="mailtype"><?php echo lang('ionize_label_email_mailtype'); ?></label>
					</dt>
					<dd>
						<select name="mailtype" id="mailtype" class="select">
							<option <?php if ($mailtype == 'text'):?>selected="selected"<?php endif;?> value="text">Text</option>
							<option <?php if ($mailtype == 'html'):?>selected="selected"<?php endif;?> value="html">HTML</option>
						</select>
					</dd>
				</dl>
			
				<!-- Newline -->
				<dl>
					<dt>
						<label for="newline"><?php echo lang('ionize_label_email_newline'); ?></label>
					</dt>
					<dd>
						<select name="newline" id="newline" class="select">
							<option <?php if ($newline == "\n"):?>selected="selected"<?php endif;?> value="\n">\n</option>
							<option <?php if ($newline == "\r\n"):?>selected="selected"<?php endif;?> value="\r\n">\r\n</option>
						</select>
					</dd>
				</dl>

			</form>
		</div>

		<!-- API -->
		<!--
		<div class="tabcontent">

            <p class="clear h20">
                <a id="buttonNewApiKey" class="left light button" title="<?php /*echo lang('ionize_label_create_new_api_key'); */?>">
					<i class="icon-plus"></i>
					<?php /*echo lang('ionize_label_create_new_api_key'); */?>
                </a>
            </p>

        </div>
		-->


		<!-- System -->
		<div class="tabcontent pt20">
		
			<div class="tabsidecolumn">
				
				<h3><?php echo lang('ionize_title_informations'); ?></h3>

				<dl class="small compact">
					<dt><label title="<?php echo lang('ionize_help_environment'); ?>"><?php echo lang('ionize_label_environment'); ?></label></dt>
					<dd><?php echo ENVIRONMENT; ?></dd>
				</dl>
				<dl class="small compact">
					<dt><label><?php echo lang('ionize_title_php_version'); ?></label></dt>
					<dd><?php echo phpversion(); ?></dd>
				</dl>
				<dl class="small compact">
					<dt><label><?php echo lang('ionize_title_db_version'); ?></label></dt>
					<dd><?php echo $this->db->platform().' '.$this->db->version(); ?></dd>
				</dl>
				<dl class="small compact">
					<dt><label><?php echo lang('ionize_label_file_uploads'); ?></label></dt>
					<dd>
						<?php if(ini_get('file_uploads') == true) :?>
							<a class="icon ok"></a>
						<?php else :?>
							<a class="icon nok"></a>
						<?php endif ;?>
					</dd>
				</dl>
				<dl class="small compact">
					<dt><label><?php echo lang('ionize_label_max_upload_size'); ?></label></dt>
					<dd><?php echo ini_get('upload_max_filesize'); ?></dd>
				</dl>
				<dl class="small compact">
					<dt>&nbsp;</dt>
					<dd><a href="<?php echo base_url() . config_item('admin_url'); ?>/desktop/get/system/phpinfo" target="_blank">Complete PHP Info</a></dd>
				</dl>
			</div>
			
			<div class="tabcolumn">
		
				<h3 class="toggler"><?php echo lang('ionize_title_encryption_key'); ?></h3>
		
				<div class="element">

					<form name="keysSettingsForm" id="keysSettingsForm" method="post">

						<!-- Form antispam key -->
						<dl class="mb10">
							<dt>
								<label for="form_antispam_key"><?php echo lang('ionize_label_antispam_key'); ?></label>
							</dt>
							<dd>
								<input id="form_antispam_key" name="form_antispam_key" type="text" class="inputtext w300 left" value="<?php echo $form_antispam_key; ?>" />
								<a class="icon left refresh ml5" id="antispamRefresh" title="<?php echo lang('ionize_label_refresh_antispam_key'); ?>"></a>
							</dd>
						</dl>
						
						<!-- Encryption key -->
						<dl class="mb10">
							<dt>
								<label for="form_antispam_key"><?php echo lang('ionize_title_encryption_key'); ?></label>
							</dt>
							<dd>
								<textarea disabled="disabled" class="w300"><?php echo config_item('encryption_key'); ?></textarea>
							</dd>
						</dl>
						
						<dl class="mb10">
							<dt>&nbsp;</dt>
							<dd>
								<input id="keysSettingsFormSubmit" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
							</dd>
						</dl>
					</form>
					
				</div>
				
				
				<!-- Cache -->
				<h3 class="toggler"><?php echo lang('ionize_title_cache'); ?></h3>
	
				<div class="element">
					<form name="cacheForm" id="cacheForm" method="post" action="<?php echo admin_url(); ?>setting/save_cache">
									
						<!-- Cache Time -->
						<dl>
							<dt>
								<label for="cache_expiration"  title="<?php echo lang('ionize_help_cache_expiration'); ?>"><?php echo lang('ionize_label_cache_expiration'); ?></label>
							</dt>
							<dd>
								<input id="cache_expiration" name="cache_expiration" class="inputtext w60" type="text" value="<?php echo config_item('cache_expiration'); ?>" />
								<input id="submit_cache" type="submit" class="submit m0" value="<?php echo lang('ionize_button_save'); ?>" />
							</dd>
						</dl>
						
						<!-- Empty cache  -->
						<dl class="mb10">
							<dt>
								<label for="clear_cache"  title="<?php echo lang('ionize_help_clear_cache'); ?>"><?php echo lang('ionize_label_clear_cache'); ?></label>
							</dt>
							<dd>
								<input id="clear_cache" type="button" class="submit m0" value="<?php echo lang('ionize_button_clear_cache'); ?>" />
							</dd>
						</dl>
		
					</form>
				</div>
				
				
				<!-- Admin URL -->
				<h3 class="toggler"><?php echo lang('ionize_title_admin_url'); ?></h3>
				
				<div class="element">
				
					<form name="adminUrlForm" id="adminUrlForm" method="post" action="<?php echo admin_url(); ?>setting/save_admin_url">
		
						<dl>
							<dt>
								<label for="admin_url"><?php echo lang('ionize_title_admin_url'); ?></label>
							</dt>
							<dd>
								<input id="admin_url" name="admin_url" class="inputtext w120" value="<?php echo config_item('admin_url'); ?>" /><br/>
								<p class="lite pl10"><?php echo lang('ionize_onchange_ionize_settings'); ?></p>
							</dd>
						</dl>
			
						<dl class="mb10">
							<dt>&nbsp;</dt>
							<dd>
								<input id="submit_admin_url" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
							</dd>
						</dl>
		
					</form>
					
				</div>
				
				
				<!-- Maintenance Mode -->
				<h3 class="toggler"><?php echo lang('ionize_title_maintenance'); ?></h3>
				
				<div class="element">
					
					<form name="maintenanceForm" id="maintenanceForm" method="post" action="<?php echo admin_url(); ?>setting/save_maintenance" class="mb20">
		
						<!-- Maintenance ? -->
						<dl>
							<dt>
								<label for="maintenance" title="<?php echo lang('ionize_label_maintenance_help'); ?>"><?php echo lang('ionize_label_maintenance'); ?></label>
							</dt>
							<dd>
								<input class="inputcheckbox" <?php if (config_item('maintenance') == '1'):?>checked="checked"<?php endif;?> type="checkbox" name="maintenance" id="maintenance" value="1" />
							</dd>
						</dl>
						
						<!-- Maintenance IP restrict -->
						<dl>
							<dt>
								<label for="maintenance_ips" title="<?php echo lang('ionize_label_maintenance_ips_help'); ?>"><?php echo lang('ionize_label_maintenance_ips'); ?></label>
							</dt>
							<dd>
								<span><?php echo lang('ionize_label_your_ip'); ?> : <?php echo $_SERVER['REMOTE_ADDR']; ?></span><br/>
								<textarea name="maintenance_ips" id="maintenance_ips" class="h50 w140"><?php echo (! empty($maintenance_ips)) ? $maintenance_ips : $_SERVER['REMOTE_ADDR']; ?></textarea>
							</dd>
						</dl>
		
						<!-- Maintenance page -->
						<?php if (function_exists('curl_init')) : ?>
							
							<dl>
								<dt>
									<label title="<?php echo lang('ionize_label_maintenance_page_help'); ?>"><?php echo lang('ionize_title_maintenance_page'); ?></label>
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
								<input id="submit_maintenance" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
							</dd>
						</dl>
		
						
					</form>

				</div>

                <!-- Compress HTML Output -->
                <h3 class="toggler"><?php echo lang('ionize_title_compress_html_output'); ?></h3>

                <div class="element">

                    <form name="compressHtmlOutputForm" id="compressHtmlOutputForm" method="post" action="<?php echo admin_url(); ?>setting/save_compress_html_output" class="mb20">

                        <!-- Maintenance ? -->
                        <dl>
                            <dt>
                                <label for="compress_html_output" title="<?php echo lang('ionize_label_compress_html_output_help'); ?>"><?php echo lang('ionize_label_compress_html_output'); ?></label>
                            </dt>
                            <dd>
                                <input class="inputcheckbox" <?php if (config_item('compress_html_output') == '1'):?>checked="checked"<?php endif;?> type="checkbox" name="compress_html_output" id="compress_html_output" value="1" />
                            </dd>
                        </dl>

                        <!-- Submit button  -->
                        <dl class="mt10">
                            <dt>&#160;</dt>
                            <dd>
                                <input id="submit_compress_html_output" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
                            </dd>
                        </dl>


                    </form>

                </div>
			</div>		
		</div>		
	</div>
</div> <!-- /maincolumn -->


<script type="text/javascript">


	var mailpath = '<?php echo $mailpath ?>';

	// Panel toolbox
	ION.initToolbox('empty_toolbox');


	// Options Accordion
	ION.initAccordion('.toggler', 'div.element', true, 'settingsAccordion1');
	ION.initAccordion('.toggler1', 'div.element1', false, 'settingsAccordion2');


	var settingsTab = new TabSwapper({tabsContainer: 'settingsTab', sectionsContainer: 'settingsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'settingsTab' });


	// Forms actions
	ION.setFormSubmit('databaseForm', 'submit_database', 'setting/save_database/true');
	ION.setFormSubmit('emailForm', 'submit_email', 'setting/save_emails_settings/true');
	ION.setFormSubmit('cacheForm', 'submit_cache', 'setting/save_cache');
	ION.setFormSubmit('maintenanceForm', 'submit_maintenance', 'setting/save_maintenance');
    ION.setFormSubmit('compressHtmlOutputForm', 'submit_compress_html_output', 'setting/save_compress_html_output');
	ION.setFormSubmit('settingsMediasForm', 'settingsMediasFormSubmit', 'setting/save_medias');
	ION.setFormSubmit('articleSettingsForm', 'articleSettingsFormSubmit', 'setting/save_article');
	ION.setFormSubmit('keysSettingsForm', 'keysSettingsFormSubmit', 'setting/save_keys');

	ION.initRequestEvent($('clear_cache'), 'setting/clear_cache');

	// Admin URL form action
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


	// Restore tinyButtons toolbar to default config
	$('texteditor_default').addEvent('click', function()
	{
		$('tinybuttons1').value = 'pdw_toggle,fullscreen,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,bullist,numlist,|,link,unlink,image,|,spellchecker';
		$('tinybuttons2').value = 'undo,redo,|,pastetext,selectall,removeformat,|,media,charmap,hr,blockquote,nonbreaking,|,template,|,codemirror';
		$('tinybuttons3').value = 'tablecontrols';
	
	});
	$('small_texteditor_default').addEvent('click', function()
	{
		$('smalltinybuttons1').value = 'bold,italic,|,bullist,numlist,|,link,unlink,image,|,nonbreaking';
		$('smalltinybuttons2').value = '';
		$('smalltinybuttons3').value = '';

	});

	$('texteditor_default_tinyblockformats').addEvent('click', function()
	{
		$('tinyblockformats').value = 'p,h2,h3,h4,h5,pre,div';
	});


	// Show / hides Email details depending on the selected protocol
	changeEmailDetails = function()
	{
		var protocol = $('emailProtocol').value;

		if (protocol == 'mail')
		{
			$('emailSMTPDetails').hide();
			$('emailSendmailDetails').hide();
		}
		else if (protocol == 'sendmail')
		{
			if (mailpath == '')
				$('mailpath').value = '/usr/sbin/sendmail';

			$('emailSMTPDetails').hide();
			$('emailSendmailDetails').show();
		}
		else
		{
			$('emailSMTPDetails').show();
			$('emailSendmailDetails').hide();
		}
	}
	changeEmailDetails();

	$('emailProtocol').addEvent('change', function()
	{
		changeEmailDetails();
	});


	// Make each tree page draggable to the maintenance page container
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

	// New API key button
	if ($('buttonNewApiKey'))
	{
		$('buttonNewApiKey').addEvent('click', function()
		{
			ION.formWindow(
				'apiKey',
				'apiKeyForm',
				Lang.get('ionize_button_new_api_key'),
				'api/key_edit/',
				{
					width:450,
					height:250
				}
			);
		});
	}

	// Save with CTRL+s
	ION.addFormSaveEvent('settingsFormSubmit');

</script>
