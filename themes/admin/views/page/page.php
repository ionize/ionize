<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @subpackage	Views
 * @category	Page
 * @author		Ionize Dev Team
 *
 */

?>
<form name="pageForm" id="pageForm" method="post" action="<?= admin_url() . 'page/save'?>">

	<input type="hidden" name="element" id="element" value="page" />
	<input type="hidden" name="action" id="action" value="save" />
	<input type="hidden" name="id_menu" value="<?= $id_menu ?>" />
	<input type="hidden" name="created" value="<?= $created ?>" />
	<input type="hidden" name="id_page" id="id_page" value="<?= $id_page ?>" />
	<input type="hidden" name="rel" id="rel" value="<?= $id_page ?>" />
	<input type="hidden" name="name" id="name" value="<?= $name ?>" />
	<input type="hidden" id="origin_id_parent" value="<?= $id_parent ?>" />
	<input type="hidden" id="origin_id_subnav" value="<?= $id_subnav ?>" />

	<?php if ($id_page != '') :?>
		<input type="hidden" name="online" value="<?= $online ?>" class="online<?= $id_page ?>" />
	<?php endif ;?>


	<div id="maincolumn" class="">
		
		<fieldset>
				
		<?php if( ! empty($id_page)) :?>
			
			<?php
				
				$title = ${Settings::get_lang('default')}['title'];

				if ($title == '') $title = ${Settings::get_lang('default')}['url'];
			
			?>

			<h2 class="main page" id="main-title"><?= $title ?></h2>
			
			<!-- Breadcrumb -->
			<div style="margin: -15px 0pt 20px 72px;">
				<p>
					<?php if ($this->connect->is('super-admins') ) :?>
						<span class="lite">ID : </span>
						<?= $id_page ?> |
					<?php endif ;?>
					<span class="lite"></span><?=$breadcrump?>
				</p>
			</div>
			
		<?php else :?>
			
			<h2 class="main page" id="main-title"><?= lang('ionize_title_new_page') ?></h2>
			
			<!-- Menu -->
			<dl class="mt20">
				<dt>
					<label for="id_menu"><?= lang('ionize_label_menu') ?></label>
				</dt>
				<dd>
					<?= $menus ?>
				</dd>
			</dl>	

			<!-- Parent -->
			<dl>
				<dt>
					<label for="id_parent"><?= lang('ionize_label_parent') ?></label>
				</dt>
				<dd>
					<div id ="parentSelectContainer"></div>
				</dd>
			</dl>	

			<!-- View -->
			<?php if (isset($views)) :?>
				<dl>
					<dt>
						<label for="view"><?= lang('ionize_label_view') ?></label>
					</dt>
					<dd>
						<?= $views ?>
					</dd>
				</dl>
			<?php endif ;?>
		
			<!-- Online / Offline -->
			<dl>
				<dt>
					<label for="online" title="<?= lang('ionize_help_page_online') ?>"><?= lang('ionize_label_page_online') ?></label>
				</dt>
				<dd>
					<div>
						<input id="online" <?php if ($online == 1):?> checked="checked" <?php endif;?> name="online" class="inputcheckbox online<?= $id_page ?>" type="checkbox" value="1"/>
					</div>
				</dd>
			</dl>

			<!-- Appears as menu item in menu ? -->
			<dl>
				<dt>
					<label for="appears" title="<?= lang('ionize_help_appears') ?>"><?= lang('ionize_label_appears') ?></label>
				</dt>
				<dd>
					<input id="appears" name="appears" type="checkbox" class="inputcheckbox" <?php if ($appears == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>


		<?php endif ;?>

			<!-- extend fields goes here... -->
				<?php foreach($extend_fields as $extend_field) :?>
				
					<?php if ($extend_field['translated'] != '1') :?>
					
						<dl>
							<dt>
								<?php
									$label = ( ! empty($extend_field['langs'][Settings::get_lang('default')]['label'])) ? $extend_field['langs'][Settings::get_lang('default')]['label'] : $extend_field['name'];
								?>
								<label for="cf_<?= $extend_field['id_extend_field'] ?>" title="<?= $extend_field['description'] ?>"><?= $label ?></label>
							</dt>
							<dd>
								<?php
									$extend_field['content'] = (!empty($extend_field['content'])) ? $extend_field['content'] : $extend_field['default_value'];
								?>
							
								<?php if ($extend_field['type'] == '1') :?>
									<input id="cf_<?= $extend_field['id_extend_field'] ?>" class="inputtext" type="text" name="cf_<?= $extend_field['id_extend_field'] ?>" value="<?= $extend_field['content']  ?>" />
								<?php endif ;?>
								
								<?php if ($extend_field['type'] == '2' OR $extend_field['type'] == '3') :?>
									<textarea id="cf_<?= $extend_field['id_extend_field'] ?>" class="<?php if($extend_field['type'] == '3'):?> tinyTextarea <?php endif ;?> inputtext h80" name="cf_<?= $extend_field['id_extend_field'] ?>"><?= $extend_field['content'] ?></textarea>
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
											<input type="checkbox" id= "cf_<?= $extend_field['id_extend_field'].$i ?>" name="cf_<?= $extend_field['id_extend_field'] ?>[]" value="<?= $key ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?= $extend_field['id_extend_field'] . $i ?>"><?= $value ?></label></input><br/>
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
											<input type="radio" id= "cf_<?= $extend_field['id_extend_field'].$i ?>" name="cf_<?= $extend_field['id_extend_field'] ?>" value="<?= $key ?>" <?php if ($extend_field['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?= $extend_field['id_extend_field'] . $i ?>"><?= $value ?></label></input><br/>
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
									<select name="cf_<?= $extend_field['id_extend_field']?>">
									<?php
										$i = 0; 
										foreach($pos as $values)
										{
											$vl = explode(':', $values);
											$key = $vl[0];
											$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
											?>
											<option value="<?= $key ?>" <?php if (in_array($key, $saved)) :?> selected="selected" <?php endif ;?>><?= $value ?></option>
											<?php
											$i++;
										}
									?>
									</select>
								<?php endif ;?>

								<!-- Date & Time -->
								<?php if ($extend_field['type'] == '7') :?>
								
									<input id="cf_<?= $extend_field['id_extend_field'] ?>" class="inputtext w120 date" type="text" name="cf_<?= $extend_field['id_extend_field'] ?>" value="<?= $extend_field['content']  ?>" />
									
								<?php endif ;?>
								
							</dd>
						</dl>	
							
					<?php endif ;?>
				<?php endforeach ;?>

		</fieldset>


		<fieldset class="mt10">
	

			<!-- Tabs -->
			<div id="pageTab" class="mainTabs">
				
				<ul class="tab-menu">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						<li <?php if($language['def'] == '1') :?> class="dl"<?php endif ;?>><a><?= ucfirst($language['name']) ?></a></li>
					<?php endforeach ;?>
					
					<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="fileTab"><a><?= lang('ionize_label_files') ?></a></li>
					<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="musicTab"><a><?= lang('ionize_label_music') ?></a></li>
					<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="videoTab"><a><?= lang('ionize_label_videos') ?></a></li>
					<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="pictureTab"><a><?= lang('ionize_label_pictures') ?></a></li>

				</ul>
				<div class="clear"></div>
			
			</div>


			<div id="pageTabContent">
			

			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $language) :?>
		
				<?php
					$lang = $language['lang'];

					// URL to the page
					$url = $lang_url = NULL;

					if ( ! empty($urls))
					{
						foreach($urls as $url_array)
						{
							if($url_array['lang'] == $lang)
							{
								$url = $url_array['path'];
								$lang_url = $lang . '/' . $url_array['path'];
							}
						}
					}
				?>
				
				<div class="tabcontent">
		
					<p class="clear h15">
						<a class="right icon copy copyLang" rel="<?= $lang ?>" title="<?= lang('ionize_label_copy_to_other_languages') ?>"></a>
					</p>

					<!-- Online -->
					<?php if(count(Settings::get_languages()) > 1) :?>

						<dl>
							<dt>
								<label for="online_<?= $lang ?>" title="<?= lang('ionize_help_page_content_online') ?>"><?= lang('ionize_label_page_content_online') ?></label>
							</dt>
							<dd>
								<input id="online_<?= $lang ?>" <?php if (${$lang}['online'] == 1):?> checked="checked" <?php endif;?> name="online_<?= $lang ?>" class="inputcheckbox" type="checkbox" value="1"/>
							</dd>
						</dl>
					
					<?php else :?>
					
						<input id="online_<?= $lang ?>" name="online_<?= $lang ?>" type="hidden" value="1"/>
					
					<?php endif ;?>

					<!-- title -->
					<dl class="first">
						<dt>
							<label for="title_<?= $lang ?>"><?= lang('ionize_label_title') ?></label>
						</dt>
						<dd>
							<textarea id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="textarea title autogrow" type="text" title="<?= lang('ionize_label_title') ?>"><?= ${$lang}['title'] ?></textarea>
						</dd>
					</dl>

					<!-- Sub title -->
					<dl>
						<dt>
							<label for="subtitle_<?= $lang ?>"><?= lang('ionize_label_subtitle') ?></label>
						</dt>
						<dd>
							<textarea id="subtitle_<?= $lang ?>" name="subtitle_<?= $lang ?>" class="textarea autogrow" type="text"><?= ${$lang}['subtitle'] ?></textarea>
						</dd>
					</dl>


					<!-- URL -->
					<dl class="mt15">
						<dt>
							<label for="url_<?= $lang ?>" title="<?= lang('ionize_help_page_url') ?>"><?= lang('ionize_label_url') ?></label>
						</dt>
						<dd>
							<input id="url_<?= $lang ?>" name="url_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['url'] ?>" title="<?= lang('ionize_help_page_url') ?>" />

							<?php if( ! is_null($lang_url)) :?>
								<a href="<?= base_url()?><?= $lang_url ?>" target="_blank" title="<?= lang('ionize_label_see_online') ?>"><img src="<?= base_url()?><?= Theme::get_theme_path() ?>images/icon_16_right.png" /></a>
								<br/><?= lang('ionize_label_full_url') ?> : <i class="selectable">/<?= $lang_url ?></i>
							<?php endif; ?>

						</dd>
					</dl>

					<!-- Nav title -->
					<dl>
						<dt>
							<label for="nav_title_<?= $lang ?>" title="<?= lang('ionize_help_page_nav_title') ?>"><?= lang('ionize_label_nav_title') ?></label>
						</dt>
						<dd>
							<input id="nav_title_<?= $lang ?>" name="nav_title_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['nav_title'] ?>"/>
						</dd>
					</dl>

					<!-- Meta title : used for browser window title -->
					<dl class="mb20">
						<dt>
							<label for="meta_title_<?= $lang ?>" title="<?= lang('ionize_help_page_window_title') ?>"><?= lang('ionize_label_meta_title') ?></label>
						</dt>
						<dd>
							<input id="meta_title_<?= $lang ?>" name="meta_title_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['meta_title'] ?>"/>
						</dd>
					</dl>



					<!-- extend fields goes here... -->
						<?php foreach($extend_fields as $extend_field) :?>
							<?php if ($extend_field['translated'] == '1') :?>
							
								<dl>
									<dt>
										<?php
											$label = ( ! empty($extend_field['langs'][Settings::get_lang('default')]['label'])) ? $extend_field['langs'][Settings::get_lang('default')]['label'] : $extend_field['name'];
										?>
										<label for="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" title="<?= $extend_field['description'] ?>"><?= $label ?></label>
									</dt>
									<dd>
										<?php
											$extend_field[$lang]['content'] = (!empty($extend_field[$lang]['content'])) ? $extend_field[$lang]['content'] : $extend_field['default_value'];
										?>

										<?php if ($extend_field['type'] == '1') :?>
											<input id="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" class="inputtext" type="text" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" value="<?= $extend_field[$lang]['content'] ?>" />
										<?php endif ;?>
										
										<?php if ($extend_field['type'] == '2' || $extend_field['type'] == '3') :?>
											<textarea id="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" class="inputtext <?php if($extend_field['type'] == '3'):?> tinyTextarea <?php else:?>autogrow <?php endif ;?>" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>"><?= $extend_field[$lang]['content'] ?></textarea>
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
													<input type="checkbox" id= "cf_<?= $extend_field['id_extend_field'].$i ?>_<?= $lang ?>" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>[]" value="<?= $key ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?= $extend_field['id_extend_field'] . $i ?>_<?= $lang ?>"><?= $value ?></label></input><br/>
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
													<input type="radio" id= "cf_<?= $extend_field['id_extend_field'].$i ?>_<?= $lang ?>" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" value="<?= $key ?>" <?php if ($extend_field[$lang]['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?= $extend_field['id_extend_field'] . $i ?>_<?= $lang ?>"><?= $value ?></label></input><br/>
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
											<select name="cf_<?= $extend_field['id_extend_field']?>_<?= $lang ?>">
											<?php
												$i = 0; 
												foreach($pos as $values)
												{
													$vl = explode(':', $values);
													$key = $vl[0];
													$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
													?>
													<option value="<?= $key ?>" <?php if (in_array($key, $saved)) :?> selected="selected" <?php endif ;?>><?= $value ?></option>
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


			<!-- Files -->
			<div class="tabcontent">
			
				<p class="h30">
					<a class="right light button" onclick="javascript:mediaManager.loadMediaList('file');return false;">
						<i class="icon-refresh"></i><?= lang('ionize_label_reload_media_list') ?>
					</a>
					<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('file');return false;">
						<i class="icon-unlink"></i><?= lang('ionize_label_detach_all_files') ?>
					</a>
				</p>
				
				<ul id="fileContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_file') ?></span>
				</ul>

			</div>

			<!-- Music -->
			<div class="tabcontent">
				
				<p class="h30"> 
					<a class="right light button" onclick="javascript:mediaManager.loadMediaList('music');return false;">
						<i class="icon-refresh"></i><?= lang('ionize_label_reload_media_list') ?>
					</a>
					<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('music');return false;">
						<i class="icon-unlink"></i><?= lang('ionize_label_detach_all_musics') ?>
					</a>
				</p>
				
				<ul id="musicContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_music') ?></span>
				</ul>

			</div>

			<!-- Videos -->
			<div class="tabcontent">
			
				<p class="h30">
					<a class="right light button" onclick="javascript:mediaManager.loadMediaList('video');return false;">
						<i class="icon-refresh"></i><?= lang('ionize_label_reload_media_list') ?>
					</a>
					<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('video');return false;">
						<i class="icon-unlink"></i><?= lang('ionize_label_detach_all_videos') ?>
					</a>
				</p>

				<ul id="videoContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_video') ?></span>
				</ul>

			</div>

			<!-- Pictures -->
			<div class="tabcontent">
			
				<p class="h30">
					<a class="right light button pictures" onclick="javascript:mediaManager.loadMediaList('picture');return false;">
						<i class="icon-refresh"></i><?= lang('ionize_label_reload_media_list') ?>
					</a>
					<a class="left light button delete" onclick="javascript:mediaManager.detachMediaByType('picture');return false;">
						<i class="icon-unlink"></i><?= lang('ionize_label_detach_all_pictures') ?>
					</a>
					<a class="left light button" onclick="javascript:mediaManager.initThumbsForParent();return false;">
						<i class="icon-process"></i><?= lang('ionize_label_init_all_thumbs') ?>
					</a>
				</p>
			
				<div id="pictureContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_picture') ?></span>
				</div>

			</div>
			
			</div>

		</fieldset>
		
		




		
		
		<!-- Articles -->
		<?php if($id_page) :?>

			<fieldset id="articles" class="mt20">
			
				<div id="childsTab" class="mainTabs">
					<ul class="tab-menu">
						
						<li class="selected"><a><?= lang('ionize_label_articles') ?></a></li>

					</ul>
					<div class="clear"></div>
				</div>

				<div id="childsTabContent" class="dropArticleInPage" rel="<?= $id_page ?>">
				
					<!-- Articles List -->
					<div class="tabcontent">

						<p>
							<a class="right light button helpme type">
								<i class="icon-helpme"></i><?= lang('ionize_label_help_articles_types') ?>
							</a>
							
							<!-- Droppable to link one article to this page -->
							<input type="text" id="new_article" class="inputtext w120 italic droppable empty nofocus" alt="<?= lang('ionize_label_drop_article_here') ?>" />
							<label title="<?= lang('ionize_help_page_drop_article_here') ?>"></label>
						</p>
						<br />
						<div id="articleListContainer"></div>
						
					</div> <!-- / tabcontent -->

				</div>
		
			</fieldset>
	
		<?php endif ;?>
	
	</div>

</form>


<!-- File Manager Form
	 Mandatory for the filemanager
-->
<form name="fileManagerForm" id="fileManagerForm">
	<input type="hidden" name="hiddenFile" />
</form>


<script type="text/javascript">

	/**
	 * Makes all elements with the class '.selectable' selectable
	 *
	 */
	ION.initSelectableText();


	ION.initHelp('#articles .type.helpme', 'article_type', Lang.get('ionize_title_help_articles_types'));

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#pageForm');

	ION.initFormAutoGrow();

	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('page_toolbox');


	/**
	 * Droppables init
	 *
	 */
	ION.initDroppable();


	/**
	 * Calendars init
	 *
	 */
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');


	/**
	 * Copy Lang data to other languages dynamically
	 *
	 */
	ION.initCopyLang('.copyLang', Array('title', 'subtitle', 'url', 'meta_title'));


	// Auto-generate Main title
	$$('.tabcontent .title').each(function(input, idx)
	{
		input.addEvent('keyup', function(e)
		{
			$('main-title').set('text', this.value);
		});
	});


	// Tabs
	var pageTab = new TabSwapper({tabsContainer: 'pageTab', sectionsContainer: 'pageTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'mainTab' });


	<?php if ( ! empty($id_page)) :?>

		// Auto-generates URL
		<?php foreach (Settings::get_languages() as $lang) :?>

			ION.initCorrectUrl('title_<?= $lang['lang']?>', 'url_<?= $lang['lang']?>');

		<?php endforeach ;?>

		/*
		 * Articles List
		 *
		 */
		ION.HTML(admin_url + 'article/get_list', {'id_page':'<?= $id_page ?>'}, {'update': 'articleListContainer'});

		/**
		 * Get Content Tabs & Elements
		 * 1. ION.getContentElements calls element_definition/get_definitions_from_parent : returns the elements definitions wich have elements for the current parent.
		 * 2. ION.getContentElements calls element/get_elements_from_definition : returns the elements for each definition
		 */
		$('desktop').store('tabSwapper', pageTab);
		ION.getContentElements('page', '<?= $id_page ?>');

		
		/**
		 * Loads media only when clicking the tab
		 *
		 */
		mediaManager.initParent('page', '<?= $id_page ?>');

		mediaManager.loadMediaList('file');
		mediaManager.loadMediaList('music');
		mediaManager.loadMediaList('video');
		mediaManager.loadMediaList('picture');

	<?php else: ?>

		// Current & parent page ID
		var id_current = ($('id_page').value) ? $('id_page').value : '0';
		var id_parent = ($('origin_id_parent').value) ? $('origin_id_parent').value : '0';

		$('id_menu').addEvent('change', function()
		{
			ION.HTML(
				admin_url + 'page/get_parents_select',
				{
					'id_menu' : $('id_menu').value,
					'id_current': id_current,
					'id_parent': id_parent
				},
				{
					'update': 'parentSelectContainer'
				}
			);
		});
		$('id_menu').fireEvent('change');


	<?php endif ;?>

</script>