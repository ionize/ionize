<?php

/**
 * Modal window for Media metadata edition
 *
 */

$pictureSize = NULL;

if($type == 'picture')
{
	$pictureSize = @getimagesize(DOCPATH.$path);
}

?>



<!-- Media summary -->
<div class="summary">

    <div id="media-tracker-<?php echo $id_media; ?>"></div>

	<!-- Picture file -->
	<?php if($type == 'picture') :?>
		<?php
			$thumb_size = (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : '120';
		?>
		<div class="picture" style="float:right;margin:0;">
		<div class="thumb" style="width:<?php echo $thumb_size; ?>px;height:<?php echo $thumb_size; ?>px;background-image:url(<?php echo admin_url(TRUE) . 'media/get_thumb/'.$id_media.'/'.time(); ?>);"></div>
		</div>
	<?php endif ;?>

	<!-- Music file -->
	<?php if($type == 'music') :?>
		<div style="float:right;">
			<embed
                src="<?php echo theme_url(); ?>flash/mp3Player/mp3player_simple.swf?mp3=<?php echo base_url().$path; ?>"
                loop="false"
                menu="false"
                quality="high"
                wmode="transparent"
                width="224"
                height="20"
                name="track_<?php echo $id_media; ?>"
                align="middle"
                allowScriptAccess="sameDomain"
                type="application/x-shockwave-flash"
                pluginspage="http://www.macromedia.com/go/getflashplayer"
            />
		</div>
	<?php endif ;?>
	
	<!-- Video file -->
	<?php if($type == 'video') :?>
		
		<?php if($provider != '') :?>

			<iframe  style="float:right;" width="170" height="145" src="<?php echo $path?>" frameborder="0" allowfullscreen></iframe>
		
		<?php else :?>

			<div style="float:right;"  id="video<?php echo $id_media; ?>">
                <embed
                    flashvars="file=<?php echo base_url().$path?>&autostart=false"
                    allowfullscreen="true"
                    allowscriptaccess="always"
                    id="video<?php echo $id_media; ?>"
                    name="video<?php echo $id_media; ?>"
                    src="<?php echo theme_url(); ?>flash/mediaplayer/player.swf"
                    width="170"
                    height="145"
                />
            </div>

		<?php endif ;?>
	<?php endif ;?>


	<h3><?php echo lang('ionize_title_informations'); ?></h3>

	<div style="margin-right:140px">
		<?php if($provider != '') :?>

			<p class="a-break">
				<?php echo auto_link($path, 'both', true) ;?>
            </p>

		<?php else: ?>

			<p class="a-break">
				<?php echo auto_link(base_url().$path, 'both', true) ;?>
			</p>

			<?php if (file_exists(DOCPATH . $path)) :?>
				<?php echo sprintf('%01.2f', filesize(DOCPATH . $path) / (1024 )); ?> ko
			<?php else :?>
				<?php echo(lang('ionize_exception_no_source_file')) ;?>
			<?php endif ;?>

			<?php if($type == 'picture') :?>
				-
				<?php if ( ! is_null($pictureSize)) :?>
					<?php echo($pictureSize['0']); ?> x <?php echo($pictureSize['1']); ?> px
					<br />
					<a id="imageCropLink<?php echo $id_media; ?>" class="light button mt10">
						<i class="icon-crop"></i>
						<?php echo lang('ionize_label_media_crop_picture'); ?>
					</a>
					<?php endif ;?>
				<?php endif ;?>

		<?php endif ;?>
    </div>

</div>


<?php if ($id_media != '') :?>
	<!-- Modules PlaceHolder -->
	<?php echo get_modules_addons('media', 'main_top'); ?>
<?php endif ;?>


<!-- Media form -->
<form name="mediaForm<?php echo $id_media; ?>" id="mediaForm<?php echo $id_media; ?>" action="media/save">

	<input type="hidden" name="id_media" value="<?php echo $id_media; ?>" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
    <input type="hidden" class="data-tracker" name="data_tracker" data-element="media" data-id="<?php echo $id_media; ?>" data-title="<?php echo $file_name; ?>" data-url="" />

	<!-- Context data -->
	<input type="hidden" name="parent" value="<?php echo $parent; ?>" />
	<input type="hidden" name="id_parent" value="<?php echo $id_parent; ?>" />

	<!-- Lang data -->
	<fieldset id="picture-lang">
		
		<!-- Tabs -->
		<div id="mediaTab<?php echo $UNIQ; ?>" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $l) :?>
					<li class="tab_media<?php if($l['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $l['lang']; ?>"><a><span><?php echo ucfirst($l['name']); ?></span></a></li>
				<?php endforeach ;?>

				<?php if($type == 'picture') :?>
					<li class="right"><a><span><?php echo lang('ionize_title_options'); ?></span></a></li>
				<?php endif ;?>

				<li class="tab_media right" rel="details"><a><span><?php echo lang('ionize_title_details'); ?></span></a></li>
			</ul>
			<div class="clear"></div>
		</div>


		<div id="mediaTabContent<?php echo $UNIQ; ?>">	

			<!-- Translated Meta data -->
			<?php foreach(Settings::get_languages() as $language) :?>

				<?php $lang_code = $language['lang']; ?>

				<div class="tabcontent<?php echo $UNIQ; ?>">

					<!-- title -->
					<dl class="small">
						<dt>
							<label for="title_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>"><?php echo lang('ionize_label_title'); ?></label>
						</dt>
						<dd>
							<input id="title_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>" name="title_<?php echo $lang_code; ?>" class="inputtext" type="text" value="<?php echo ${$lang_code}['title']; ?>"/>
							<a class="icon clearfield" data-id="title_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>"></a>
						</dd>
					</dl>

					<?php if(pathinfo(FCPATH.$path, PATHINFO_EXTENSION) == 'mp3') :?>

					<dl class="small mt10">
						<dt>
							<label</label>
						</dt>
						<dd class="lite"><?php echo lang('ionize_message_alt_desc_for_mp3'); ?></dd>
					</dl>
					<?php endif ;?>

					<!-- alternative text -->
					<dl class="small">
						<dt>
							<label for="alt_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>"><?php echo lang('ionize_label_alt'); ?></label>
						</dt>
						<dd>
							<input id="alt_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>" name="alt_<?php echo $lang_code; ?>" class="inputtext" type="text" value="<?php echo ${$lang_code}['alt']; ?>"/>
							<a class="icon clearfield" data-id="alt_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>"></a>
						</dd>
					</dl>

					<!-- description -->
					<dl class="small">
						<dt>
							<label for="description_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>"><?php echo lang('ionize_label_description'); ?></label>
						</dt>
						<dd>
							<input id="description_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>" name="description_<?php echo $lang_code; ?>" class="inputtext" type="text" value="<?php echo ${$lang_code}['description']; ?>"/>
							<a class="icon clearfield" data-id="description_<?php echo $lang_code; ?><?php echo $type.$id_media; ?>"></a>
						</dd>
					</dl>

					<!-- extend fields goes here... -->
					<?php foreach($extend_fields as $extend_field) :?>
						<?php if ($extend_field['translated'] == '1') :?>

							<dl class="small">
								<dt>
									<?php
										$label = ( ! empty($extend_field['langs'][Settings::get_lang('default')]['label'])) ? $extend_field['langs'][Settings::get_lang('default')]['label'] : $extend_field['name'];
									?>
									<label for="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>" title="<?php echo $extend_field['description']; ?>"><?php echo $label; ?></label>
								</dt>
								<dd>
									<?php if ($extend_field['type'] == '1') :?>
										<input id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>" class="inputtext w340" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>" value="<?php echo $extend_field[$lang_code]['content']; ?>" />
									<?php endif ;?>

                                    <!-- Textarea -->
									<?php if ($extend_field['type'] == '2') :?>
                                    <textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>" class="text autogrow inputtext" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>"><?php echo $extend_field[$lang_code]['content']; ?></textarea>
									<?php endif ;?>

                                    <!-- Textarea with editor -->
									<?php if ($extend_field['type'] == '3') :?>
                                    <textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>" class="smallTinyTextarea h80" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang_code; ?>" rel="<?php echo $lang_code; ?>"><?php echo $extend_field[$lang_code]['content']; ?></textarea>
									<?php endif ;?>

								</dd>
							</dl>

						<?php endif ;?>
					<?php endforeach ;?>

				</div>
			<?php endforeach ;?>

			<!-- Thumbnails preferences -->
			<?php if($type == 'picture') :?>
				<div class="tabcontent<?php echo $UNIQ; ?>">
					
					<!-- Thumbnail square crop area -->
					<dl class="small">
						<dt><?php echo lang('ionize_label_square_crop_area'); ?>&nbsp;&nbsp;</dt>
						<dd>
							<input id="square_crop_<?php echo $id_media; ?>_1" name="square_crop" type="radio" value="tl"<?php if ($square_crop == 'tl'): ?> checked="checked"<?php endif; ?>><label for="square_crop_<?php echo $id_media; ?>_1"><?php echo lang('ionize_label_top_left'); ?></label></input><br />
							<input id="square_crop_<?php echo $id_media; ?>_2" name="square_crop" type="radio" value="m"<?php if ($square_crop == 'm'): ?> checked="checked"<?php endif; ?>><label for="square_crop_<?php echo $id_media; ?>_2"><?php echo lang('ionize_label_middle'); ?></label></input><br />
							<input id="square_crop_<?php echo $id_media; ?>_3" name="square_crop" type="radio" value="br"<?php if ($square_crop == 'br'): ?> checked="checked"<?php endif; ?>><label for="square_crop_<?php echo $id_media; ?>_3"><?php echo lang('ionize_label_bottom_right'); ?></label></input>
						</dd>
					</dl>

				</div>
			<?php endif ;?>
			
			<div class="tabcontent<?php echo $UNIQ; ?>">

				<!-- Lang display -->
				<dl class="small">
					<dt><label for="lang_<?php echo $type.$id_media; ?>"><?php echo lang('ionize_label_media_limit_display_to_lang'); ?></label></dt>
					<dd>
						<input type="radio" name="lang_display" id="display_all" value="" <?php if($context_data['lang_display'] == ''):?>checked="checked"<?php endif ;?>/><label for="display_all"><?php echo lang('ionize_label_media_no_limit_display'); ?></label>
						<?php foreach(Settings::get_languages() as $language) :?>
						<input id="display_<?php echo $language['lang']; ?>" type="radio" name="lang_display" value="<?php echo $language['lang']; ?>"  <?php if($context_data['lang_display'] == $language['lang']):?>checked="checked"<?php endif ;?>/><label for="display_<?php echo $language['lang']; ?>"><img alt="<?php echo $language['lang']; ?>" src="<?php echo theme_url(); ?>/images/world_flags/flag_<?php echo $language['lang']; ?>.gif" /></label>
						<?php endforeach; ?>
					</dd>
				</dl>


				<!-- Copyright -->
				<dl class="small">
					<dt><label for="copyright"><?php echo lang('ionize_label_copyright'); ?></label></dt>
					<dd>
						<input id="copyright_<?php echo $type.$id_media; ?>" name="copyright" class="inputtext" type="text" value="<?php echo $copyright; ?>" />
						<a class="icon clearfield" data-id="copyright_<?php echo $type.$id_media; ?>"></a>
					</dd>
				</dl>
			
				<!-- Link (URL) -->
				<dl class="small">
					<dt><label for="link"><?php echo lang('ionize_label_link'); ?></label></dt>
					<dd>
						<input id="link_<?php echo $type.$id_media; ?>" name="link" type="text" class="inputtext" value="<?php echo $link; ?>" />
						<a class="icon clearfield" data-id="link_<?php echo $type.$id_media; ?>"></a>
					</dd>
				</dl>

				<!-- Date -->
				<dl class="small">
					<dt><label for="date_<?php echo $type.$id_media; ?>"><?php echo lang('ionize_label_date'); ?></label></dt>
					<dd>
						<input id="date_<?php echo $type.$id_media; ?>" name="date" type="text" class="inputtext date" value="<?php echo humanize_mdate($date, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
						<a class="icon clearfield date" data-id="date_<?php echo $type.$id_media; ?>"></a>
					</dd>
				</dl>


				<!-- Extend Fields (Main) -->
				<?php foreach($extend_fields as $extend_field) :?>
				
						<?php if ($extend_field['translated'] != '1') :?>
					
						<dl class="small">
							<dt>
								<?php
									$label = ( ! empty($extend_field['langs'][Settings::get_lang('default')]['label'])) ? $extend_field['langs'][Settings::get_lang('default')]['label'] : $extend_field['name'];
								?>
								<label for="cf_<?php echo $extend_field['id_extend_field']; ?>" title="<?php echo $extend_field['description']; ?>"><?php echo $label; ?></label>
							</dt>
							<dd>
								<?php
									$extend_field['content'] = ($extend_field['content'] != '') ? $extend_field['content'] : $extend_field['default_value'];
								?>
							
								<?php if ($extend_field['type'] == '1') :?>
									<input id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="inputtext w200" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>" value="<?php echo $extend_field['content']; ?>" />
								<?php endif ;?>
								
                                <!-- Textarea -->
								<?php if ($extend_field['type'] == '2') :?>
                                	<textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="autogrow inputtext" name="cf_<?php echo $extend_field['id_extend_field']; ?>"><?php echo $extend_field['content']; ?></textarea>
								<?php endif ;?>

                                <!-- Textarea with editor -->
								<?php if ($extend_field['type'] == '3') :?>
                                	<textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="smallTinyTextarea inputtext" name="cf_<?php echo $extend_field['id_extend_field']; ?>" rel="details"><?php echo $extend_field['content']; ?></textarea>
								<?php endif ;?>


								<!-- Checkbox -->
								<?php if ($extend_field['type'] == '4') :?>
									
									<?php
										$pos = 		explode("\n", $extend_field['value']);
										$saved = 	explode(',', $extend_field['content']);
									?>
									<?php
										$i = 0; 
										foreach($pos as $values)
										{
											$vl = explode(':', $values);
											$key = $vl[0];
											$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
			
											?>
											<input type="checkbox" id= "cf_<?php echo $extend_field['id_extend_field'].$i; ?>" name="cf_<?php echo $extend_field['id_extend_field']; ?>[]" value="<?php echo $key; ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?php echo $extend_field['id_extend_field'] . $i; ?>"><?php echo $value; ?></label></input><br/>
											<?php
											$i++;
										}
									?>
								<?php endif ;?>
								
								<!-- Radio -->
								<?php if ($extend_field['type'] == '5') :?>
									
									<?php
										$pos = explode("\n", $extend_field['value']);
									?>
									<?php
										$i = 0; 
										foreach($pos as $values)
										{
											$vl = explode(':', $values);
											$key = $vl[0];
											$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
			
											?>
											<input type="radio" id= "cf_<?php echo $extend_field['id_extend_field'].$i; ?>" name="cf_<?php echo $extend_field['id_extend_field']; ?>" value="<?php echo $key; ?>" <?php if ($extend_field['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?php echo $extend_field['id_extend_field'] . $i; ?>"><?php echo $value; ?></label></input><br/>
											<?php
											$i++;
										}
									?>
								<?php endif ;?>
								
								<!-- Selectbox -->
								<?php if ($extend_field['type'] == '6' && !empty($extend_field['value'])) :?>
									
									<?php									
										$pos = explode("\n", $extend_field['value']);
										$saved = 	explode(',', $extend_field['content']);
									?>
									<select name="cf_<?php echo $extend_field['id_extend_field']; ?>">
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
								<?php if ($extend_field['type'] == '7') :?>
								
									<input id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="inputtext w120 date" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>" value="<?php echo $extend_field['content']; ?>" />
									<a class="icon clearfield date" data-id="cf_<?php echo $extend_field['id_extend_field']; ?>"></a>

								<?php endif ;?>
								
							</dd>
						</dl>	
							
					<?php endif ;?>

					<?php endforeach ;?>

			</div>
		
		</div>
		
	</fieldset>


</form>

<div class="buttons">
	<button id="bSave<?php echo $type.$id_media; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancel<?php echo $type.$id_media; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>


<script type="text/javascript">

	/**
	 * Calendars init
	 *
	 */
	ION.initDatepicker();

	/**
	 * Clear Field Init
	 */
	ION.initClearField('#mediaForm<?php echo $id_media; ?>');

	/** 
	 * Tabs init
	 *
	 */
	new TabSwapper({tabsContainer: 'mediaTab<?php echo $UNIQ; ?>', sectionsContainer: 'mediaTabContent<?php echo $UNIQ; ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent<?php echo $UNIQ; ?>' });

    /**
     * TinyEditors
     * Must be called after tabs init.
     *
     */
    ION.initTinyEditors('.tab_media', '#mediaTabContent<?php echo $UNIQ; ?> .tinyTextarea');
    ION.initTinyEditors('.tab_media', '#mediaTabContent<?php echo $UNIQ; ?> .smallTinyTextarea', 'small', {'height':80});


    /**
	 * Opens the crop window if picture
	 *
	 */
	
	<?php if ( ! is_null($pictureSize)) :?>
	if (typeOf($('imageCropLink<?php echo $id_media; ?>')) != 'null')
	{
		$('imageCropLink<?php echo $id_media; ?>').addEvent('click', function()
		{
			// Should be : 'maximizable': true, 
			ION.dataWindow('ImageCrop<?php echo $id_media; ?>', Lang.get('ionize_label_media_crop_picture'), 'media/get_crop/<?php echo $id_media; ?>', {width:660, height:480, padding:0});
		});
	}
	<?php endif ;?>

</script>
