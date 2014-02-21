<?php
/**
 * Used to create on item instance based on its definition
 * When saved, creates an item instance through : item/save
 *
 * @receives:
 * 		$item_definition
 *
 */

// These values are empty when adding a new item
$id_item = ( !empty($id_item)) ? $id_item : '';
$id_item_definition = $item_definition['id_item_definition'];

?>

<h2 class="main items"><?php echo($item_definition['title_item']) ?></h2>
<?php if ( User()->is('super-admin') && ! empty($id_item)) :?>
	<div class="main subtitle">
		<p>
			<span class="lite">ID : </span> <?php echo $id_item; ?> |
			<span class="lite">Key : </span> <?php echo $item_definition['name'] ?>
		</p>
	</div>
<?php endif ;?>

<div>
	<form name="itemForm" id="itemForm<?php echo $id_item; ?>" method="post" action="<?php echo admin_url() ?>item/save">

		<input type="hidden" name="id_item" value="<?php echo $id_item; ?>" />
		<input type="hidden" name="id_item_definition" value="<?php echo $item_definition['id_item_definition']; ?>" />
		<input type="hidden" name="reload" id="reloadItem<?php echo $id_item; ?>" value="0" />

		<!-- Ordering : First or last (or Element one if exists ) -->
		<?php if( empty($id_item)) :?>
			<dl class="small mb10">
				<dt>
					<label for="ordering"><?php echo lang('ionize_label_ordering'); ?></label>
				</dt>
				<dd>
					<select name="ordering" id="ordering<?php echo $id_item; ?>" class="select">
						<?php if( ! empty($id_item)) :?>
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
					$label = ( ! empty($field['langs'][Settings::get_lang('default')]['label'])) ? $field['langs'][Settings::get_lang('default')]['label'] : $field['name'];
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

							<input id="cf_<?php echo $id_extend; ?>" class="inputtext w120 date" type="text" name="cf_<?php echo $id_extend; ?>" value="<?php echo $field['content'] ; ?>" data-item="item" data-id="<?php echo $id_extend; ?>" />

						<?php endif ;?>

					</dd>
				</dl>

			<?php endif ;?>
		<?php endforeach ;?>


		<?php if( ! empty($lang_fields) OR $has_media_fields == TRUE) :?>

			<!-- Tabs -->
			<div id="itemTab<?php echo $UNIQ; ?>" class="mainTabs">
				<ul class="tab-menu">

					<?php if ($has_lang_fields) :?>
						<?php foreach(Settings::get_languages() as $language) :?>
							<li class="tab-item<?php echo $UNIQ; ?> <?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><span><?php echo ucfirst($language['name']); ?></span></a></li>
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
								<li class="extendMediaTab left<?php if( empty($id_item)) :?> inactive<?php endif ;?>" data-id="<?php echo $id_extend ?>"><a><?php echo $label ?></a></li>
							<?php else:?>
								<?php foreach(Settings::get_languages() as $language) :?>
									<li class="extendMediaTab left<?php if( empty($id_item)) :?> inactive<?php endif ;?>" data-id="<?php echo $id_extend ?>" data-lang="<?php echo $language['lang']; ?>"><a><?php echo $label . ' ' . ucfirst($language['lang']); ?></a></li>
								<?php endforeach ;?>
							<?php endif;?>
						<?php endif;?>
					<?php endforeach;?>

				</ul>
				<div class="clear"></div>
			</div>

			<div id="itemTabContent<?php echo $UNIQ; ?>">

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
								$label = ( ! empty($field['langs'][$lang]['label'])) ? $field['langs'][$lang]['label'] : $field['name'];
								$label_title = User()->is('super-admin') ? 'Key : ' . $field['name'] : ($field['description'] != '' ? $field['description'] : '');
								$field[$lang]['content'] = (!empty($field[$lang]['content'])) ? $field[$lang]['content'] : $field['default_value'];
								?>

								<?php if ($field['type']< 8) :?>

									<dl class="small">
										<dt>
											<label for="cf_<?php echo $id_extend; ?>_<?php echo $lang; ?>" <?php if ( ! empty($label_title)) :?>title="<?php echo $label_title; ?>"<?php endif ;?>><?php echo $label; ?></label>
										</dt>
										<dd>
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
								<?php if( ! empty($id_item)) :?>
									<p class="h30">
										<?php if (User()->is('super-admin')) :?>
											<span class="lite">Extend code : <strong><?php echo  $extend['name']?></strong></span>
										<?php endif ;?>
										<a data-id="<?php echo $id_extend ?>" data-label="<?php echo $label ?>" class="extendMediaButton button light right">
											<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
										</a>
									</p>
								<?php endif;?>
								<?php if ( empty($id_item)) :?>
									<?php echo lang('ionize_message_please_save_first') ?>
								<?php endif ;?>

								<div class="sortable-container extendMediaContainer" data-id="<?php echo $id_extend ?>" data-label="<?php echo $label ?>"></div>
							</div>
						<?php else:?>
							<?php foreach(Settings::get_languages() as $language) :?>
								<div class="tabcontent<?php echo $UNIQ; ?>">
									<?php if( ! empty($id_item)) :?>
										<p class="h30">
											<?php if (User()->is('super-admin')) :?>
												<span class="lite">Extend code : <strong><?php echo  $extend['name']?></strong></span>
											<?php endif ;?>
											<a data-id="<?php echo $id_extend ?>" data-lang="<?php echo $language['lang'] ?>" data-label="<?php echo $label ?>" class="extendMediaButton button light right">
												<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
											</a>
										</p>
									<?php endif;?>
									<?php if ( empty($id_item)) :?>
										<?php echo lang('ionize_message_please_save_first') ?>
									<?php endif ;?>
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
	<button class="button yes right" id="bSaveitem<?php echo $id_item; ?>" type="button" ><?php echo lang('ionize_button_save_close'); ?></button>
	<button class="button green right ml10" id="bSaveAndStay<?php echo $id_item; ?>" type="button" ><?php echo lang('ionize_button_save'); ?></button>
	<button class="button no right" type="button" id="bCancelitem<?php echo $id_item ?>"><?php echo lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	var id_item = '<?php echo $id_item; ?>';
	var uniq = '<?php echo $UNIQ; ?>';

	// Saves and re-opens if the item is new
	$('bSaveAndStay' + id_item).addEvent('click', function()
	{
		var reload = $('reloadItem' + id_item);
		reload.set('value', 1);

		ION.JSON(
			$('itemForm' + id_item).getAttribute('action'),
			$('itemForm' + id_item),
			{
				onSuccess:function()
				{
					reload.set('value', 0);
				}
			}
		);

		if (id_item == '')
		{
			var parent = $('itemForm' + id_item).getParent('.mocha');
			parent.close();
		}
	});

	<?php if( ! empty($id_item)) :?>

		var itemOptions = {
			container:		'extendMediaContainer',
			tab:			'extendMediaTab',
			parent: 		'item',
			id_parent: 		id_item
		};

		// Extend Media Tab : Load Media List
		$$('#itemForm'+id_item+' .extendMediaTab').each(function(el)
		{
			var options = Object.clone(itemOptions);
			options['id_extend'] = el.getProperty('data-id');
			options['lang'] = el.getProperty('data-lang');
			options['extend_label'] = el.getProperty('data-label');

			extendMediaManager.loadMediaList(options);

			el.addEvent('click', function()
			{
				var opt = Object.clone(itemOptions);
				opt['id_extend'] = this.getProperty('data-id');
				opt['lang'] = this.getProperty('data-lang');
				opt['extend_label'] = this.getProperty('data-label');

				extendMediaManager.init(opt);
			});
		});

		// Add Extend Media button
		$$('#itemForm'+id_item+' .extendMediaButton').each(function(el)
		{
			el.addEvent('click', function()
			{
				var opt = Object.clone(itemOptions);
				opt['id_extend'] = this.getProperty('data-id');
				opt['lang'] = this.getProperty('data-lang');
				opt['extend_label'] = this.getProperty('data-label');

				console.log(opt);



				extendMediaManager.open(opt);
			});
		});

	<?php endif;?>


	// Tabs
	new TabSwapper({
		tabsContainer: 'itemTab'+uniq,
		sectionsContainer: 'itemTabContent'+uniq,
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent'+uniq,
		cookieName: 'itemTab'
	});


	// Text editor
	ION.initTinyEditors(
		'.tab-item'+uniq,
		'#itemTabContent'+uniq+' .tinyTextarea',
		'small', {height:120}
	);

	// Calendars init
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');

	// Resize
	// ION.windowResize('item'+id_item, {'width':500});

</script>