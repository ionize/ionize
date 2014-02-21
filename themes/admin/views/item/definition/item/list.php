<?php
/**
 *
 * From the Static Items manager :
 * List of items which belongs to one item definition
 *
 * @receives :
 * 		$id_item_definition
 *		$items
 */

$id_def = $id_item_definition;

$nbLang = count(Settings::get_languages());
$width = (100 / $nbLang);

?>

<ul id="items<?php echo $id_def; ?>" class="sortable-container">

	<?php foreach($items as $item) :?>

		<?php
			$id_item = $item['id_item'];
		?>

		<li class="sortme item p8" id="item<?php echo $id_item; ?>" data-id="<?php echo $id_item; ?>">

			<a class="icon delete right absolute mr10" data-id="<?php echo $id_item; ?>"></a>
			<a class="icon edit right absolute mr35" data-id="<?php echo $id_item; ?>"></a>
			<span class="icon left drag absolute"></span>

			<?php foreach($item['fields'] as $field) :?>

				<?php if ($field['translated'] != '1' && $field['type'] < 8) :?>

					<?php
						$id = $field['id_extend_field'];
					?>

					<dl class="small mr50 ml30 mb0 mt0">
						<dt class="lite">
							<label><?php echo $field['label']; ?></label>
						</dt>
						<dd>

							<span class="title" data-id="<?php echo $id_item; ?>">

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

							</span>

						</dd>
					</dl>

				<?php elseif ($field['type'] < 8) :?>

					<dl class="small mr50 ml30 mb0 mt0">

						<dt class="lite">
							<label>
								<span class="edit title " data-id="<?php echo $id_item; ?>">
									<?php echo $field['label']; ?>
								</span>
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
			<?php endforeach ;?>
		</li>
	<?php endforeach ;?>
</ul>

<script type="text/javascript">

	// itemManager
	var itemsManager<?php echo $id_def; ?> = new ION.ItemManager({
		'container': 'items<?php echo $id_def; ?>',
		'element':'item',
		'sortable': true
	});

	// Edit Item
	$$('#items<?php echo $id_def; ?> a.title, #items<?php echo $id_def; ?> a.edit').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			var id = this.getProperty('data-id');

			ION.formWindow(
				'item' + id,
				'itemForm' + id,
				'ionize_title_edit_item',
				'item/edit',
				{width:600, height:350},
				{'id_item': id}
			);
		});
		// ION.addDragDrop(item, '.folder,.file', 'ION.dropContentElementInPage,ION.dropContentElementInArticle');
	});

</script>

