
<!-- Main Column -->
<div id="maincolumn">

	<h2 class="main languages" id="main-title"><?php echo lang('ionize_title_language'); ?></h2>

	<!-- Tabs -->
	<div id="langTab" class="mainTabs">

		<ul class="tab-menu">

			<li id="langExistingTab"><a><?php echo lang('ionize_title_existing_languages'); ?></a></li>
			<li id="langContentTab"><a><?php echo lang('ionize_title_content'); ?></a></li>
			<li id="langUrlTab"><a><?php echo lang('ionize_title_options'); ?></a></li>

		</ul>
		<div class="clear"></div>

	</div>

	<div id="langTabContent">

		<!-- Existing languages -->
		<div class="tabcontent">

			<form name="existingLangForm" id="existingLangForm" method="post" action="<?php echo admin_url(); ?>lang/update">

				<?php if (!$languages = Settings::get_languages()) :?>

					<p><?php echo lang('ionize_message_no_languages') ;?></p>

				<?php else : ?>

					<!-- Submit button -->
					<p class="h30">
						<input id="existingLangFormSubmit" type="button" class="right submit" value="<?php echo lang('ionize_button_update'); ?>" />
					</p>

					<input name="current_default_lang" id="current_default_lang" type="hidden" value="<?php echo Settings::get_lang('default'); ?>"/>

					<!-- Sortable UL -->
					<ul id="langContainer" class="sortable pb20">

						<?php foreach($languages as $lang) :?>

							<?php
								$code = $lang['lang'];
								$name = $lang['name'];
							?>

							<li id="lang_<?php echo $code; ?>" class="sortme" data-id="<?php echo $code; ?>">

								<!-- Drag icon -->
								<div class="drag left mt5">
									<span class="icon ordering"></span>
								</div>

                                <!-- Delete button -->
                                <a class="icon right delete mt5" data-id="<?php echo $code; ?>"></a>

								<!-- Lang Code -->
								<dl class="small">
									<dt>
										<label for="lang_<?php echo $code; ?>"><?php echo lang('ionize_label_code'); ?></label>
									</dt>
									<dd>
										<input name="lang_<?php echo $code; ?>" id="lang_<?php echo$code?>" class="inputtext" type="text" value="<?php echo $code; ?>"/>
									</dd>
								</dl>

								<!-- Name -->
								<dl class="small">
									<dt>
										<label for="name_<?php echo $code; ?>"><?php echo lang('ionize_label_name'); ?></label>
									</dt>
									<dd>
										<input name="name_<?php echo $code; ?>" id="name_<?php echo$code?>" class="inputtext" type="text" value="<?php echo $name; ?>"/>
									</dd>
								</dl>

								<!-- Direction -->
								<dl class="small mt10">
									<dt>
										<label><?php echo lang('ionize_label_lang_direction'); ?></label>
									</dt>
									<dd>
										<input id="direction_<?php echo $code; ?>_1" <?php if ($lang['direction'] == '1' OR empty($lang['direction'])):?>checked="checked"<?php endif;?> type="radio" name="direction_<?php echo $code; ?>" class="inputradio" value="1" />
										<label for="direction_<?php echo $code; ?>_1"><?php echo lang('ionize_label_lang_direction_ltr'); ?></label>
										<input id="direction_<?php echo $code; ?>_2" <?php if ($lang['direction'] == '2'):?>checked="checked"<?php endif;?> type="radio" name="direction_<?php echo $code; ?>" class="inputradio" value="2" />
										<label for="direction_<?php echo $code; ?>_2"><?php echo lang('ionize_label_lang_direction_rtl'); ?></label>
									</dd>
								</dl>

								<!-- Online ? -->
								<dl class="small">
									<dt>
										<label for="online_<?php echo $code; ?>"><?php echo lang('ionize_label_online'); ?></label>
									</dt>
									<dd>
										<input id="online_<?php echo $code; ?>" name="online_<?php echo $code; ?>" <?php if ($lang['online'] == '1'):?>checked="checked"<?php endif;?> class="inputcheckbox" type="checkbox" value="1" />
									</dd>
								</dl>

								<!-- Default ? -->
								<dl class="small">
									<dt>
										<label for="def_<?php echo $code; ?>"><?php echo lang('ionize_label_default'); ?></label>
									</dt>
									<dd>
										<input id="def_<?php echo $code; ?>" <?php if (Settings::get_lang('default') == $code ):?>checked="checked"<?php endif;?> type="radio" name="default_lang" class="inputradio" value="<?php echo $code; ?>" />
									</dd>
								</dl>

							</li>

						<?php endforeach ;?>

					</ul>

				<?php endif ;?>
			</form>

		</div>


		<!-- Copy content -->
		<div class="tabcontent p20">

			<dl class="small">
				<dt>
					<label for="lang_copy_from" title="<?php echo lang('ionize_help_copy_all_content'); ?>"><?php echo lang('ionize_label_copy_all_content'); ?></label>
				</dt>
				<dd>
					<div class="w100 left">
						<select name="lang_copy_from" id="lang_copy_from" class="w100 select">
							<?php foreach(Settings::get_languages() as $language) :?>
							<option value="<?php echo $language['lang']; ?>"><?php echo ucfirst($language['name']); ?></option>
							<?php endforeach ;?>
						</select>

						<br/>

						<select name="lang_copy_to" id="lang_copy_to" class="w100 select mt5">
							<?php foreach(Settings::get_languages() as $language) :?>
							<option value="<?php echo $language['lang']; ?>"><?php echo ucfirst($language['name']); ?></option>
							<?php endforeach ;?>
						</select>

					</div>
					<div class="w30 h50 left ml5" style="background:url('<?php echo admin_style_url(); ?>images/icon_24_from_to.png') no-repeat 50% 50%;"></div>
				</dd>
			</dl>

			<!-- Submit button  -->
			<dl class="small">
				<dt>&#160;</dt>
				<dd>
					<input type="submit" value="<?php echo lang('ionize_button_copy_content'); ?>" class="submit" id="copy_lang">
				</dd>
			</dl>

		</div>


		<!-- URLs -->
		<div class="tabcontent p20">

			<form name="optionsLangForm" id="optionsLangForm" method="post" action="<?php echo admin_url(); ?>lang/update">

				<dl>
					<dt>
						<label for="force_lang_urls"><?php echo lang('ionize_label_force_lang_urls'); ?></label>
					</dt>
					<dd>
						<input <?php if (Settings::get('force_lang_urls') == '1'):?>checked="checked"<?php endif;?> class="inputcheckbox" type="checkbox" name="force_lang_urls" id="force_lang_urls" value="1" />
					</dd>
				</dl>

				<dl class="last">
					<dt></dt>
					<dd>
						<input id="optionsLangFormSubmit" type="button" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
					</dd>
				</dl>

			</form>

			<?php
			/*
			<p><?php echo lang('ionize_notify_advanced_language'); ?></p>

			<form name="cleanLangForm" id="cleanLangForm" method="post">

				<input id="submit_clean" type="submit" class="submit" value="<?php echo lang('ionize_button_clean_lang_tables'); ?>" />
				<label title="<?php echo lang('ionize_help_clean_lang_tables'); ?>"></label>

			</form>
			*/
			?>


		</div>

	</div>


</div>


<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('lang_toolbox');


	// ION.initAccordion('.toggler1', 'div.element1', false, 'langAccordion2');

	// Tabs
	new TabSwapper({tabsContainer: 'langTab', sectionsContainer: 'langTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'langTab' });

	/*
	 * Lang itemManager
	 * Use of ItemManager.deleteItem, etc.
	 */
	langManager = new ION.ItemManager(
	{
		element: 	'lang',
		container: 	'langContainer'		
	});
	
	langManager.makeSortable();

	// Forms submit
	ION.setFormSubmit('existingLangForm', 'existingLangFormSubmit', 'lang/update');
	ION.setFormSubmit('optionsLangForm', 'optionsLangFormSubmit', 'lang/save_options');

	// Content copy confirmation callback
	var copyLang = function()
	{
		var url = admin_url + 'lang/copy_lang_content';

		var data = {
			'case': 'lang',
			'from' : $('lang_copy_from').value,
			'to' : $('lang_copy_to').value
		};
		ION.sendData(url, data);

	};

	// Copy content
	$('copy_lang').addEvent('click', function(e)
	{
		e.stop();

		ION.confirmation(
			'copyLangConfWindow',
			copyLang,
			Lang.get('ionize_message_confirm_copy_whole_content')
		);
	});

</script>





