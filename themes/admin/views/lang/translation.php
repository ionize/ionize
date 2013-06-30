
<div id="maincolumn">

	<h2 class="main languages" id="main-title"><?php echo lang('ionize_title_translation'); ?></h2>
	
	<div class="subtitle">
		<form>
			<div class="w400 relative">
				<label class="left"><?php echo lang('ionize_label_article_filter'); ?></label>
				<input id="search_translation" type="text" class="inputtext w300 left"></input>
				<a id="cleanFilter" class="icon clearfield left ml5"></a>
				<div class="clear"></div>
			</div>
		</form>
	
	</div>
	


	<!-- Tabs -->
	<div id="translationsTab" class="mainTabs clear mt20">
		<ul class="tab-menu">
			<li id="theme_translations"><a><?php echo lang('ionize_tab_current_theme'); ?></a></li>
			<li id="modules_translations"><a><?php echo lang('ionize_tab_modules'); ?></a></li>
		</ul>
		<div class="clear"></div>
	</div>

	<div id="translationsTabContent">
	
		<!-- Theme translations -->
		<div class="tabcontent">

			<form name="themeTranslationForm" id="themeTranslationForm" method="post" action="">

				<input type="hidden" name="file_name" value="theme"></input>

				<!-- Buttons -->
				<input id="themeTranslationFormSubmit" type="button" class="submit right ml5" value="<?php echo lang('ionize_button_save'); ?>" />
				<a class="button light btnExpand right ml5" rel="themeTogglers"><?php echo lang('ionize_label_expand_all'); ?></a>
				<a class="button light right ml5" id="btnAddTranslation">
					<i class="icon-plus"></i><?php echo lang('ionize_label_add_translation'); ?>
				</a>

				<fieldset id="blocks" class="clear">
		
					<?php
						$nbLang = count(Settings::get_languages());
						$width = (100 / $nbLang) - 3;
					?>
					
					<div id="themeTogglers" class="togglers">
		
						<?php
							$el_id = 0;
						?>
		
						<?php foreach($terms as $term) :?>
						
							<?php
								$el_id ++;
							?>
						
							<ul class="term">
							
								<li>
									<span class="toggler left" style="display:block;height:16px;" data-id="<?php echo $el_id; ?>"></span><input type="text" class="left inputtext w300 transinput" id="key_<?php echo $el_id; ?>" name="key_<?php echo $el_id; ?>" value="<?php echo $term; ?>"></input>
									<a class="left icon delete ml5" data-id="<?php echo $el_id; ?>"></a>
								</li>
								
								<div class="translation pl5" id="el_<?php echo $el_id; ?>">
								
									<?php foreach(Settings::get_languages() as $language) :?>
								
										<?php $lang = $language['lang']; ?>
										
										<div style="float:left;width:<?php echo $width?>%;margin-right:2%;">
											<label for="<?php echo $lang; ?>_<?php echo $el_id; ?>"><?php echo $language['name']; ?></label>
											<textarea name="value_<?php echo $lang; ?>_<?php echo $el_id; ?>" id="<?php echo $lang; ?>_<?php echo $el_id; ?>" class="transtext h60 ml5" style="width:100%;"><?php echo $theme_translations[$lang][$term]; ?></textarea>
										</div>
										
									<?php endforeach ;?>
		
									<p class="pl5 lite small clear">
										<?php if( ! empty($theme_terms['views'][$term]) ) :?>
										
											<?php echo $theme_terms['views'][$term]; ?>
										
										<?php endif ;?>
									
									</p>
														
								</div>
								
							</ul>
		
						<?php endforeach ;?>
				
					</div>
				</fieldset>
			</form>
			<!-- Term block model -->
			<ul id="termModel" style="display:none;">
				<li><span class="toggler"></span><input type="text" class="inputtext w300"></input></li>
				<div class="translation ml15 model">
					<?php foreach(Settings::get_languages() as $language) :?>
						<?php $lang = $language['lang']; ?>
						<div style="float:left;width:<?php echo $width?>%;margin-right:2%">
							<label for="<?php echo $lang; ?>_"><?php echo $language['name']; ?></label>
							<textarea name="value_<?php echo $lang; ?>_" class="h60 ml5" style="width:100%;"></textarea>
						</div>
					<?php endforeach ;?>
					<p class="clear"></p>
				</div>
			</ul>
		</div>
		
		<!-- Modules translations -->
		<div class="tabcontent">
		
			<?php
				$nbLang = count(Settings::get_languages());
				$width = (100 / $nbLang) - 3;
			?>
			
			<p class="mb20"><?php echo lang('ionize_help_modules_translation'); ?></p>

			<?php foreach($module_translations as $module => $terms) :?>
				
				<h3><?php echo $module; ?></h3>
				
				<form name="<?php echo $module; ?>TranslationForm" id="<?php echo $module; ?>TranslationForm" method="post">
					
					<input type="hidden" name="file_name" value="module_<?php echo $module; ?>"></input>
					
					<!-- Save button -->
					<div class="toolbox nobr">
						<input id="<?php echo $module; ?>TranslationFormSubmit" type="button" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
					</div>
					
					<!-- Expand button -->
					<a class="button light btnExpand right" rel="<?php echo $module; ?>Togglers">
						<i class="icon-expand"></i><?php echo lang('ionize_label_expand_all'); ?>
					</a>

					<p>
						<?php echo lang('ionize_text_module_translation_file_exist_for'); ?> : <strong><?php echo implode(', ', $module_translation_files[($module)]); ?></strong>
					</p>

					<fieldset class="clear mb20">
					
						<div id="<?php echo $module; ?>Togglers" class="togglers">
			
			
							<?php foreach($terms as $term => $values) :?>
							
								<?php
									$el_id ++;
								?>
								
								<ul class="term clear">
								
									<li class="toggler" data-id="<?php echo $el_id; ?>">
										<span class="left" style="display:block;height:16px;"></span>
										<span class="left"><i class="lite transtext"><?php echo $term; ?></i></span>
										<input type="hidden" id="key_<?php echo $el_id; ?>" name="key_<?php echo $el_id; ?>" value="<?php echo $term; ?>"></input>
									</li>
									
									<div class="translation pl5 clear" id="el_<?php echo $el_id; ?>">
									
										<?php foreach(Settings::get_languages() as $language) :?>
									
											<?php $lang = $language['lang']; ?>
											
											<div style="float:left;width:<?php echo $width?>%;margin-right:2%;">
												<label class="m0" for="<?php echo $lang; ?>_<?php echo $el_id; ?>"><?php echo $language['name']; ?></label>
												<textarea name="value_<?php echo $lang; ?>_<?php echo $el_id; ?>" id="<?php echo $lang; ?>_<?php echo $el_id; ?>" class="transtext h60" style="width:100%;"><?php echo $values[$lang]['theme']; ?></textarea>
												<i class="lite transtext"><?php echo $values[$lang]['default']; ?></i>
											</div>
											
										<?php endforeach ;?>
			
										<p class="pl5 lite small clear">
											<?php if( ! empty($theme_terms['views'][$term]) ) :?>
											
												<?php echo $theme_terms['views'][$term]; ?>
											
											<?php endif ;?>
										</p>
															
									</div>
								</ul>
			
							<?php endforeach ;?>
					
						</div>
					
					</fieldset>

				</form>
				
				<script type="text/javascript">

					ION.setFormSubmit('<?php echo $module; ?>TranslationForm', '<?php echo $module; ?>TranslationFormSubmit', 'translation/save');

				</script>
				
			<?php endforeach ;?>
				
		</div>
	
	</div>

	
</div>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	ION.initToolbox('empty_toolbox');

	ION.setFormSubmit('themeTranslationForm', 'themeTranslationFormSubmit', 'translation/save');

    // Save with CTRL+s
    ION.addFormSaveEvent('themeTranslationFormSubmit');


	/**
	 * Add translation button (for theme translations)
	 *
	 */
	$('btnAddTranslation').addEvent('click', function(e) 
	{
		addTranslationTerm();
	});

	
	var initTogglers = function()
	{
		$$('.togglers .toggler').each(function(el)
		{
			el.child = $('el_' + el.getProperty('data-id')).hide();
			
			el.removeEvent('click');
			el.removeClass('expand');
			el.getParent('ul').removeClass('highlight');
			
			el.addEvent('click', function()
			{
				this.toggleClass('expand');
				this.child.toggle();
				this.getParent('ul').toggleClass('highlight');
			});
		});
	}
	initTogglers();
	
	/**
	 * Expand / Collapse button
	 *
	 */
	var initBtnExpand = function()
	{
		$$('.btnExpand').each(function(item)
		{
			item.store('status', 'collapse');
			item.togglers = item.getProperty('rel');
			
			item.removeEvent('click');
			
			item.addEvent('click', function(e) 
			{
				e.stop();
				if (this.retrieve('status') == 'collapse')
				{
					$$('#' + this.togglers + ' .toggler').each(function(el){
						el.addClass('expand');
						el.child.show();
						el.getParent('ul').addClass('highlight');
					});
					this.value = Lang.get('ionize_label_collapse_all');
					this.store('status', 'expand');
				}
				else
				{
					$$('#' + this.togglers +' .toggler').each(function(el){
						el.child.hide();
						el.removeClass('expand');
						el.getParent('ul').removeClass('highlight');
					});
					this.value = Lang.get('ionize_label_expand_all');
					this.store('status', 'collapse');
				}
			});
		});
	}
	initBtnExpand();


	/** 
	 * Tabs init
	 *
	 */
	new TabSwapper({tabsContainer: 'translationsTab', sectionsContainer: 'translationsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'translationsTab' });
	
	/**
	 * Term delete
	 *
	 */
	$$('#themeTogglers .delete').each(function(item)
	{
		var rel = item.getProperty('data-id');
		
		item.addEvent('click', function(e)
		{
			ION.confirmation(
				'deleteTranslationTerm' + rel,
				function()
				{
					$('key_' + rel).value = '';
					ION.sendData(base_url + 'translation/save', $('themeTranslationForm'))
				},
				Lang.get('ionize_message_delete_translation')
			);
		});

	});
	
	var addTranslationTerm = function(parent)
	{
		var childs = $('themeTogglers').getChildren('ul');
		var nb = childs.length + 1;

		var clone = $('termModel').clone();
		var toggler = clone.getElement('.toggler');
		toggler.setProperty('rel', nb);
		
		var input = clone.getElement('input');
		input.setProperty('name', 'key_' + nb);
		
		var translation = clone.getElement('.translation');
		translation.setProperty('id', 'el_' + nb);
		
		var labels = clone.getElements('label');
		labels.each(function(label, idx)
		{
			label.setProperty('for', label.getProperty('for') + nb);
		});
		
		var textareas = clone.getElements('textarea');
		textareas.each(function(textarea, idx)
		{
			textarea.setProperty('name', textarea.getProperty('name') + nb);
		});
		
		clone.inject($('themeTogglers'), 'top').setStyle('display', 'block');
		input.focus();
		
		initTogglers();
	}
	
	var searchTranslation = function(search)
	{
		var reg = new RegExp('<span class="highlight"[^><]*>|<.span[^><]*>','g');
		
		var search = RegExp(search,"gi");
		
		$$('.term').each(function(el)
		{
			var els = el.getElements('.transtext');
			var c = '';
			els.each(function(el)
			{
				c = c + el.get('text');
			});
			
			els = el.getElements('.transinput');
			els.each(function(el)
			{
				c = c + el.value;
			});
			
			var m = c.match(search);
			
			if ( (m) )
			{
				el.show();
			}
			else
				el.hide();
		});
	}
	
	$('search_translation').addEvent('keyup', function(e)
	{
		e.stop();
		
		var search = this.value;
		
		if (search.length > 2)
		{
			if (this.timeoutID)
			{
				clearTimeout(this.timeoutID);
			}
			this.timeoutID = searchTranslation.delay(500, this, search);
		}
	});
	
	$('cleanFilter').addEvent('click', function(e)
	{
		$('search_translation').value = '';
		
		$$('.translation').each(function(el)
		{
			if ( ! el.hasClass('model'))
				var ul = el.getParent('ul').show();
		});
	});
	
	
</script>