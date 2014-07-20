<?php
/**
 * Website Settings
 *
 */
?>
<!-- Main Column -->
<div id="maincolumn">

<!--
	<input id="settingsFormSubmit" type="button" class="submit right" value="<?php echo lang('ionize_button_save_settings'); ?>" />
-->
	<h2 class="main website" id="main-title"><?php echo lang('ionize_title_site_settings'); ?></h2>
	
	<!-- Title & Meta keywords & Meta description -->
	<fieldset id="blocks">

		<!-- Tabs -->
		<div id="webSettingsTab" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $l) :?>
					<li<?php if($l['def'] == '1') :?> class="dl"<?php endif ;?>><a><span><?php echo ucfirst($l['name']); ?></span></a></li>
				<?php endforeach ;?>
				<li id="email_settings"><a><?php echo lang('ionize_title_mail_send'); ?></a></li>
				<li id="ga_settings"><a><?php echo lang('ionize_title_google_analytics'); ?></a></li>
				<li id="seo_settings"><a><?php echo lang('ionize_title_seo'); ?></a></li>
			</ul>
			<div class="clear"></div>
		</div>


		<div id="webSettingsTabContent">


			<form name="settingsForm" id="settingsForm" method="post" action="<?php echo admin_url(); ?>setting/save">

				<!-- Tabs content blocks -->
				<?php foreach(Settings::get_languages() as $language) :?>
					
					<div class="tabcontent p20">
					
						<!-- Title -->
						<dl>
							<dt>
								<label for="site_title_<?php echo $language['lang']; ?>"><?php echo lang('ionize_label_site_title'); ?></label>
							</dt>
							<dd>
								<input name="site_title_<?php echo $language['lang']; ?>" id="site_title_<?php echo $language['lang']; ?>" class="inputtext w360" type="text" value="<?php echo Settings::get('site_title', $language['lang']); ?>"/>
							</dd>
						</dl>
		
						<dl>
							<dt>
								<label for="meta_description_<?php echo $language['lang']; ?>"><?php echo lang('ionize_label_meta_description'); ?></label>
							</dt>
							<dd>
								<textarea name="meta_description_<?php echo $language['lang']; ?>" id="meta_description_<?php echo $language['lang']; ?>" class="w360 h60"><?php echo Settings::get('meta_description', $language['lang']); ?></textarea>
							</dd>
						</dl>
		
						<dl>
							<dt>
								<label for="meta_keywords_<?php echo $language['lang']; ?>"><?php echo lang('ionize_label_meta_keywords'); ?></label>
							</dt>
							<dd>
								<textarea name="meta_keywords_<?php echo $language['lang']; ?>" id="meta_keywords_<?php echo $language['lang']; ?>" class="w360 h60"><?php echo Settings::get('meta_keywords', $language['lang']); ?></textarea>
							</dd>
						</dl>
		
					</div>
		
				<?php endforeach ;?>

				<!-- Emails -->
				<div class="tabcontent p20">

					<div>
						<?php
						$emails = array('contact', 'info', 'technical');
						?>
						<?php foreach($emails as $email) :?>
							<dl>
								<dt>
									<label for="email_<?php echo $email ?>"  title="<?php echo lang('ionize_help_email_'.$email); ?>"><?php echo lang('ionize_label_email_'.$email); ?></label>
								</dt>
								<dd>
									<input id="email_<?php echo $email ?>" name="email_<?php echo $email ?>" class="inputtext w240" type="text" value="<?php echo Settings::get('email_'.$email); ?>" />
								</dd>
							</dl>
						<?php endforeach ;?>
					</div>

				</div>


				<!-- Google Analytics -->
				<div class="tabcontent p20">
					<dl>
						<dt>
							<label for="google_analytics_id" title="<?php echo lang('ionize_help_setting_google_analytics_id'); ?>"><?php echo lang('ionize_label_google_analytics_id'); ?></label>
						</dt>
						<dd>
							<input type="text" name="google_analytics_id" id="google_analytics_id" class="inputtext w100" value="<?php echo Settings::get('google_analytics_id'); ?>" />
						</dd>
					</dl>
					<dl class="mt10">
						<dt>
							<label for="google_analytics" title="<?php echo lang('ionize_help_setting_google_analytics'); ?>"><?php echo lang('ionize_label_google_analytics_tracking_code'); ?></label>
						</dt>
						<dd>
							<textarea name="google_analytics" id="google_analytics" class="autogrow w360"><?php echo htmlentities(stripslashes(Settings::get('google_analytics')), ENT_QUOTES, 'utf-8'); ?></textarea>
						</dd>
					</dl>
					<dl class="mt10">
						<dt>
							<label for="google_analytics_profile_id" title="<?php echo lang('ionize_help_setting_google_analytics_profile_id'); ?>"><?php echo lang('ionize_label_google_analytics_profile_id'); ?></label>
						</dt>
						<dd>
							<input type="text" name="google_analytics_profile_id" id="google_analytics_profile_id" class="inputtext w100" value="<?php echo Settings::get('google_analytics_profile_id'); ?>" />
						</dd>
					</dl>
					<dl class="mt10">
						<dt>
							<label for="google_analytics_url" title="<?php echo lang('ionize_help_setting_google_analytics_url'); ?>"><?php echo lang('ionize_label_google_analytics_url'); ?></label>
						</dt>
						<dd>
							<input type="text" name="google_analytics_url" id="google_analytics_url" class="inputtext w180" value="<?php echo Settings::get('google_analytics_url'); ?>" />
						</dd>
					</dl>
					<dl class="mt10">
						<dt>
							<label for="google_analytics_email" title="<?php echo lang('ionize_help_setting_google_analytics_email'); ?>"><?php echo lang('ionize_label_google_analytics_email'); ?></label>
						</dt>
						<dd>
							<input type="text" name="google_analytics_email" id="google_analytics_email" class="inputtext w180" value="<?php echo Settings::get('google_analytics_email'); ?>" />
						</dd>
					</dl>
					<dl class="mt10">
						<dt>
							<label for="google_analytics_password" title="<?php echo lang('ionize_help_setting_google_analytics_password'); ?>"><?php echo lang('ionize_label_google_analytics_password'); ?></label>
						</dt>
						<dd>
							<input type="password" name="google_analytics_password" id="google_analytics_password" class="inputtext w180" value="<?php echo Settings::get('google_analytics_password'); ?>" />
						</dd>
					</dl>
					<dl class="mt10">
						<dt>
							<label for="dashboard_google" title="<?php echo lang('ionize_help_display_google'); ?>"><?php echo lang('ionize_label_display_google'); ?></label>
						</dt>
						<dd>
							<input class="inputcheckbox" type="checkbox" name="dashboard_google" id="dashboard_google" <?php if (Settings::get('dashboard_google') == '1'):?> checked="checked" <?php endif;?> value="1" />
						</dd>
					</dl>
				</div>

			</form>
			

			<!-- SEO -->
			<div class="tabcontent pt20">

				<!-- Compress Sitemap XML -->
				<form name="sitemapGzipForm" id="sitemapGzipForm" method="post" action="<?php echo admin_url(); ?>setting/save_setting">

					<input type="hidden" name="config_file" value="sitemaps" />
					<input type="hidden" name="setting" value="sitemaps_gzip" />
					
					<dl class="last">
						<dt>
							<label for="sitemaps_gzip" title="<?php echo lang('ionize_help_setting_sitemaps_gzip'); ?>"><?php echo lang('ionize_label_sitemaps_gzip'); ?></label>
						</dt>
						<dd>
							<input class="inputcheckbox" <?php if (config_item('sitemaps_gzip') == '1'):?>checked="checked"<?php endif;?> type="checkbox" name="setting_value" id="sitemaps_gzip" value="true" />
						</dd>
					</dl>
				</form>

				<!-- Generate Sitemap after each page or article change -->
				<form name="sitemapAutoForm" id="sitemapAutoForm" method="post" action="<?php echo admin_url(); ?>setting/save_setting">

					<input type="hidden" name="config_file" value="sitemaps" />
					<input type="hidden" name="setting" value="sitemaps_auto_create" />

					<dl class="last">
						<dt>
							<label for="sitemaps_auto_create" title="<?php echo lang('ionize_help_setting_sitemaps_auto_create'); ?>"><?php echo lang('ionize_label_sitemaps_auto_create'); ?></label>
						</dt>
						<dd>
							<input class="inputcheckbox" <?php if (config_item('sitemaps_auto_create') == '1'):?>checked="checked"<?php endif;?> type="checkbox" name="setting_value" id="sitemaps_auto_create" value="true" />
						</dd>
					</dl>
				</form>





				<h3><?php echo lang('ionize_title_sitemap_search_engine'); ?></h3>
				
				<!-- Sitemaps Search Engines -->
				<form name="sitemapUrlForm" id="sitemapUrlForm" method="post" action="<?php echo admin_url(); ?>setting/save_setting">
					
					<input type="hidden" name="config_file" value="sitemaps" />
					<input type="hidden" name="type" value="array" />
					<input type="hidden" name="setting" value="sitemaps_search_engines" />
					
					<div class="summary r10">
					
						<p><?php echo lang('ionize_text_sitemaps_url_list'); ?> :</p>
						<p><textarea class="w400 h80" name="setting_value"><?php echo implode("\n", config_item('sitemaps_search_engines')); ?></textarea></p>
						<input id="submit_sitemap_url" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
						
					</div>
				</form>
				
				
				<!-- Permalink Ping -->
				<h3 class="mt20"><?php echo lang('ionize_title_permalink_ping_server'); ?></h3>

				<form name="pingUrlForm" id="pingUrlForm" method="post" action="<?php echo admin_url(); ?>setting/save_seo_urls">
					
					<input type="hidden" name="type" value="permalink_ping" />

					<div class="summary r10">

						<p><?php echo lang('ionize_text_ping_url_list'); ?> :</p>
						<p><textarea class="w400 h80" name="urls"><?php echo str_replace("|", "\n", Settings::get('permalink_ping_urls')); ?></textarea></p>
						<input id="submit_ping_url" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
						
					</div>
				</form>

			
			</div>

		</div>
	</fieldset>

</div> <!-- /maincolumn -->


<script type="text/javascript">

	ION.initFormAutoGrow();

	// Panel toolbox
	ION.initToolbox('setting_toolbox');

	// Options Accordion
	ION.initAccordion('.toggler', 'div.element');

	//Tabs init
	new TabSwapper({tabsContainer: 'webSettingsTab', sectionsContainer: 'webSettingsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'webSettingsTab' });


	// SEO URLs forms action
	ION.setFormSubmit('pingUrlForm', 'submit_ping_url', 'setting/save_seo_urls');
	ION.setFormSubmit('sitemapUrlForm', 'submit_sitemap_url', 'setting/save_setting');
	ION.setChangeSubmit('sitemapGzipForm', 'sitemaps_gzip', 'setting/save_setting');
	ION.setChangeSubmit('sitemapAutoForm', 'sitemaps_auto_create', 'setting/save_setting');

</script>