<?php

$tracker_title = ${Settings::get_lang('default')}['title'];
if ($tracker_title == '')
	$tracker_title = $name;

?>

<form name="articleForm" id="articleForm" method="post" action="<?php echo site_url(config_item('admin_url').'/article/save/'.$id_article); ?>">

	<input type="hidden" name="element" id="element" value="article" />
	<input type="hidden" class="data-tracker" name="data_tracker" data-element="article" data-id="<?php echo $id_article; ?>" data-title="<?php echo $tracker_title; ?>" data-url="article/edit/<?php echo $id_page; ?>.<?php echo $id_article; ?>" />
	<input type="hidden" name="id_article" id="id_article" value="<?php echo $id_article; ?>" />
	<input type="hidden" name="rel" id="rel" value="<?php echo $id_page; ?>.<?php echo $id_article; ?>" />
	<input type="hidden" name="created" value="<?php echo $created; ?>" />
	<input type="hidden" name="author" value="<?php echo $author; ?>" />
	<input type="hidden" name="name" id="name" value="<?php echo $name; ?>" />
	<input type="hidden" name="main_parent" id="main_parent" value="<?php echo $main_parent; ?>" />
	<input type="hidden" name="has_url" id="has_url" value="<?php echo $has_url; ?>" />
	
	<!-- JS storing element -->
	<input type="hidden" id="memory" />

	<div id="maincolumn">

		<fieldset class="article-header">

			<!-- Existing article -->
			<?php if( ! empty($id_article)) :?>

				<?php

					$title = ${Settings::get_lang('default')}['title'];
					if ($title == '') $title = $name;

				?>

	            <div id="article-tracker-<?php echo $id_article; ?>"></div>


    	        <h2 class="main article" id="main-title"><?php echo $title; ?></h2>

				<div style="margin: -15px 0pt 20px 72px;">
					<p>
						<?php if ($this->connect->is('super-admins') ) :?>
							<span class="lite">ID : </span>
							<?php echo $id_article; ?>
						<?php endif ;?>

						<?php if( ! empty($breadcrump)) :?>
							| <span class="lite"><?php echo lang('ionize_label_article_context_edition'); ?> : </span><?php echo$breadcrump?>
						<?php endif ;?>
					</p>
				</div>

			<!-- New article -->
			<?php else :?>

				<h2 class="main article" id="main-title"><?php echo lang('ionize_title_new_article'); ?></h2>

				<input type="hidden" name="id_page" id="id_page" value="<?php echo $id_page; ?>" />

				<!-- Where is the article ? -->
				<dl>
					<dt><label><?php echo lang('ionize_label_article_in'); ?></label></dt>
					<dd class="lite"><?php echo $menu; ?>
						<?php foreach ($breadcrumbs as $breadcrumb) :?>
							> <?php echo $breadcrumb['title']; ?>
						<?php endforeach ;?>
					</dd>
				</dl>

				<!-- Ordering -->
				<dl>
					<dt >
						<label for="ordering_select"><?php echo lang('ionize_label_ordering'); ?></label>
					</dt>
					<dd>
						<select name="ordering_select" id="ordering_select" class="select">
							<?php if($id_article) :?>
								<option value="<?php echo $ordering; ?>"><?php echo $ordering; ?></option>
							<?php endif ;?>
							<option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
							<option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
							<option id="ordering_select_after" value="after" <?php if( empty($articles)) :?>style="display:none"<?php endif ;?>><?php echo lang('ionize_label_ordering_after'); ?></option>
						</select>
					</dd>
					<dd>
						<select name="ordering_after" id="ordering_after" style="display:none;" class="select w140 mt5">
							<?php foreach($articles as $article) :?>
								<?php
									$title = ($article['title'] != '') ? $article['title'] : $article['name'];
								?>
								<option value="<?php echo $article['id_article']; ?>"><?php echo $title; ?></option>
							<?php endforeach ;?>
						</select>
					</dd>
				</dl>

				<!-- Online / Offline -->
				<dl class="mb20">
					<dt>
						<label for="online" title="<?php echo lang('ionize_help_article_online'); ?>"><?php echo lang('ionize_label_article_online'); ?></label>
					</dt>
					<dd>
						<div>
							<input id="online" <?php if ($online == 1):?> checked="checked" <?php endif;?> name="online" class="inputcheckbox" type="checkbox" value="1"/>
						</div>
					</dd>
				</dl>

			<?php endif ;?>

            <?php if ( ! empty($id_article)) :?>

                <!-- Modules PlaceHolder -->
                <?php echo get_modules_addons('article', 'main_top'); ?>

            <?php endif ;?>

        	<div id="articleExtendFields">

				<!-- Extend Fields (Main) -->
				<?php foreach($extend_fields as $extend_field) :?>
				
					<?php if ($extend_field['translated'] != '1') :?>
					
						<dl>
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
									<input id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="inputtext" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>" value="<?php echo $extend_field['content'] ; ?>" />
								<?php endif ;?>

								<!-- Textarea -->
								<?php if ($extend_field['type'] == '2') :?>
                               		<textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="autogrow inputtext" name="cf_<?php echo $extend_field['id_extend_field']; ?>"><?php echo $extend_field['content']; ?></textarea>
								<?php endif ;?>

								<!-- Textarea with editor -->
								<?php if ($extend_field['type'] == '3') :?>
									<textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="smallTinyTextarea inputtext" name="cf_<?php echo $extend_field['id_extend_field']; ?>"><?php echo $extend_field['content']; ?></textarea>
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
								
									<input id="cf_<?php echo $extend_field['id_extend_field']; ?>" class="inputtext w120 date" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>" value="<?php echo $extend_field['content'] ; ?>" />
									<a class="icon clearfield date" data-id="cf_<?php echo $extend_field['id_extend_field']; ?>"></a>

								<?php endif ;?>
								
							</dd>
						</dl>	
							
					<?php endif ;?>
				<?php endforeach ;?>

			</div>

		</fieldset>

		<fieldset id="blocks" class="mt10">
	
			<!-- Tabs -->
			<div id="articleTab" class="mainTabs">
				
				<ul class="tab-menu">
					
					<?php foreach(Settings::get_languages() as $language) :?>
					
						<li class="tab_article<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['name']); ?></a></li>
					
					<?php endforeach ;?>
					
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="fileTab"><a><?php echo lang('ionize_label_files'); ?></a></li>
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="musicTab"><a><?php echo lang('ionize_label_music'); ?></a></li>
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="videoTab"><a><?php echo lang('ionize_label_videos'); ?></a></li>
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="pictureTab"><a><?php echo lang('ionize_label_pictures'); ?></a></li>

				</ul>
				<div class="clear"></div>
			
			</div>

			<div id="articleTabContent">

				<!-- Text block -->
				<?php foreach(Settings::get_languages() as $language) :?>
					
				<?php $lang = $language['lang']; ?>

				<div class="tabcontent <?php echo $lang; ?>">

					<!-- Copy data -->
					<p class="clear h15">
						<a class="right icon copy copyLang" rel="<?php echo $lang; ?>" title="<?php echo lang('ionize_label_copy_to_other_languages'); ?>"></a>
					</p>

					<div class="article-header">

						<!-- Online -->
						<?php if(count(Settings::get_languages()) > 1) :?>

							<dl>
								<dt>
									<label for="online_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_article_content_online'); ?>"><?php echo lang('ionize_label_online_in'); ?> <?php echo ucfirst($language['name']); ?></label>
								</dt>
								<dd>
									<input id="online_<?php echo $lang; ?>" <?php if (${$lang}['online'] == 1):?> checked="checked" <?php endif;?> name="online_<?php echo $lang; ?>" class="inputcheckbox" type="checkbox" value="1"/>
								</dd>
							</dl>

						<?php else :?>

							<input id="online_<?php echo $lang; ?>" name="online_<?php echo $lang; ?>" type="hidden" value="1"/>

						<?php endif ;?>

						<!-- title -->
						<dl class="first">
							<dt>
								<label for="title_<?php echo $lang; ?>"><?php echo lang('ionize_label_title'); ?></label>
							</dt>
							<dd>
								<textarea id="title_<?php echo $lang; ?>" name="title_<?php echo $lang; ?>" class="textarea title autogrow" type="text"><?php echo ${$lang}['title']; ?></textarea>
							</dd>
						</dl>

						<!-- Toggler : More : SEO, Online.. -->
	<!--
						<h3 class="toggler toggler-<?php echo $lang; ?>"><?php echo lang('ionize_title_seo'); ?></h3>
	-->
						<!-- sub title -->
						<dl>
							<dt>
								<label for="subtitle_<?php echo $lang; ?>"><?php echo lang('ionize_label_subtitle'); ?></label>
							</dt>
							<dd>
								<textarea id="subtitle_<?php echo $lang; ?>" name="subtitle_<?php echo $lang; ?>" class="textarea text autogrow" type="text"><?php echo ${$lang}['subtitle']; ?></textarea>
								<!-- <a class="icon edit subtitle"></a> -->
							</dd>
						</dl>

						<!-- URL -->
						<dl>
							<dt>
								<label for="url_<?php echo $lang; ?>"><?php echo lang('ionize_label_url'); ?></label>
							</dt>
							<dd>
								<input id="url_<?php echo $lang; ?>" name="url_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo ${$lang}['url']; ?>"/>
							</dd>
						</dl>


						<!-- Meta Title : Browser window title -->
						<dl>
							<dt>
								<label for="meta_title_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_article_window_title'); ?>"><?php echo lang('ionize_label_meta_title'); ?></label>
							</dt>
							<dd>
								<input id="meta_title_<?php echo $lang; ?>" name="meta_title_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo ${$lang}['meta_title']; ?>"/>
							</dd>
						</dl>


						<!-- extend fields goes here... -->
						<?php if ( $has_translated_extend_fields && ! empty($extend_fields)) :?>

							<!--
								<h3 class="toggler toggler-<?php echo $lang; ?>"><?php echo lang('ionize_title_extend_fields'); ?></h3>
							-->

							<div class="element element-<?php echo $lang; ?>">

							<?php foreach($extend_fields as $extend_field) :?>

								<?php if ($extend_field['translated'] == '1') :?>

									<dl>
										<dt>
											<?php
												$label = ( ! empty($extend_field['langs'][Settings::get_lang('default')]['label'])) ? $extend_field['langs'][Settings::get_lang('default')]['label'] : $extend_field['name'];
											?>
											<label for="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" title="<?php echo $extend_field['description']; ?>"><?php echo $label; ?></label>
										</dt>
										<dd>
											<?php
												$extend_field[$lang]['content'] = (!empty($extend_field[$lang]['content'])) ? $extend_field[$lang]['content'] : $extend_field['default_value'];
											?>

											<?php if ($extend_field['type'] == '1') :?>
												<input id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" class="inputtext" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" value="<?php echo $extend_field[$lang]['content']; ?>" />
											<?php endif ;?>

											<!-- Textarea -->
											<?php if ($extend_field['type'] == '2') :?>
												<textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" class="text autogrow inputtext" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>"><?php echo $extend_field[$lang]['content']; ?></textarea>
											<?php endif ;?>

											<!-- Textarea with editor -->
											<?php if ($extend_field['type'] == '3') :?>
												<textarea id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" class="smallTinyTextarea h80" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" rel="<?php echo $lang; ?>"><?php echo $extend_field[$lang]['content']; ?></textarea>
											<?php endif ;?>

											<!-- Checkbox -->
											<?php if ($extend_field['type'] == '4') :?>

												<?php
													$pos = 		explode("\n", $extend_field['value']);
													$saved = 	explode(',', $extend_field[$lang]['content']);
												?>

												<?php
													$i = 0;
													foreach($pos as $values)
													{
														$vl = explode(':', $values);
														$key = $vl[0];
														$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

														?>
														<input type="checkbox" id= "cf_<?php echo $extend_field['id_extend_field'].$i; ?>_<?php echo $lang; ?>" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>[]" value="<?php echo $key; ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?php echo $extend_field['id_extend_field'] . $i; ?>_<?php echo $lang; ?>"><?php echo $value; ?></label></input><br/>
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
														<input type="radio" id= "cf_<?php echo $extend_field['id_extend_field'].$i; ?>_<?php echo $lang; ?>" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" value="<?php echo $key; ?>" <?php if ($extend_field[$lang]['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?php echo $extend_field['id_extend_field'] . $i; ?>_<?php echo $lang; ?>"><?php echo $value; ?></label></input><br/>
														<?php
														$i++;
													}
												?>
											<?php endif ;?>

											<!-- Selectbox -->
											<?php if ($extend_field['type'] == '6' && !empty($extend_field['value'])) :?>

												<?php
													$pos = explode("\n", $extend_field['value']);
													$saved = 	explode(',', $extend_field[$lang]['content']);
												?>
												<select name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>">
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

												<input id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" class="inputtext w120 date" type="text" name="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>" value="<?php echo $extend_field['content'] ; ?>" />
												<a class="icon clearfield date" data-id="cf_<?php echo $extend_field['id_extend_field']; ?>_<?php echo $lang; ?>"></a>

											<?php endif ;?>

										</dd>
									</dl>

								<?php endif ;?>
							<?php endforeach ;?>

							</div><!-- / element1 -->

						<!-- End if extend_fields -->
						<?php endif ;?>

					</div>

					<!-- Text -->
					<h3 class=" toggler-<?php echo $lang; ?> article-header"><?php echo lang('ionize_label_text'); ?></h3>
		
					<div class=" element-<?php echo $lang; ?> mb40">

						<div>
							<textarea id="content_<?php echo $lang; ?>" name="content_<?php echo $lang; ?>" class="tinyTextarea h260" rel="<?php echo $lang; ?>"><?php echo htmlentities(${$lang}['content'], ENT_QUOTES, 'utf-8'); ?></textarea>

							<p class="clear h15">
                                <!--
								<a id="wysiwyg_<?php echo $lang; ?>" class="light button left" onclick="tinymce.execCommand('mceToggleEditor',false,'content_<?php echo $lang; ?>');return false;"><?php echo lang('ionize_label_toggle_editor'); ?></a>
								-->
							</p>

						</div>
					
					</div>

				</div>
				<?php endforeach ;?>
	
	
				<!-- Files -->
				<div class="tabcontent">
				
					<p class="h30">
						<a class="right light button" onclick="javascript:mediaManager.loadMediaList('file');return false;">
							<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
						</a>
						<a class="left light button unlink" onclick="javascript:mediaManager.detachMediaByType('file');return false;">
							<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_files'); ?>
						</a>
					</p>
					
					<ul id="fileContainer" class="sortable-container">
					</ul>
	
				</div>
	
				<!-- Music -->
				<div class="tabcontent">
					
					<p class="h30">
						<a class="right light button" onclick="javascript:mediaManager.loadMediaList('music');return false;">
							<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
						</a>
						<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('music');return false;">
							<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_musics'); ?>
						</a>
					</p>
					
					<ul id="musicContainer" class="sortable-container">
					</ul>
	
				</div>
	
				<!-- Videos -->
				<div class="tabcontent">
				
					<p class="h30">
						<a class="right light button" onclick="javascript:mediaManager.loadMediaList('video');return false;">
							<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
						</a>
						<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('video');return false;">
							<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_videos'); ?>
						</a>
					</p>
					
					<dl class="first">
						<dt>
							<label for="addVideo"><?php echo lang('ionize_label_add_video'); ?></label>
						</dt>
						<dd>
							<textarea id="addVideo" name="addVideo" class="inputtext w300 autogrow left mr5" type="text"></textarea>
							<a id="btnAddVideo" class="left light button">
								<i class="icon-plus"></i><?php echo lang('ionize_button_add_video'); ?>
							</a>
						</dd>
					</dl>
					
					<ul id="videoContainer" class="sortable-container">
					</ul>
	
				</div>
	
				<!-- Pictures -->
				<div class="tabcontent">
				
					<p class="h30">
	<!--					<a class="fmButton right"><img src="<?php echo theme_url(); ?>images/icon_16_plus.png" /> <?php echo lang('ionize_label_attach_media'); ?></a>-->

						<a class="button light right" onclick="javascript:mediaManager.loadMediaList('picture');return false;">
							<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
						</a>
						<a class="button light left" onclick="javascript:mediaManager.detachMediaByType('picture');return false;">
							<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_pictures'); ?>
						</a>
						<?php
						/*
						<a class="button light left" onclick="javascript:mediaManager.initThumbsForParent();return false;">
							<i class="icon-process"></i><?php echo lang('ionize_label_init_all_thumbs'); ?>
						</a>
						*/
						?>
					</p>

					<div id="pictureContainer" class="sortable-container"></div>

				</div>
			</div>

		</fieldset>

	</div>

</form>


<!-- File Manager Form
	 Mandatory for the filemanager
-->
<form name="fileManagerForm" id="fileManagerForm" action="">
	<input type="hidden" name="hiddenFile" />
</form>



<script type="text/javascript">


	ION.initFormAutoGrow();

	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	ION.initToolbox('article_toolbox');

    /**
     * Init the Edit Mode
	 */
    ION.initEditMode('editionModeSwitcher', 'article', '.article-header');

	/**
	 * Article element in each of its parent context
	 * 
	 */
	ION.initDroppable();
	 
	/**
	 * Calendars init
	 *
	 */
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');
    ION.initClearField('#articleForm');


	// Auto-generate Main title
	$$('.tabcontent .title').each(function(input, idx)
	{
		input.addEvent('keyup', function(e)
		{
			$('main-title').set('text', this.value);
		});
	});
	
	 
	// Auto-generates URL
	<?php if ($id_article == '') :?>
		<?php foreach (Settings::get_languages() as $lang) :?>

			ION.initCorrectUrl('title_<?php echo $lang['lang']; ?>', 'url_<?php echo $lang['lang']; ?>');

		<?php endforeach ;?>
	<?php endif; ?>


	// Article ordering :
	// - Show / hide article list depending on Ordering select
	// - Update the article select list after parent change
	if ($('id_page'))
	{
		$('ordering_select').addEvent('change', function(e)
		{
			e.stop();
			var el = e.target;
			if (el.value == 'after'){ $('ordering_after').setStyle('display', 'block');}
			else { $('ordering_after').setStyle('display', 'none');	}
		});
	}

	/**
	 * Copy Lang data to other languages dynamically
	 *
	 */
	ION.initCopyLang('.copyLang', Array('title', 'subtitle', 'url', 'content'));
	

	/** 
	 * Show current tabs
	 */
	var articleTab = new TabSwapper({tabsContainer: 'articleTab', sectionsContainer: 'articleTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'articleTab' });


	/**
	 * TinyEditors
	 * Must be called after tabs init.
	 *
	 */
    ION.initTinyEditors(null, '#articleExtendFields .tinyTextarea');
    ION.initTinyEditors(null, '#articleExtendFields .smallTinyTextarea', 'small', {'height':80});
    ION.initTinyEditors('.tab_article', '#articleTabContent .tinyTextarea');
    ION.initTinyEditors('.tab_article', '#articleTabContent .smallTinyTextarea', 'small', {'height':80});

	<?php if (!empty($id_article)) :?>
	
		// Dates
		/*
		ION.datePicker.options['onClose'] = function()	
		{
			ION.JSON('article/update_field', {'field': ION.datePicker.input.id, 'value': ION.datePicker.input.value, 'type':'date', 'id_article': $('id_article').value});
		}
		*/


		/**
		 * Get Content Elements Tabs & Elements
		 *
		 */
		$('desktop').store('tabSwapper', articleTab);
		ION.getContentElements('article', '<?php echo $id_article; ?>');
		
		
		/**
		 * Add Video button
		 *
		 */
		$('btnAddVideo').addEvent('click', function()
		{
			if ($('addVideo').value !='')
			{
				ION.JSON('media/add_external_media', {
					'type': 'video', 
					'parent': 'article', 
					'id_parent': '<?php echo $id_article; ?>',
					'path': $('addVideo').value
				});
			}
			return false;
		});
		
		/**
		 * Media Manager & tabs events
		 *
		 */
		mediaManager.initParent('article', '<?php echo $id_article; ?>');
		mediaManager.loadMediaList('file');
		mediaManager.loadMediaList('music');
		mediaManager.loadMediaList('picture');
		mediaManager.loadMediaList('video');

	<?php endif ;?>

</script>