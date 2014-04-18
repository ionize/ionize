
<?php
	
/**
 * Used to add an element to a container (page , article) on the Editor side
 * Called by element_definition/get_element_detail() when the user click on one element definition
 * When saving, creates an element instance through : element/save
 * @receives:
 *          $lang_fields
 *          $fields
 */


// These values are empty when adding a new element
$id_element = ( !empty($id_element)) ? $id_element : '';
$parent = ( !empty($parent)) ? $parent : '';
$id_parent = ( !empty($id_parent)) ? $id_parent : '';

?>

<?php if ($id_element == '') :?>
	<a id="elementAddBackButton" class="light button back">
		<i class="icon-back"></i><?php echo lang('ionize_label_back_to_element_list'); ?>
	</a>
<?php endif ;?>

<div class="mt10" id="elementDiv<?php echo $id_element; ?>">

	<form name="elementForm" id="elementForm<?php echo $id_element; ?>" method="post">

		<input type="hidden" id="elementParent<?php echo $id_element; ?>" name="parent" value="<?php echo $parent; ?>" />
		<input type="hidden" id="elementIdParent<?php echo $id_element; ?>" name="id_parent" value="<?php echo $id_parent; ?>" />
		<input type="hidden" id="id_element<?php echo $id_element; ?>" name="id_element" value="<?php echo $id_element; ?>" />
		<input type="hidden" id="id_element_definition<?php echo $id_element; ?>" name="id_element_definition" value="<?php echo $element_definition['id_element_definition']; ?>" />

		<!-- Ordering : First or last (or Element one if exists ) -->
		<?php if( empty($id_element)) :?>
		<dl class="small mb10">
			<dt >
				<label for="ordering"><?php echo lang('ionize_label_ordering'); ?></label>
			</dt>
			<dd>
				<select name="ordering" id="ordering<?php echo $id_element; ?>" class="select">
					<?php if( ! empty($id_element)) :?>
						<option value="<?php echo $ordering; ?>"><?php echo $ordering; ?></option>
					<?php endif ;?>
					<option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
					<option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
				</select>
			</dd>
		</dl>
		<?php endif ;?>


		<?php foreach($fields as $field) :?>

			<?php
				$id_extend = $field['id_extend_field'];
			?>

			<?php if ($field['translated'] != '1' && $field['type'] != 8) :?>

				<?php
					$label = ( ! empty($field['lang_definition'][Settings::get_lang('default')]['label'])) ? $field['langs'][Settings::get_lang('default')]['label'] : $field['name'];
					$label_title = User()->is('super-admin') ? 'Key : ' . $field['name'] : ($field['description'] != '' ? $field['description'] : '');
					$field['content'] = (!empty($field['content'])) ? $field['content'] : $field['default_value'];
				?>

				<dl class="small">
					<dt>
						<label for="cf_<?php echo $id_extend; ?>" <?php if ( ! empty($label_title)) :?>title="<?php echo $label_title; ?>"<?php endif ;?>><?php echo $label; ?></label>
					</dt>
					<dd>
						<?php if ($field['type'] == '1') :?>
							<input id="cf_<?php echo $id_extend; ?>" class="inputtext w300 clear" type="text" name="cf_<?php echo $id_extend; ?>" value="<?php echo $field['content'] ; ?>" />
						<?php endif ;?>

						<?php if ($field['type'] == '2' OR $field['type'] == '3') :?>
							<textarea id="cf_<?php echo $id_extend; ?>" class="<?php if($field['type'] == '3'):?> tinyTextarea <?php endif ;?> inputtext h80" name="cf_<?php echo $id_extend; ?>"><?php echo $field['content']; ?></textarea>
						<?php endif ;?>

						<!-- Checkbox -->
						<?php if ($field['type'] == '4') :?>

							<?php
								$pos = 		explode("\n", $field['value']);
								$saved = 	explode(',', $field['content']);
							?>
							<?php
								$i = 0;
								foreach($pos as $values)
								{
									$vl = explode(':', $values);
									$key = $vl[0];
									$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

									?>
										<input type="checkbox" id= "cf_<?php echo $id_extend.$i; ?>" name="cf_<?php echo $id_extend; ?>[]" value="<?php echo $key; ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?php echo $id_extend . $i; ?>"><?php echo $value; ?></label></input><br/>
									<?php
									$i++;
								}
							?>
						<?php endif ;?>

						<!-- Radio -->
						<?php if ($field['type'] == '5') :?>

							<?php
								$pos = explode("\n", $field['value']);
							?>
							<?php
								$i = 0;
								foreach($pos as $values)
								{
									$vl = explode(':', $values);
									$key = $vl[0];
									$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

									?>
										<input type="radio" id= "cf_<?php echo $id_extend.$i; ?>" name="cf_<?php echo $id_extend; ?>" value="<?php echo $key; ?>" <?php if ($field['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?php echo $id_extend . $i; ?>"><?php echo $value; ?></label></input><br/>
									<?php
									$i++;
								}
							?>
						<?php endif ;?>

						<!-- Selectbox -->
						<?php if ($field['type'] == '6' && !empty($field['value'])) :?>

							<?php
								$pos = explode("\n", $field['value']);
								$saved = 	explode(',', $field['content']);
							?>
							<select name="cf_<?php echo $id_extend?>">
							<?php
								$i = 0;
								foreach($pos as $values)
								{
									$vl = explode(':', $values);
									$key = $vl[0];
									$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
									?>
									<option value="<?php echo $key; ?>" <?php if (in_array($key, $saved)) :?> selected="selected" <?php endif ;?>><?php echo $value; ?></option>
									<?php
									$i++;
								}
							?>
							</select>
						<?php endif ;?>

						<!-- Date & Time -->
						<?php if ($field['type'] == '7') :?>

							<input id="cf_<?php echo $id_extend; ?>" class="inputtext w120 date" type="text" name="cf_<?php echo $id_extend; ?>" value="<?php echo $field['content'] ; ?>" data-item="element" data-id="<?php echo $id_extend; ?>" />

						<?php endif ;?>

					</dd>
				</dl>

			<?php endif ;?>
		<?php endforeach ;?>


		<?php if( ! empty($lang_fields) OR $has_media_fields == TRUE) :?>

			<!-- Tabs -->
			<div id="elementTab<?php echo $UNIQ; ?>" class="mainTabs">
				<ul class="tab-menu">

					<?php if ($has_lang_fields) :?>
						<?php foreach(Settings::get_languages() as $language) :?>
							<li class="tab-element<?php echo $UNIQ; ?> <?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><span><?php echo ucfirst($language['name']); ?></span></a></li>
						<?php endforeach ;?>
					<?php endif ;?>

					<!-- Media Extend Fields -->
					<?php foreach($fields as $extend) :?>
						<?php if ($extend['type'] == 8) :?>
							<?php
							$id_extend = $extend['id_extend_field'];
							$label =  ! empty($extend['label'])  ? $extend['label'] : $extend['name'];
							$label_title = $extend['name'] . ( !empty($extend['description']) ? ' : ' .$extend['description'] : '');
							?>
							<?php if ($extend['translated'] != '1') :?>
								<li class="extendMediaTab left<?php if( empty($id_element)) :?> inactive<?php endif ;?>" data-id="<?php echo $id_extend ?>"><a><?php echo $label ?></a></li>
							<?php else:?>
								<?php foreach(Settings::get_languages() as $language) :?>
									<li class="extendMediaTab left<?php if( empty($id_element)) :?> inactive<?php endif ;?>" data-id="<?php echo $id_extend ?>" data-lang="<?php echo $language['lang']; ?>"><a><?php echo $label . ' ' . ucfirst($language['lang']); ?></a></li>
								<?php endforeach ;?>

							<?php endif;?>
						<?php endif;?>
					<?php endforeach;?>

				</ul>
				<div class="clear"></div>
			</div>

			<div id="elementTabContent<?php echo $UNIQ; ?>">

				<!-- Text block -->
				<?php if ($has_lang_fields) :?>
					<?php foreach(Settings::get_languages() as $language) :?>

						<?php $lang = $language['lang']; ?>

						<div class="tabcontent<?php echo $UNIQ; ?>">

							<p class="clear h15">
								<a class="right icon copy copyLang" rel="<?php echo $lang; ?>" title="<?php echo lang('ionize_label_copy_to_other_languages'); ?>"></a>
							</p>

							<?php foreach($lang_fields as $field) :?>

								<?php
									$id_extend = $field['id_extend_field'];
								?>

								<?php if ($field['type']< 8) :?>

									<dl class="small">
										<dt>
											<?php
												$label = ( ! empty($field['lang_definition'][$lang]['label'])) ? $field['lang_definition'][$lang]['label'] : $field['name'];
											?>
											<label for="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" title="<?php echo $field['description']; ?>"><?php echo $label; ?></label>
										</dt>
										<dd>
											<?php
												$field[$lang]['content'] = (!empty($field[$lang]['content'])) ? $field[$lang]['content'] : $field['default_value'];
											?>

											<?php if ($field['type'] == '1') :?>
												<input id="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" class="inputtext" type="text" name="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" value="<?php echo $field[$lang]['content']; ?>" />
											<?php endif ;?>

											<?php if ($field['type'] == '2' || $field['type'] == '3') :?>
												<textarea id="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" class="inputtext h80 <?php if($field['type'] == '3'):?> tinyTextarea <?php endif ;?>" name="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" rel="<?php echo $lang; ?>"><?php echo $field[$lang]['content']; ?></textarea>
											<?php endif ;?>

											<!-- Checkbox -->
											<?php if ($field['type'] == '4') :?>

												<?php
													$pos = 		explode("\n", $field['value']);
													$saved = 	explode(',', $field[$lang]['content']);
												?>

												<?php
													$i = 0;
													foreach($pos as $values)
													{
														$vl = explode(':', $values);
														$key = $vl[0];
														$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

														?>
														<input type="checkbox" id= "cf_<?php echo $id_extend.$i; ?>_<?php echo $lang; ?>" name="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>[]" value="<?php echo $key; ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?php echo $id_extend . $i; ?>_<?php echo $lang; ?>"><?php echo $value; ?></label></input><br/>
														<?php
														$i++;
													}
												?>
											<?php endif ;?>

											<!-- Radio -->
											<?php if ($field['type'] == '5') :?>

												<?php
													$pos = explode("\n", $field['value']);
												?>
												<?php
													$i = 0;
													foreach($pos as $values)
													{
														$vl = explode(':', $values);
														$key = $vl[0];
														$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

														?>
														<input type="radio" id= "cf_<?php echo $id_extend.$i; ?>_<?php echo $lang; ?>" name="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" value="<?php echo $key; ?>" <?php if ($field[$lang]['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?php echo $id_extend . $i; ?>_<?php echo $lang; ?>"><?php echo $value; ?></label></input><br/>
														<?php
														$i++;
													}
												?>
											<?php endif ;?>

											<!-- Selectbox -->
											<?php if ($field['type'] == '6' && !empty($field['value'])) :?>

												<?php
													$pos = explode("\n", $field['value']);
													$saved = 	explode(',', $field[$lang]['content']);
												?>
												<select name="cf_<?php echo $id_extend?>_<?php echo $lang; ?>">
												<?php
													$i = 0;
													foreach($pos as $values)
													{
														$vl = explode(':', $values);
														$key = $vl[0];
														$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
														?>
														<option value="<?php echo $key; ?>" <?php if (in_array($key, $saved)) :?> selected="selected" <?php endif ;?>><?php echo $value; ?></option>
														<?php
														$i++;
													}
												?>
												</select>
											<?php endif ;?>

										</dd>
									</dl>

								<?php endif ;?>

							<?php endforeach ;?>

						</div>

					<?php endforeach ;?>
				<?php endif ;?>

				<!-- Extends : Medias -->
				<?php foreach($fields as $extend) :?>

					<?php if ($extend['type'] == 8) :?>
						<?php
						$id_extend = $extend['id_extend_field'];
						$label =  ! empty($extend['label'])  ? $extend['label'] : $extend['name'];
						$label_title = $extend['name'] . ( !empty($extend['description']) ? ' : ' .$extend['description'] : '');
						?>
						<?php if ($extend['translated'] != '1') :?>
							<div class="tabcontent<?php echo $UNIQ; ?>">
								<?php if( ! empty($id_element)) :?>
									<p class="h30">
										<?php if (User()->is('super-admin')) :?>
											<span class="lite">Extend code : <strong><?php echo  $extend['name']?></strong></span>
										<?php endif ;?>
										<a data-id="<?php echo $id_extend ?>" data-label="<?php echo $label ?>" class="extendMediaButton button light right">
											<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
										</a>
									</p>
								<?php endif;?>
								<?php if ( empty($id_element)) :?>
									<?php echo lang('ionize_message_please_save_first') ?>
								<?php endif ;?>

								<div class="sortable-container extendMediaContainer" data-id="<?php echo $id_extend ?>" data-label="<?php echo $label ?>"></div>
							</div>
						<?php else:?>
							<?php foreach(Settings::get_languages() as $language) :?>
								<div class="tabcontent<?php echo $UNIQ; ?>">
									<?php if( ! empty($id_element)) :?>
										<p class="h30">
											<?php if (User()->is('super-admin')) :?>
												<span class="lite">Extend code : <strong><?php echo  $extend['name']?></strong></span>
											<?php endif ;?>
											<a data-id="<?php echo $id_extend ?>" data-lang="<?php echo $language['lang'] ?>" data-label="<?php echo $label ?>" class="extendMediaButton button light right">
												<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
											</a>
										</p>
									<?php endif;?>
									<div class="sortable-container extendMediaContainer" data-id="<?php echo $id_extend ?>" data-lang="<?php echo $language['lang'] ?>" data-label="<?php echo $label ?>"></div>
								</div>
							<?php endforeach ;?>
						<?php endif;?>
					<?php endif;?>
				<?php endforeach;?>

			</div>

		<?php endif ;?>

	</form>
</div>

<div class="buttons">
	<button id="saveElementFormSubmit<?php echo $id_element; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_element'); ?></button>
</div>


<script type="text/javascript">

// Window Title : Add Element
if ($('titleAddContentElement'))
	$('titleAddContentElement').set('text', '<?php echo $element_definition['title'] ?>');
// Edit mode
else
{
	var title = new Element('h2', {
		'class':'main elements',
		'text': '<?php echo $element_definition['title'] ?>'
	});
	title.inject($('elementDiv<?php echo $id_element; ?>'), 'before');
}

// Back button
if ($('elementAddBackButton'))
{
	$('elementAddBackButton').addEvent('click', function(el)
	{
		ION.HTML('element_definition/get_element_list', {'parent': '<?php echo $parent?>', 'id_parent': '<?php echo $id_parent?>'}, {'update': 'elementAddContainer' });
	});
}

// Save button
$('saveElementFormSubmit<?php echo $id_element; ?>').addEvent('click', function(e)
{
	e.stop();
	
	// New Element : Add current opened parent / parent_id to the form
	if ($('element') && $('id_element<?php echo $id_element; ?>').value == '')
	{
		var parent = $('element').value;
		var id_parent = $('id_' + parent).value;
		
		if (parent && id_parent)
		{
			$('elementParent<?php echo $id_element; ?>').value = parent;
			$('elementIdParent<?php echo $id_element; ?>').value = id_parent;
		}
	}
	
	if ($('elementParent<?php echo $id_element; ?>').value !='' && $('elementIdParent<?php echo $id_element; ?>').value != '')
	{
		// tinyMCE and CKEditor trigerSave
		// mandatory for text save. See how to externalize without make it too complex.
		if (typeof tinyMCE != "undefined")
			tinyMCE.triggerSave();

		// Get the form
		var options = ION.getJSONRequestOptions(
			'element/save',
			$('elementForm<?php echo $id_element; ?>'),
			{
				'onSuccess': function()	{
					ION.closeWindow($('wcontentElement<?php echo $id_element; ?>'))
				}
			}
		);
		
		var r = new Request.JSON(options);
		
		r.send();
	}
	else
	{
		ION.notification('error', Lang.get('ionize_message_element_cannot_be_added_to_parent'));
	}
});

<?php if( ! empty($id_element)) :?>

	var id_element = '<?php echo $id_element; ?>';

	// Extend Media Tab : Load Media List
	$$('#elementForm'+id_element+' .extendMediaTab').each(function(el)
	{
		el.addEvent('click', function(e)
		{
			extendMediaManager.loadMediaList({
				container:		'extendMediaContainer',
				tab:			'extendMediaTab',
				parent: 		'element',
				id_parent: 		id_element,
				id_extend: 		this.getProperty('data-id'),
				lang: 			this.getProperty('data-lang'),
				extend_label: 	this.getProperty('data-label')
			});
		});
		el.click();
	});

	// Add Extend Media button
	$$('#elementForm'+id_element+' .extendMediaButton').each(function(el)
	{
		el.addEvent('click', function(e)
		{
			extendMediaManager.open({
				container:		'extendMediaContainer',
				tab:			'extendMediaTab',
				parent: 		'element',
				id_parent: 		id_element,
				id_extend: 		this.getProperty('data-id'),
				lang: 			this.getProperty('data-lang'),
				extend_label: 	this.getProperty('data-label')
			});
		});
	});

<?php endif;?>


// Tabs
new TabSwapper({
	tabsContainer: 'elementTab<?php echo $UNIQ; ?>',
	sectionsContainer: 'elementTabContent<?php echo $UNIQ; ?>',
	selectedClass: 'selected',
	deselectedClass: '',
	tabs: 'li',
	clickers: 'li a',
	sections: 'div.tabcontent<?php echo $UNIQ; ?>'
});

// Copy Lang data to other languages dynamically
var elements = Array();
<?php foreach($lang_fields as $field) :?>
	<?php if ($field['type'] == '1' || $field['type'] == '2' || $field['type'] == '3') :?>
		elements.push('cf_<?php echo $field['id_extend_field']; ?>');
	<?php endif ;?>
<?php endforeach ;?>
ION.initCopyLang('.copyLang', elements);

// Text editor
ION.initTinyEditors('.tab-element<?php echo $UNIQ; ?>', '#elementTabContent<?php echo $UNIQ; ?> .tinyTextarea', 'small', {height:120});

// Calendars init
ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');

// Resize
// ION.windowResize('contentElement<?php echo $id_element; ?>', {'width':500});

</script>
