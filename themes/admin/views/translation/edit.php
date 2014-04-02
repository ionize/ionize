
<h2 class="main languages"><?php echo $filename; ?></h2>

<div class="mt20 clearfix">
	<form>
		<div class="w300 relative left">
			<label class="left"><?php echo lang('ionize_label_article_filter'); ?></label>
			<input id="search_translation" type="text" class="inputtext w220 left" />
			<a id="cleanFilter" class="icon clearfield left ml5"></a>
			<div class="clear"></div>
		</div>
		<div class=" right">
			<a class="button light" id="addTranslationsButton">
				<i class="icon-plus"></i><?php echo lang('ionize_label_add_translation'); ?>
			</a>
		</div>
	</form>
</div>

<div class="mt20">
<form id="translationForm" action="<?php echo admin_url(); ?>translations/save" method="post">

	<!-- Hidden Inputs -->
	<input type="hidden" name="filename" value="<?php echo $filename; ?>" />
	<input type="hidden" name="path" value="<?php echo $path; ?>" />
	<input type="hidden" name="lang_path" value="<?php echo $lang_path; ?>" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />

	<!-- Tabs -->
	<div id="translationsTab" class="mainTabs clear mt20">
		<ul class="tab-menu">
			<?php foreach($languages as $language) :?>
				<?php $lang = $language['lang']; ?>

				<li id="<?php echo $lang; ?>" class="tab_translation<?php if($default_lang_code == $lang): ?> dl<?php endif; ?>"><a><?php echo $language['name']; ?></a></li>

			<?php endforeach; ?>
		</ul>
		<div class="clear"></div>
	</div>
	<div id="translationsTabContent">
		<?php foreach($languages as $language) :?>

			<?php $lang = $language['lang']; ?>

			<div class="tabcontent">

				<!-- Filter Button Group -->
				<div class="translation_toolbox translationToolbox_<?php echo $lang; ?>">

					<div class="divider">
						<a id="<?php echo $lang; ?>TranslationFormSubmit" class="button submit">
							<?php echo lang('ionize_button_save'); ?>
						</a>
					</div>

					<div class="divider">
						<a class="button light translation-button-<?php echo $lang; ?>" data-filter="empty">
							<i class="icon-flag red"></i><?php echo lang('ionize_title_translation_empty_translations'); ?>
						</a>
					</div>
					<div class="divider">
						<a class="button light translation-button-<?php echo $lang; ?>" data-filter="same">
							<i class="icon-flag yellow"></i><?php echo lang('ionize_title_translation_same_translations'); ?>
						</a>
					</div>
					<div class="divider">
						<a class="button light active translation-button-<?php echo $lang; ?>" data-filter="all">
							<i class="icon-lang"></i><?php echo lang('ionize_title_translation_all_translations'); ?>
						</a>
					</div>
					<div class="divider title-filter">
						<?php echo lang('ionize_label_filter_by'); ?> :
					</div>

				</div>

				<div id="translation_<?php echo $lang; ?>" class="translations translation_<?php echo $lang; ?>">
					<table class="translationList list" id="translationTable<?php echo $lang; ?>">
						<tbody id="translations_<?php echo $lang; ?>">
							<?php
								$el_id = 0;
							?>

							<?php foreach($items[$lang] as $key => $value): ?>
								<?php

									$el_id ++;

									$_value = $value;

									$class = array();

									if($_value == '') {
										$_value = $key;
										$class[] = 'empty';
									}
									if($default_lang_code != $lang && $value != '' && $items[$default_lang_code][$key] == $value)
									{
										$class[] = 'same';
									}

									$_value = strip_tags($_value);
									$_value = word_limiter($_value, 10, '...');

									$_key = $key;

									if($default_lang_code != $lang)
									{
										$_key = ($items[$default_lang_code][$key] != '') ? $items[$default_lang_code][$key] : $key;
									}

									$help_title = '';
									if($default_lang_code != $lang)
									{
										$help_title = ($items[$default_lang_code][$key] != '') ? ' class="help" title="'. $key .'" ' : '';
									}

									$class = ( ! empty($class)) ? ' ' . implode(' ', $class) : '';

								?>
								<tr class="translation_item translation_<?php echo $lang; ?><?php echo $class; ?>">
									<th>
										<label for="<?php echo $lang; ?>_<?php echo $el_id; ?>"<?php echo $help_title; ?>><?php echo $_key; ?></label>
										<input type="hidden" class="translation_term_key" id="key_<?php echo $el_id; ?>" name="key_<?php echo $el_id; ?>" value="<?php echo $key; ?>" />
									</th>
									<td>
										<?php if ( mb_strlen( $value ) > $textarea_line_break ): ?>

											<?php
												echo form_textarea(
													array(
														'name' => 'value_' . $lang . '_' . $el_id,
														'id' => $lang . '_' . $el_id,
														'value' => ''.$value.'',
														'rows' => $textarea_rows,
														'class' => 'translation_term_value autogrow textarea w100p'
													)
												);
											?>

										<?php else:?>

											<?php
												echo form_input(
													array(
														'name' => 'value_' . $lang . '_' . $el_id,
														'id' => $lang . '_' . $el_id,
														'value' => ''.$value.'',
														'class' => 'translation_term_value inputtext w100p',
													)
												);
											?>

										<?php endif;?>
										<?php if(! empty($items['views'][$key])): ?>
											<br />
											<small><b><?php echo $items['views'][$key]; ?></b></small>
										<?php endif; ?>
									</td>
									<td class="center">
										<a class="icon delete ml5" data-id="<?php echo $el_id; ?>"></a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<script type="text/javascript">

				var lang = '<?php echo $lang; ?>';

				// Filter Menu
				$$('.translationToolbox_<?php echo $lang; ?> .divider a').addEvent('click', function(e)
				{
					$$('.translationToolbox_<?php echo $lang; ?>').getElement('a.active').removeClass('active');
					this.addClass('active');
				});

				// Filter Buttons
				$$('.translation-button-<?php echo $lang; ?>').each(function(item, idx)
				{
					var filterValue = item.getProperty('data-filter');

					item.addEvent('click', function(e)
					{
						switch (filterValue) {
							case "empty":
								$$('.translation_<?php echo $lang; ?> .translation_item').hide();
								$$('.translation_<?php echo $lang; ?> .' + filterValue).setStyle('display', '');
								// Show Empty Hide Others
								break;
							case "same":
								// Show Same Hide Others
								$$('.translation_<?php echo $lang; ?> .translation_item').hide();
								$$('.translation_<?php echo $lang; ?> .' + filterValue).setStyle('display', '');
								break;
							case "all":
								// Show All
								$$('.translation_<?php echo $lang; ?> .translation_item').setStyle('display', '');
								break;
						}
					});
				});

				// Form save action
				ION.setFormSubmit('translationForm', '<?php echo $lang; ?>TranslationFormSubmit', 'translation/save');

				// Save with CTRL+s
				ION.addFormSaveEvent('<?php echo $lang; ?>TranslationFormSubmit');

			</script>
		<?php endforeach; ?>
	</div>
</form>
</div>
<script type="text/javascript">


	//Panel toolbox
	ION.initToolbox(
		null,
		function(el)
		{
			new ION.ButtonToolbar(el,{
					buttons:
						[
							// Back to Translations Button
							{
								title: Lang.get('ionize_label_back_to_translations'),
								icon: 'icon-back',
								'class': 'light',
								onClick: function()
								{
									ION.HTML(
										ION.adminUrl + 'translation/welcome',
										{},
										{'update': 'splitPanel_mainPanel_pad'}
									);
								}
							}
						]
				}
			);
		}
	);

	// Tabs init
	new TabSwapper({
		tabsContainer: 'translationsTab',
		sectionsContainer: 'translationsTabContent',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent',
		cookieName: 'translationsTab'
	});

	// Term delete
	$$('#translationForm .delete').each(function(item)
	{
		var rel = item.getProperty('data-id');

		item.addEvent('click', function(e)
		{
			ION.confirmation(
				'deleteTranslationTerm' + rel,
				function()
				{
					var translationInputs = $('translationForm').getElements('input[name=key_' + rel + ']');

					translationInputs.each(function(el){
						el.setProperty('value', '');
					});

					ION.sendData(admin_url + 'translation/save', $('translationForm'))
				},
				Lang.get('ionize_message_delete_translation')
			);
		});

	});


	// Filter Translations Form
	$('search_translation').addEvent('keyup', function(e)
	{
		e.stop();

		var search = this.value;

		// If Search Term > 2 Make Search
		if (search.length > 2)
		{
			if (this.timeoutID)
			{
				clearTimeout(this.timeoutID);
			}
			this.timeoutID = searchTranslation.delay(500, this, search);
		}

		// If Search Term Length < 3 Reset Show ALL
		if(search.length < 3)
		{
			clearTranslationSearch();
		}
	});

	// Clear Filtering...
	$('cleanFilter').addEvent('click', function(e)
	{
		$('search_translation').value = '';

		clearTranslationSearch();
	});

	// Filter Translation Items
	var searchTranslation = function(search)
	{
		$$('#translationForm').each(function(el)
		{
			var translationItem = el.getElements('.translation_item');

			translationItem.each(function(item){

				var itemKey = item.getElement('.translation_term_key').getProperty('value'),
					itemValue = item.getElement('.translation_term_value').getProperty('value');

				if( (itemKey.match(search)) || (itemValue.match(search)))
					item.setStyle('display', '');
				else
					item.hide();
			})
		});
	}

	// Show All Translation Items
	var clearTranslationSearch = function()
	{
		$$('#translationForm').each(function(el)
		{
			var translationItem = el.getElements('.translation_item');

			translationItem.each(function(item){
				item.setStyle('display', '');
			})
		});
	}

	// Add New Translation Item Button
	$('addTranslationsButton').addEvent('click', function(){
		addTranslationItem();
	});

	// Add New Translation Item Function
	var addTranslationItem = function()
	{
		<?php foreach($languages as $language): ?>

			var lang = '<?php echo $language['lang'] ?>',
				translations = $('translationTable' + lang).getElements('tr'),
				nbTranslations = translations.length + 1
			;

			var tr = new Element('tr', {
				'class' : 'translation_item newTranslationItem'
			});

			var th = new Element('th').inject(tr);

			var thInput = new Element('input', {
				'type' : 'text',
				'name' : 'key_' + nbTranslations,
				'class': 'inputtext',
				'data-id' : lang + '_' + nbTranslations,
				'data-idx': 'input_' + nbTranslations,
				'data-lang' : lang
			}).inject(th);

			var td = new Element('td', {'class': 'w100p'}).inject(tr);

			thInput.addEvent('keyup', function(el){

				var tdInputValue = this.value,
					tdInputLang = this.getProperty('data-lang'); // Current Field Lang

				var otherInputs = $('translationForm').getElements('input[data-idx=' + 'input_' + nbTranslations +']');

				otherInputs.each(function(item){
					if(item.getProperty('data-lang') != tdInputLang)
						item.setProperty('value', tdInputValue);
				});

			});

			var tdInput = new Element('input', {
				'type'  : 'text',
				'name'  : 'value_' + lang + '_' + nbTranslations,
				'class' : 'translation_term_value inputtext w100p',
				'value' : ''
			}).inject(td);

			tr.inject($('translations_' + lang), 'top');
			thInput.focus();

		<?php endforeach; ?>
	}

</script>