
<div id="sidecolumn" class="close">

	<!- Informations -->
	<div class="info"></div>
	
	<div id="options">

		<!-- New language -->
		<h3 class="toggler mt20"><?=lang('ionize_title_add_language')?></h3>

		<div class="element">

			<form name="newLangForm" id="newLangForm" method="post" action="<?= admin_url() ?>lang/save">

				<!-- Lang Code -->
				<dl class="small">
					<dt>
						<label for="lang_new"><?=lang('ionize_label_code')?></label>
					</dt>
					<dd>
						<input id="lang_new" name="lang_new" class="inputtext w40" type="text" value="" />
					</dd>
				</dl>

				<!-- Name -->
				<dl class="small">
					<dt>
						<label for="name_new"><?=lang('ionize_label_name')?></label>
					</dt>
					<dd>
						<input id="name_new" name="name_new" class="inputtext w140" type="text" value=""/><br />
					</dd>
				</dl>

				<!-- Online  -->
				<dl class="small">
					<dt>
						<label for="online_new"><?=lang('ionize_label_online')?></label>
					</dt>
					<dd>
						<input id="online_new" name="online_new" class="inputcheckbox" type="checkbox" value="1" />
					</dd>
				</dl>
			
				<!-- Submit button  -->
				<dl class="small">
					<dt>
						<label>&#160;</label>
					</dt>
					<dd>
						<input id="submit_new" type="submit" class="submit" value="<?= lang('ionize_button_save_new_lang') ?>" />
					</dd>
				</dl>
				
			</form>

		</div> <!-- /element -->
		
		
		<!-- Copy Content -->
		<h3 class="toggler"><?= lang('ionize_title_content') ?></h3>
		
		<div class="element">
		
			<dl class="small">
				<dt>
					<label for="lang_copy_from" title="<?= lang('ionize_help_copy_all_content') ?>"><?= lang('ionize_label_copy_all_content') ?></label>
				</dt>
				<dd>
					<div class="w100 left">
						<select name="lang_copy_from" id="lang_copy_from" class="w100 select">
							<?php foreach(Settings::get_languages() as $language) :?>
								<option value="<?= $language['lang'] ?>"><?= ucfirst($language['name']) ?></option>
							<?php endforeach ;?>
						</select>
						
						<br/>
					
						<select name="lang_copy_to" id="lang_copy_to" class="w100 select mt5">
							<?php foreach(Settings::get_languages() as $language) :?>
								<option value="<?= $language['lang'] ?>"><?= ucfirst($language['name']) ?></option>
							<?php endforeach ;?>
						</select>
					
					</div>
					<div class="w30 h50 left ml5" style="background:url(<?= theme_url() ?>images/icon_24_from_to.png) no-repeat 50% 50%;"></div>
				</dd>
			</dl>
		
			<!-- Submit button  -->
			<dl class="small">
				<dt>&#160;</dt>
				<dd>
					<input type="submit" value="<?= lang('ionize_button_copy_content') ?>" class="submit" id="copy_lang">
				</dd>
			</dl>
		
		</div>
		
		
		<!-- Advanced actions with content -->
		<h3 class="toggler"><?=lang('ionize_title_advanced_language')?></h3>

		<div class="element">

			<p><?=lang('ionize_notify_advanced_language')?></p>
	
			<form name="cleanLangForm" id="cleanLangForm" method="post">

				<input id="submit_clean" type="submit" class="submit" value="<?= lang('ionize_button_clean_lang_tables') ?>" />
				<label title="<?= lang('ionize_help_clean_lang_tables') ?>"></label>
				
			</form>

		</div>

	</div> <!-- /options -->

</div> <!-- /sidecolumn -->


<!-- Main Column -->
<div id="maincolumn">

	<h2 class="main languages" id="main-title"><?= lang('ionize_title_language') ?></h2>


	<!-- No languages -->
	<?php if (!$languages = Settings::get_languages()) :?>

		<p><?= lang('ionize_message_no_languages') ;?></p>

	<!-- Existing languages -->
	<?php else : ?>

		<form name="existingLangForm" id="existingLangForm" method="post" action="<?= admin_url() ?>lang/update">


		<input name="current_default_lang" id="current_default_lang" type="hidden" value="<?= Settings::get_lang('default'); ?>"/>


		<h3 class="toggler1 mt20"><?=lang('ionize_title_existing_languages')?></h3>
		
		<div class="element1">

		<!-- Sortable UL -->
		<ul id="langContainer" class="sortable pb20">

			<?php foreach($languages as $lang) :?>

				<?php
					$code = $lang['lang'];
					$name = $lang['name'];
				?>

				<li id="lang_<?= $code ?>" class="sortme h100" rel="<?= $code ?>">

					<!-- Drag icon -->
					<div class="drag left h100">
						<img src="<?= theme_url() ?>images/icon_16_ordering.png" />
					</div>

					<!-- Lang Code -->
					<dl class="small">
						<dt>
							<label for="lang_<?= $code ?>"><?=lang('ionize_label_code')?></label>
						</dt>
						<dd>
							<input name="lang_<?= $code ?>" id="lang_<?=$code?>" class="inputtext" type="text" value="<?= $code ?>"/>
							
							<!-- Delete button -->
							<a class="icon right delete" rel="<?= $code ?>"></a>
							
						</dd>
					</dl>

					<!-- Name -->
					<dl class="small">
						<dt>
							<label for="name_<?= $code ?>"><?=lang('ionize_label_name')?></label>
						</dt>
						<dd>
							<input name="name_<?= $code ?>" id="name_<?=$code?>" class="inputtext" type="text" value="<?= $name ?>"/>
						</dd>
					</dl>

					<!-- Online ? -->
					<dl class="small">
						<dt>
							<label for="online_<?= $code ?>"><?=lang('ionize_label_online')?></label>
						</dt>
						<dd>
							<input id="online_<?= $code ?>" name="online_<?= $code ?>" <?php if ($lang['online'] == '1'):?>checked="checked"<?php endif;?> class="inputcheckbox" type="checkbox" value="1" />
						</dd>
					</dl>

					<!-- Default ? -->
					<dl class="small">
						<dt>
							<label for="def_<?= $code ?>"><?=lang('ionize_label_default')?></label>
						</dt>
						<dd>
							<input id="def_<?= $code ?>" <?php if (Settings::get_lang('default') == $code ):?>checked="checked"<?php endif;?> type="radio" name="default_lang" class="inputradio" value="<?= $code ?>" />
						</dd>
					</dl>

				</li>

			<?php endforeach ;?>

		</ul>
		</div>
		
		<!-- URLs -->
		<h3 class="toggler1"><?=lang('ionize_title_lang_urls')?></h3>
		
		<div class="element1">
	
			<dl class="last">
				<dt>
					<label for="force_lang_urls"><?=lang('ionize_label_force_lang_urls')?></label>
				</dt>
				<dd>
					<input <?php if (Settings::get('force_lang_urls') == '1'):?>checked="checked"<?php endif;?> class="inputcheckbox" type="checkbox" name="force_lang_urls" id="force_lang_urls" value="1" />
				</dd>
			</dl>
	
		</div>


		</form>

	<?php endif ;?>

</div>


<script type="text/javascript">
	

	/**
	 * New lang form action
	 * see init-form.js for more information about this method
	 *
	 */
	ION.setFormSubmit('newLangForm', 'submit_new', 'lang/save');


	/**
	 * Clean Lang tables form action
	 *
	 */
	ION.setFormSubmit('cleanLangForm', 'submit_clean', 'lang/clean_tables', {message:Lang.get('ionize_confirmation_clean_lang')});

	 
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('lang_toolbox');

				
	ION.initAccordion('.toggler', 'div.element', true, 'langAccordion1');
	ION.initAccordion('.toggler1', 'div.element1', false, 'langAccordion2');
	

	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
	ION.initLabelHelpLinks('#options');


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

	
</script>





