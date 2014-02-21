<?php

/**
 * Displays the Element list of a instance of parent container (Editor side)
 * Called by element/get_elements_from_definition()
 *
 */

$elements = $definition['elements'];

$id_def = $definition['id_element_definition'];

$nbLang = count(Settings::get_languages());
$width = (100 / $nbLang);

?>

<ul id="elements<?php echo $id_def; ?>" class="sortable-container">

<?php foreach($elements as $element) :?>

	<?php
	
	$id_element = $element['id_element'];
	
	/*
	 * Identify the first field of each element
	 * $i = 0 : first element, has the link to the edit window
	 * $i = 1 : all childs will be wrapped into a toggler content div
	 *
	 */ 
	$i = 0;
	
	?>

	<li class="sortme element element<?php echo $id_element; ?> p8" id="element<?php echo $id_element; ?>" data-id="<?php echo $id_element; ?>">

		<a class="icon delete right absolute mr10" data-id="<?php echo $id_element; ?>"></a>
        <a class="icon edit right absolute mr35" data-id="<?php echo $id_element; ?>"></a>
		<span class="icon left drag absolute"></span>

		<div style="overflow:hidden;clear:both;" class="ml20 mr20">

			<?php if(count($element['fields']) > 1) :?>
			<span class="toggler right mr40" style="display:block;height:16px;" data-id="<?php echo $id_element; ?>">
				<a class="left" data-id="<?php echo $id_element; ?>"><?php echo lang('ionize_label_see_element_detail'); ?></a>
			</span>
			<?php endif ;?>

			<?php foreach($element['fields'] as $field) :?>


				<?php
				/*
				 * Wraps the childs field into a toggler content div
				 *
				 */
				?>
				<?php if ($i == 1) :?>
					<div class="pt5" id="def_<?php echo $id_element; ?>">
				<?php endif ;?>

				<?php if ($field['type'] != 8) :?>

					<?php if ($field['translated'] != '1') :?>

						<?php
							$id = $field['id_extend_field'];
						?>

						<dl class="small m0 mr50 ml10">
							<dt class="lite">
								<label title="<?php echo $field['description']; ?>"><?php echo $field['label']; ?></label>
							</dt>
							<dd>
								<?php
								/*
								 * Wraps the first field into an edit link
								 *
								 */
								?>
								<?php if ($i == 0) :?>
									<a class="title" data-id="<?php echo $id_element; ?>">
								<?php endif ;?>


								<?php
									$field['content'] = (!empty($field['content'])) ? $field['content'] : $field['default_value'];
								?>

								<?php if ($field['type'] == '1' OR $field['type'] == '2' OR $field['type'] == '3') :?>
									<?php echo substr($field['content'],0, 30); ?>
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
												<?php if (in_array($key, $saved)) :?>
													<?php echo $value; ?>
												<?php endif ;?>
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
												<?php if ($field['content'] == $key) :?>
													<?php echo $value; ?>
												<?php endif ;?>

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
									<?php
										$i = 0;
										foreach($pos as $values)
										{
											$vl = explode(':', $values);
											$key = $vl[0];
											$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
											?>
											<?php if (in_array($key, $saved)) :?>
												<?php echo $value; ?>
											<?php endif ;?>

											<?php
											$i++;
										}
									?>
								<?php endif ;?>

								<!-- Date & Time -->
								<?php if ($field['type'] == '7') :?>

									<?php echo humanize_mdate($field['content'], Settings::get('date_format'). ' %H:%i:%s'); ?>

								<?php endif ;?>




								<?php
								/*
								 * Close the first field edit link wrapper
								 *
								 */
								?>
								<?php if ($i == 0) :?>
									</a>
								<?php endif ;?>

							</dd>
						</dl>

					<?php else :?>

						<dl class="small mr50 ml10 m0">

							<dt class="lite">
								<label title="<?php echo $field['description']; ?>">
									<?php echo $field['label']; ?>
								</label>
							</dt>
							<dd>

							<?php foreach(Settings::get_languages() as $language) :?>

								<div class="left" style="width:<?php echo $width; ?>%;overflow:hidden;">

									<?php $lang = $language['lang']; ?>

									<div class="left w20">
										<img class="mt3 mb3" src="<?php echo admin_style_url(); ?>images/world_flags/flag_<?php echo $lang?>.gif" />
									</div>

									<div class="ml30">

									<?php if (!empty($field[$lang]['content'])) :?>

										<?php
											$field[$lang]['content'] = (!empty($field[$lang]['content'])) ? $field[$lang]['content'] : $field[$lang]['default_value'];
										?>

										<?php if ($field['type'] == '1' OR $field['type'] == '2' OR $field['type'] == '3') :?>
											<?php echo character_limiter($field[$lang]['content'], 30); ?>
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
														<?php if (in_array($key, $saved)) :?>
															<?php echo $value; ?>
														<?php endif ;?>
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
														<?php if ($field[$lang]['content'] == $key) :?>
															<?php echo $value; ?>
														<?php endif ;?>

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
											<?php
												$i = 0;
												foreach($pos as $values)
												{
													$vl = explode(':', $values);
													$key = $vl[0];
													$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
													?>
													<?php if (in_array($key, $saved)) :?>
														<?php echo $value; ?>
													<?php endif ;?>

													<?php
													$i++;
												}
											?>
										<?php endif ;?>

										<!-- Date & Time -->
										<?php if ($field['type'] == '7') :?>

											<?php echo humanize_mdate($field[$lang]['content'], Settings::get('date_format'). ' %H:%i:%s'); ?>

										<?php endif ;?>

									<?php endif ;?>
									</div>

								</div>

								<?php endforeach ;?>

							</dd>
						</dl>

					<?php endif ;?>

					<?php

						$i++;

					?>
				<?php endif ;?>
			<?php endforeach ;?>
			
			<?php
			/*
			 * Closes the toggler content div wrapper
			 *
			 */
			?>
			<?php if ($i > 1) :?>
				</div>
			<?php endif ;?>
			
			
		</div>
	</li>
<?php endforeach ;?>

</ul>


<script type="text/javascript">


	/**
	 * itemManager
	 *
	 */
	var elementsManager<?php echo $id_def; ?> = new ION.ItemManager({
		'container': 'elements<?php echo $id_def; ?>', 
		'element':'element', 
		'parent_element': '<?php echo $parent; ?>', 
		'id_parent':'<?php echo $id_parent; ?>',
		'sortable': true
	});


	// Add toggler to each definition
	<?php if(count($element['fields']) > 1) :?>
	$$('#elements<?php echo $id_def; ?> li.element .toggler').each(function(el)
	{
		ION.initListToggler(el, $('def_' + el.getProperty('data-id')));
	});
	<?php endif ;?>

	// Edit on each element
	$$('#elements<?php echo $id_def; ?> li.element a.title, #elements<?php echo $id_def; ?> li.element .edit').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			var id = this.getProperty('data-id');
			ION.dataWindow(
				'contentElement' + id,
				'ionize_title_edit_content_element',
				'element/edit',
				{width:500, height:350},
				{'id_element': id}
			);
		});
		
		ION.addDragDrop(item, '.folder,.file', 'ION.dropContentElementInPage,ION.dropContentElementInArticle');
	});
	
</script>
