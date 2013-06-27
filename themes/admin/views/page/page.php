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

$tracker_title = ${Settings::get_lang('default')}['title'];
if ($tracker_title == '')
	$tracker_title = $name;


?>
<form name="pageForm" id="pageForm" method="post" action="<?php echo admin_url() . 'page/save'?>">

	<input type="hidden" name="element" id="element" value="page" />
    <input type="hidden" class="data-tracker" name="data_tracker" data-element="page" data-id="<?php echo $id_page; ?>" data-title="<?php echo $tracker_title; ?>" data-url="page/edit/<?php echo $id_page; ?>" />
	<input type="hidden" name="action" id="action" value="save" />
	<input type="hidden" name="id_menu" value="<?php echo $id_menu; ?>" />
	<input type="hidden" name="created" value="<?php echo $created; ?>" />
	<input type="hidden" name="id_page" id="id_page" value="<?php echo $id_page; ?>" />
	<input type="hidden" name="rel" id="rel" value="<?php echo $id_page; ?>" />
	<input type="hidden" name="name" id="name" value="<?php echo $name; ?>" />
	<input type="hidden" id="origin_id_parent" value="<?php echo $id_parent; ?>" />
	<input type="hidden" id="origin_id_subnav" value="<?php echo $id_subnav; ?>" />

	<?php if ($id_page != '') :?>
		<input type="hidden" name="online" value="<?php echo $online; ?>" class="online<?php echo $id_page; ?>" />
	<?php endif ;?>


	<div id="maincolumn" class="">
		
		<fieldset>
				
		<?php if( ! empty($id_page)) :?>
			
			<?php
				
				$title = ${Settings::get_lang('default')}['title'];

				if ($title == '') $title = ${Settings::get_lang('default')}['url'];
			
			?>

        	<div id="page-tracker-<?php echo $id_page; ?>"></div>

			<h2 class="main page" id="main-title"><?php echo $title; ?></h2>
			
			<!-- Breadcrumb -->
			<div class="main subtitle">
				<p>
					<span class="lite">ID : </span><?php echo $id_page; ?> |
					<span class="lite"></span><?php echo$breadcrump?>
				</p>
			</div>
			
		<?php else :?>
			
			<h2 class="main page" id="main-title"><?php echo lang('ionize_title_new_page'); ?></h2>
			
			<!-- Menu -->
			<dl class="mt20">
				<dt>
					<label for="id_menu"><?php echo lang('ionize_label_menu'); ?></label>
				</dt>
				<dd>
					<?php echo $menus; ?>
				</dd>
			</dl>	

			<!-- Parent -->
			<dl>
				<dt>
					<label for="id_parent"><?php echo lang('ionize_label_parent'); ?></label>
				</dt>
				<dd>
					<div id ="parentSelectContainer"></div>
				</dd>
			</dl>	

			<!-- View -->
			<?php if (isset($views)) :?>
				<dl>
					<dt>
						<label for="view"><?php echo lang('ionize_label_view'); ?></label>
					</dt>
					<dd>
						<?php echo $views; ?>
					</dd>
				</dl>
			<?php endif ;?>
		
			<!-- Online / Offline -->
			<dl>
				<dt>
					<label for="online" title="<?php echo lang('ionize_help_page_online'); ?>"><?php echo lang('ionize_label_page_online'); ?></label>
				</dt>
				<dd>
					<div>
						<input id="online" <?php if ($online == 1):?> checked="checked" <?php endif;?> name="online" class="inputcheckbox online<?php echo $id_page; ?>" type="checkbox" value="1"/>
					</div>
				</dd>
			</dl>

			<!-- Appears as menu item in menu ? -->
			<dl>
				<dt>
					<label for="appears" title="<?php echo lang('ionize_help_appears'); ?>"><?php echo lang('ionize_label_appears'); ?></label>
				</dt>
				<dd>
					<input id="appears" name="appears" type="checkbox" class="inputcheckbox" <?php if ($appears == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>


		<?php endif ;?>

            <?php if ($id_page != '') :?>

                <!-- Modules PlaceHolder -->
                <?php echo get_modules_addons('page', 'main_top'); ?>

            <?php endif ;?>

        	<!-- Extend Fields (Main) -->
			<div id="pageExtendFields">

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
									$extend_field['content'] = (!empty($extend_field['content'])) ? $extend_field['content'] : $extend_field['default_value'];
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


		<fieldset class="mt10">
	

			<!-- Tabs -->
			<div id="pageTab" class="mainTabs">
				
				<ul class="tab-menu">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						<li class="tab_page<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['name']); ?></a></li>
					<?php endforeach ;?>

					<?php if ( ! empty($id_page)) :?>

						<?php if(Authority::can('access', 'admin/page/media/file')) :?>
							<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="fileTab"><a><?php echo lang('ionize_label_files'); ?></a></li>
						<?php endif ;?>
						<?php if(Authority::can('access', 'admin/page/media/music')) :?>
							<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="musicTab"><a><?php echo lang('ionize_label_music'); ?></a></li>
						<?php endif ;?>
						<?php if(Authority::can('access', 'admin/page/media/video')) :?>
							<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="videoTab"><a><?php echo lang('ionize_label_videos'); ?></a></li>
						<?php endif ;?>
						<?php if(Authority::can('access', 'admin/page/media/picture')) :?>
							<li class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>" id="pictureTab"><a><?php echo lang('ionize_label_pictures'); ?></a></li>
						<?php endif ;?>

					<?php endif ;?>

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

						<p class="clear h20">
							<?php if( ! is_null($lang_url)) :?>
								<a class="button light right" href="<?php echo base_url(); ?><?php echo $lang_url; ?>" target="_blank" title="<?php echo lang('ionize_label_see_online'); ?>">
									<i class="icon arrow-right"></i>
									<?php echo lang('ionize_label_see_online') ?>
								</a>
							<?php endif; ?>
							<a class="button light right copyLang"rel="<?php echo $lang; ?>" title="<?php echo lang('ionize_label_copy_to_other_languages'); ?>">
								<i class="icon copy"></i>
								<?php echo lang('ionize_label_copy_to_other_languages') ?>
							</a>
						</p>

						<!-- Online -->
						<?php if(count(Settings::get_languages()) > 1) :?>

							<dl>
								<dt>
									<label for="online_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_content_online'); ?>"><?php echo lang('ionize_label_online_in'); ?> <?php echo ucfirst($language['name']); ?></label>
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
								<textarea id="title_<?php echo $lang; ?>" name="title_<?php echo $lang; ?>" class="textarea title autogrow" type="text" title="<?php echo lang('ionize_label_title'); ?>"><?php echo ${$lang}['title']; ?></textarea>
							</dd>
						</dl>

						<!-- Sub title -->
						<dl>
							<dt>
								<label for="subtitle_<?php echo $lang; ?>"><?php echo lang('ionize_label_subtitle'); ?></label>
							</dt>
							<dd>
								<textarea id="subtitle_<?php echo $lang; ?>" name="subtitle_<?php echo $lang; ?>" class="textarea autogrow" type="text"><?php echo ${$lang}['subtitle']; ?></textarea>
							</dd>
						</dl>


						<!-- URL -->
						<dl>
							<dt>
								<label for="url_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_url'); ?>"><?php echo lang('ionize_label_url'); ?></label>
							</dt>
							<dd>
								<input id="url_<?php echo $lang; ?>" name="url_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo ${$lang}['url']; ?>" title="<?php echo lang('ionize_help_page_url'); ?>" />

								<?php if( ! is_null($lang_url)) :?>
									<br/>
									<?php echo lang('ionize_label_full_url'); ?> : <i class="selectable">/<?php echo $lang_url; ?></i>
								<?php endif; ?>

							</dd>
						</dl>

						<!-- Nav title -->
						<dl>
							<dt>
								<label for="nav_title_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_nav_title'); ?>"><?php echo lang('ionize_label_nav_title'); ?></label>
							</dt>
							<dd>
								<input id="nav_title_<?php echo $lang; ?>" name="nav_title_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo ${$lang}['nav_title']; ?>"/>
							</dd>
						</dl>

						<!-- Meta title : used for browser window title -->
						<dl>
							<dt>
								<label for="meta_title_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_window_title'); ?>"><?php echo lang('ionize_label_meta_title'); ?></label>
							</dt>
							<dd>
								<input id="meta_title_<?php echo $lang; ?>" name="meta_title_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo ${$lang}['meta_title']; ?>"/>
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

					</div>

				<?php endforeach ;?>

				<?php if ( ! empty($id_page)) :?>

					<?php if(Authority::can('access', 'admin/page/media/file')) :?>
						<!-- Files -->
						<div class="tabcontent">

							<p class="h30">
								<a class="right light button" onclick="javascript:mediaManager.loadMediaList('file');return false;">
									<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
								</a>
								<?php if(Authority::can('unlink', 'admin/page/media/file')) :?>

									<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('file');return false;">
										<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_files'); ?>
									</a>

								<?php endif ;?>
							</p>

							<ul id="fileContainer" class="sortable-container">
								<span><?php echo lang('ionize_message_no_file'); ?></span>
							</ul>

						</div>
					<?php endif ;?>

					<?php if(Authority::can('access', 'admin/page/media/music')) :?>
						<!-- Music -->
						<div class="tabcontent">

							<p class="h30">
								<a class="right light button" onclick="javascript:mediaManager.loadMediaList('music');return false;">
									<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
								</a>
								<?php if(Authority::can('unlink', 'admin/page/media/music')) :?>

									<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('music');return false;">
										<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_musics'); ?>
									</a>

								<?php endif ;?>

							</p>

							<ul id="musicContainer" class="sortable-container">
								<span><?php echo lang('ionize_message_no_music'); ?></span>
							</ul>

						</div>
					<?php endif ;?>

					<?php if(Authority::can('access', 'admin/page/media/video')) :?>
						<!-- Videos -->
						<div class="tabcontent">

							<p class="h30">
								<a class="right light button" onclick="javascript:mediaManager.loadMediaList('video');return false;">
									<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
								</a>

								<?php if(Authority::can('unlink', 'admin/page/media/video')) :?>

									<a class="left light button" onclick="javascript:mediaManager.detachMediaByType('video');return false;">
										<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_videos'); ?>
									</a>
								<?php endif ;?>

							</p>

							<?php if(Authority::can('link', 'admin/page/media/video')) :?>

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

							<?php endif ;?>

							<ul id="videoContainer" class="sortable-container">
								<span><?php echo lang('ionize_message_no_video'); ?></span>
							</ul>

						</div>
					<?php endif ;?>

					<?php if(Authority::can('access', 'admin/page/media/picture')) :?>
						<!-- Pictures -->
						<div class="tabcontent">

							<p class="h30">
								<a class="right light button pictures" onclick="javascript:mediaManager.loadMediaList('picture');return false;">
									<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
								</a>
								<?php if(Authority::can('unlink', 'admin/page/media/picture')) :?>
									<a class="left light button delete" onclick="javascript:mediaManager.detachMediaByType('picture');return false;">
										<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_pictures'); ?>
									</a>
								<?php endif ;?>

							</p>

							<div id="pictureContainer" class="sortable-container">
								<span><?php echo lang('ionize_message_no_picture'); ?></span>
							</div>

						</div>
					<?php endif ;?>

				<?php endif ;?>
			</div>
		</fieldset>
		
		<!-- Articles -->
		<?php if($id_page) :?>

			<fieldset id="articles" class="mt20">
			
				<div id="childsTab" class="mainTabs">
					<ul class="tab-menu">
						
						<li class="selected"><a><?php echo lang('ionize_label_articles'); ?></a></li>

					</ul>
					<div class="clear"></div>
				</div>

				<div id="childsTabContent" class="dropArticleInPage" data-id="<?php echo $id_page; ?>">
				
					<!-- Articles List -->
					<div class="tabcontent">

						<p>
							<a class="right light button helpme type">
								<i class="icon-helpme"></i><?php echo lang('ionize_label_help_articles_types'); ?>
							</a>
							
							<!-- Droppable to link one article to this page -->
							<input type="text" id="new_article" class="inputtext w120 italic droppable empty nofocus" alt="<?php echo lang('ionize_label_drop_article_here'); ?>" />
							<label title="<?php echo lang('ionize_help_page_drop_article_here'); ?>"></label>
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

	// Makes all elements with the class '.selectable' selectable
	ION.initSelectableText();

	ION.initHelp('#articles .type.helpme', 'article_type', Lang.get('ionize_title_help_articles_types'));

	ION.initFormAutoGrow();

	// Toolbox
	ION.initToolbox('page_toolbox', null, {'id_page': '<?php echo $id_page; ?>'});

	// Droppables
	ION.initDroppable();

	// Calendars init
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');
    ION.initClearField('#pageForm');

	// Copy Lang data to other languages dynamically
	ION.initCopyLang('.copyLang', Array('title', 'subtitle', 'url', 'meta_title', 'nav_title'));

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

    // TinyEditors. Must be called after tabs init.
    ION.initTinyEditors(null, '#pageExtendFields .tinyTextarea');
    ION.initTinyEditors(null, '#pageExtendFields .smallTinyTextarea', 'small', {'height':80});

    ION.initTinyEditors('.tab_page', '#pageTabContent .tinyTextarea');
    ION.initTinyEditors('.tab_page', '#pageTabContent .smallTinyTextarea', 'small', {'height':80});

	<?php if ( ! empty($id_page)) :?>

		// Articles List
		ION.HTML('article/get_list', {'id_page':'<?php echo $id_page; ?>'}, {'update': 'articleListContainer'});

		/**
		 * Get Content Tabs & Elements
		 * 1. ION.getContentElements calls element_definition/get_definitions_from_parent : returns the elements definitions wich have elements for the current parent.
		 * 2. ION.getContentElements calls element/get_elements_from_definition : returns the elements for each definition
		 */
		$('desktop').store('tabSwapper', pageTab);
		ION.getContentElements('page', '<?php echo $id_page; ?>');


		<?php if(Authority::can('link', 'admin/page/media/video')) :?>

			// Add Video button
			$('btnAddVideo').addEvent('click', function()
			{
				if ($('addVideo').value !='')
				{
					ION.JSON('media/add_external_media', {
						'type': 'video',
						'parent': 'page',
						'id_parent': '<?php echo $id_page; ?>',
						'path': $('addVideo').value
					});
				}
				return false;
			});
		<?php endif ;?>

		// Media Manager & tabs events
		mediaManager.initParent('page', '<?php echo $id_page; ?>');

		<?php if(Authority::can('access', 'admin/page/media/file')) :?>
			mediaManager.loadMediaList('file');
		<?php endif ;?>
		<?php if(Authority::can('access', 'admin/page/media/music')) :?>
			mediaManager.loadMediaList('music');
		<?php endif ;?>
		<?php if(Authority::can('access', 'admin/page/media/video')) :?>
			mediaManager.loadMediaList('video');
		<?php endif ;?>
		<?php if(Authority::can('access', 'admin/page/media/picture')) :?>
			mediaManager.loadMediaList('picture');
		<?php endif ;?>

	<?php else: ?>

		// Auto-generates URL
		<?php foreach (Settings::get_languages() as $lang) :?>

			ION.initCorrectUrl('title_<?php echo $lang['lang']; ?>', 'url_<?php echo $lang['lang']; ?>');

		<?php endforeach ;?>

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
					'id_parent': id_parent,
					'check_add_page' : true
				},
				{
					'update': 'parentSelectContainer'
				}
			);
		});
		$('id_menu').fireEvent('change');


	<?php endif ;?>

</script>