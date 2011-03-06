
<form name="pageForm" id="pageForm" method="post" action="<?= admin_url() . 'page/save'?>">

	<input type="hidden" name="element" id="element" value="page" />
	<input type="hidden" name="action" id="action" value="save" />
	<input type="hidden" name="id_menu" value="<?= $id_menu ?>" />
	<input type="hidden" name="created" value="<?= $created ?>" />
	<input type="hidden" name="id_page" id="id_page" value="<?= $id_page ?>" />
	<input type="hidden" name="name" id="name" value="<?= $name ?>" />
	<input type="hidden" id="origin_id_parent" value="<?= $id_parent ?>" />
	
	<?php if ($id_page != '') :?>
		<input type="hidden" name="online" value="<?= $online ?>" class="online<?= $id_page ?>" />
	<?php endif ;?>
	
	<div id="sidecolumn" class="close">

		<!-- Main informations -->
		<?php if ($id_page != '') :?>
			
			<div class="info">
			
				<dl class="compact small">
					<dt><label><?= lang('ionize_label_status') ?></label></dt>
					<dd class="icon">
						<a class="page<?= $id_page ?> <?=($online == '1') ? 'online' : 'offline' ;?>" onclick="javascript:ION.switchPageStatus('<?= $id_page ?>')"></a>
					</dd>
				</dl>
		
				<dl class="compact small">
					<dt><label><?= lang('ionize_label_created') ?></label></dt>
					<dd><?= humanize_mdate($created, Settings::get('date_format'). ' %H:%m:%s') ?></dd>
				</dl>
		
				<?php if (humanize_mdate($updated, Settings::get('date_format'). ' %H:%m:%s') != '') :?>
					<dl class="compact small">
						<dt><label><?= lang('ionize_label_updated') ?></label></dt>
						<dd><?= humanize_mdate($updated, Settings::get('date_format'). ' %H:%m:%s') ?></dd>
					</dl>
				<?php endif ;?>

				<!-- Internal / External link Info -->
				<dl class="compact" id="link_info"></dl>

			</div>
			
		<?php endif ;?>


		<div id="options">

			<!-- Options -->
			<h3 class="toggler"><?= lang('ionize_title_options') ?></h3>
		
			<div class="element">

				<!-- Existing page -->
				<?php if ($id_page != '') :?>
				
					<!-- Appears as menu item in menu ? -->
					<dl class="small">
						<dt>
							<label for="appears" title="<?= lang('ionize_help_appears') ?>"><?= lang('ionize_label_appears') ?></label>
						</dt>
						<dd>
							<input id="appears" name="appears" type="checkbox" class="inputcheckbox" <?php if ($appears == 1):?> checked="checked" <?php endif;?> value="1" />
						</dd>
					</dl>
				
				<?php endif ;?>


				<!-- Page view -->
				<?php if ($id_page !='' && isset($views)) :?>
					<dl class="small">
						<dt>
							<label for="view"><?= lang('ionize_label_view') ?></label>
						</dt>
						<dd>
							<?= $views ?>
						</dd>
					</dl>
				<?php endif ;?>
				
				<!-- Article List Template -->
				<?php if (isset($article_list_views)) :?>
					<dl class="small">
						<dt>
							<label for="article_list_view" title="<?= lang('ionize_help_article_list_template') ?>"><?= lang('ionize_label_article_list_template') ?></label>
						</dt>
						<dd>
							<?= $article_list_views ?>
						</dd>
					</dl>
				<?php endif ;?>
				
				<!-- Article Template -->
				<?php if (isset($article_views)) :?>
					<dl class="small">
						<dt>
							<label for="article_view"><?= lang('ionize_label_article_template') ?></label>
						</dt>
						<dd>
							<?= $article_views ?>
						</dd>
					</dl>
				<?php endif ;?>
				

				<!-- Internal / External link -->
				<dl class="small last">
					<dt>
						<label for="link" title="<?= lang('ionize_help_page_link') ?>"><?= lang('ionize_label_link') ?></label>
						<br/>
						
					</dt>
					<dd>
						<input type="hidden" id="link_type" name="link_type" value="<?= $link_type ?>" />
						<input type="hidden" id="link_id" name="link_id" value="<?= $link_id ?>" />
						
						<textarea id="link" name="link" class="inputtext w140 h40 droppable" alt="<?= lang('ionize_label_drop_link_here') ?>"><?= $link ?></textarea>
						<br />
						
						<a id="link_remove"><?= lang('ionize_label_remove_link') ?></a><br/>
					</dd>
				</dl>

			</div>
			

			<!-- Parent -->
			<?php if ($id_page != '') :?>

				<h3 class="toggler"><?= lang('ionize_title_page_parent') ?></h3>
				
				<div class="element">
			
					<!-- Menu -->
					<dl class="small">
						<dt>
							<label for="id_menu"><?= lang('ionize_label_menu') ?></label>
						</dt>
						<dd>
							<?= $menus ?>
						</dd>
					</dl>	
	
					<!-- Parent -->
					<dl class="small last">
						<dt>
							<label for="id_parent"><?= lang('ionize_label_parent') ?></label>
						</dt>
						<dd>
							<select name="id_parent" id="id_parent" class="select w150">
							
							
							</select>
						</dd>
					</dl>
					
				</div>
			
			<?php endif ;?>


			<!-- Advanced Options -->
			<h3 class="toggler"><?= lang('ionize_title_advanced') ?></h3>
		
			<div class="element">
				
				<!-- Pagination -->
				<dl class="small">
					<dt>
						<label for="pagination" title="<?= lang('ionize_help_pagination') ?>"><?= lang('ionize_label_pagination_nb') ?></label>
					</dt>
					<dd>
						<input id="pagination" name="pagination" type="text" class="inputtext w40" value="<?= $pagination ?>" />
					</dd>
				</dl>

				<!-- Home page -->
				<dl class="small last">
					<dt>
						<label for="home" title="<?= lang('ionize_help_home_page') ?>"><?= lang('ionize_label_home_page') ?></label>
					</dt>
					<dd>
						<input id="home" name="home" type="checkbox" class="inputcheckbox" <?php if ($home == 1):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>


			</div>

			<!-- Dates -->
			<h3 class="toggler"><?= lang('ionize_title_dates') ?></h3>
			
			<div class="element">
				<dl class="small">
					<dt>
						<label for="publish_on" title="<?= lang('ionize_help_publish_on') ?>"><?= lang('ionize_label_publish_on') ?></label>
					</dt>
					<dd>
						<input id="publish_on" name="publish_on" type="text" class="inputtext w120 date" value="<?= humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>
			
				<dl class="small last">
					<dt>
						<label for="publish_off" title="<?= lang('ionize_help_publish_off') ?>"><?= lang('ionize_label_publish_off') ?></label>
					</dt>
					<dd>
						<input id="publish_off" name="publish_off" type="text" class="inputtext w120 date"  value="<?= humanize_mdate($publish_off, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>
			
			</div>


			<!-- Metas -->
			<h3 class="toggler"><?= lang('ionize_title_metas') ?></h3>
			
			<div class="element">
				
				<!-- Meta_Description -->
				<dl class="small">
					<dt>
						<label title="<?= lang('ionize_help_page_meta') ?>"><?= lang('ionize_label_meta_description') ?></label>
					</dt>
					<dd>
						<div class="tab small">
							<ul class="tab-content small">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li id="tab-<?= $language['lang'] ?>-description"><a><span><?= ucfirst(substr($language['name'],0,3)) ?></span></a></li>
								<?php endforeach ;?>
							</ul>
						</div>
						
						<?php foreach(Settings::get_languages() as $language) :?>
							<div id="block-<?= $language['lang'] ?>-description" class="block description small">
								<textarea id="meta_description_<?= $language['lang'] ?>" name="meta_description_<?= $language['lang'] ?>" class="w140 h80"><?= ${$language['lang']}['meta_description'] ?></textarea>
							</div>
						<?php endforeach ;?>
						
					</dd>
				</dl>
			
				<!-- Meta_Keywords -->
				<dl class="small last">
					<dt>
						<label title="<?= lang('ionize_help_page_meta') ?>"><?= lang('ionize_label_meta_keywords') ?></label>
					</dt>
					<dd>
						<div class="tab small">
							<ul class="tab-content small">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li id="tab-<?= $language['lang'] ?>-keywords"><a><span><?= ucfirst(substr($language['name'],0,3)) ?></span></a></li>
								<?php endforeach ;?>
							</ul>
						</div>
						
						<?php foreach(Settings::get_languages() as $language) :?>
							<div id="block-<?= $language['lang'] ?>-keywords" class="block keywords small">
								<textarea id="meta_keywords_<?= $language['lang'] ?>" name="meta_keywords_<?= $language['lang'] ?>" class="w140 h40"><?= ${$language['lang']}['meta_keywords'] ?></textarea>
							</div>
						<?php endforeach ;?>
						
					</dd>
				</dl>
			
			</div>

	

			<!-- Access authorization -->
			<h3 class="toggler"><?= lang('ionize_title_authorization') ?></h3>
			
			<div class="element">
			
				<dl class="small last">
					<dt>
						<label for="template"><?= lang('ionize_label_groups') ?></label>
					</dt>
					<dd>
						<div id="groups">
							<?= $groups ?>
						</div>
					</dd>
				</dl>
			
			</div>


			<!-- Copy Content -->
			<h3 class="toggler"><?= lang('ionize_title_content') ?></h3>
			
			<div class="element">
			
				<dl class="small">
					<dt>
						<label for="lang_copy_from" title="<?= lang('ionize_help_copy_content') ?>"><?= lang('ionize_label_copy_content') ?></label>
					</dt>
					<dd>
						<div class="w100 h50 left">
							<select name="lang_copy_from" id="lang_copy_from" class="w100 select">
								<?php foreach(Settings::get_languages() as $language) :?>
									<option value="<?= $language['lang'] ?>"><?= ucfirst($language['name']) ?></option>
								<?php endforeach ;?>
							</select>
							
							<br/>
						
							<select name="lang_copy_to" id="lang_copy_to" class="w100 select mt5">
								<?php foreach(Settings::get_languages() as $language) :?>
									<option value="<?= $language['lang'] ?>"><?= ucfirst($language['name']) ?></option>
								<?php endforeach ;?>
							</select>
						
						</div>
						<div class="w30 h50 left ml5" style="background:url(<?= theme_url() ?>images/icon_24_from_to.png) no-repeat 50% 50%;"></div>
					</dd>
				</dl>
			
				<!-- Inlude article content  -->
				<dl class="small">
					<dt>
						<label for="copy_article" title="<?= lang('ionize_help_copy_article_content') ?>"><?= lang('ionize_label_copy_article_content') ?></label></dt>
					<dd>
						<input type="checkbox" name="copy_article" id="copy_article" value="1" />
					</dd>
				</dl>
				
				<!-- Submit button  -->
				<dl class="small last">
					<dt>&#160;</dt>
					<dd>
						<input type="submit" value="<?= lang('ionize_button_copy_content') ?>" class="submit" id="copy_lang">
					</dd>
				</dl>
			
			</div>


			<!-- Other info : Permanent URL, etc. -->
			<h3 class="toggler"><?= lang('ionize_title_informations') ?></h3>
			
			<div class="element">

				<?php if ($id_page != '') :?>
				<dl class="small compact">
					<dt><label for="permanent_url"><?= lang('ionize_label_permanent_url') ?></label></dt>
					<dd>
						<div class="tab small">
							<ul class="tab-content small">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li id="tab-<?= $language['lang'] ?>-permanent_url"><a><span><?= ucfirst(substr($language['name'],0,3)) ?></span></a></li>
								<?php endforeach ;?>
							</ul>
						</div>
						
						<?php foreach(Settings::get_languages() as $language) :?>
						
							<?php
							
							$lang = (count(Settings::get_online_languages()) > 1) ? $language['lang'].'/' : '';
							
							?>
						
							<div id="block-<?= $language['lang'] ?>-permanent_url" class="block permanent_url small">
								<textarea id="permanent_url_<?= $language['lang'] ?>" class="w140 h80" onclick="javascript:this.select();" readonly="readonly"><?= base_url().$lang ?><?= ${$language['lang']}['url'] ?></textarea>
							</div>
						<?php endforeach ;?>
						
					</dd>
				</dl>
				
				<!-- Technical info -->
				<dl class="small compact">
					<dt><label for="">Ordering</label></dt>
					<dd><?= $ordering ?></dd>
				</dl>

				<?php endif ;?>
			
			</div>
			
			
		</div>	<!-- /options -->
	
	</div> <!-- /sidecolumn -->



	<div id="maincolumn">
		
		<fieldset>
				

		<?php if( ! empty($id_page)) :?>
			
			<?php
				
				$title = ${Settings::get_lang('default')}['title'];
				
				if ($title == '') $title = ${Settings::get_lang('default')}['name'];
			
			?>

			<h2 class="main page" id="main-title"><?= $title ?></h2>


		<?php else :?>
			
			<h2 class="main page" id="main-title"><?= lang('ionize_title_new_page') ?></h2>
			
			<!-- Menu -->
			<dl>
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
					<select name="id_parent" id="id_parent" class="select"></select>
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
			<?php if (Settings::get('use_extend_fields') == '1') :?>
				<?php foreach($extend_fields as $extend_field) :?>
				
					<?php if ($extend_field['translated'] != '1') :?>
					
						<dl>
							<dt>
								<label for="cf_<?= $extend_field['id_extend_field'] ?>" title="<?= $extend_field['description'] ?>"><?= $extend_field['label'] ?></label>
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
			<?php endif ;?>

		</fieldset>


		<fieldset id="blocks">
	
			<!-- Tabs -->
			<div class="tab">
				<ul class="tab-content">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						<li id="tab-<?= $language['lang'] ?>"<?php if($language['def'] == '1') :?> class="dl"<?php endif ;?>><a><span><?= ucfirst($language['name']) ?></span></a></li>
					<?php endforeach ;?>

					<li id="tab-files" class="right<?php if( empty($id_page)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_files') ?></span></a></li>
					<li id="tab-music" class="right<?php if( empty($id_page)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_music') ?></span></a></li>
					<li id="tab-videos" class="right<?php if( empty($id_page)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_videos') ?></span></a></li>
					<li id="tab-pictures" class="right<?php if( empty($id_page)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_pictures') ?></span></a></li>

				</ul>
			</div>
	
			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $language) :?>
		
				<?php $lang = $language['lang']; ?>
				
				<div id="block-<?= $lang ?>" class="block data">
		
					<!-- title -->
					<dl class="first">
						<dt>
							<label for="title_<?= $lang ?>"><?= lang('ionize_label_title') ?></label>
						</dt>
						<dd>
							<input id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="inputtext title" type="text" value="<?= ${$lang}['title'] ?>"/>
						</dd>
					</dl>

					<!-- URL -->
					<dl>
						<dt>
							<label for="url_<?= $lang ?>" title="<?= lang('ionize_help_page_url') ?>"><?= lang('ionize_label_url') ?></label>
						</dt>
						<dd>
							<input id="url_<?= $lang ?>" name="url_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['url'] ?>"/>
							<?php if(!empty($id_page)) :?>
								<a href="<?= base_url()?><?= ${$lang}['url'] ?>" target="_blank" title="<?= lang('ionize_label_see_online') ?>"><img src="<?= base_url()?><?= Theme::get_theme_path() ?>images/icon_16_right.png" /></a>
							<?php endif; ?>
						</dd>
					</dl>

					<!-- Meta title : used for browser window title -->
					<dl>
						<dt>
							<label for="meta_title_<?= $lang ?>" title="<?= lang('ionize_help_page_window_title') ?>"><?= lang('ionize_label_meta_title') ?></label>
						</dt>
						<dd>
							<input id="meta_title_<?= $lang ?>" name="meta_title_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['meta_title'] ?>"/>
						</dd>
					</dl>

			
					<!-- sub title -->
					<dl>
						<dt>
							<label for="subtitle_<?= $lang ?>"><?= lang('ionize_label_subtitle') ?></label>
						</dt>
						<dd>
							<textarea id="subtitle_<?= $lang ?>" name="subtitle_<?= $lang ?>" class="inputtext h30" type="text"><?= ${$lang}['subtitle'] ?></textarea>
						</dd>
					</dl>
			

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



					<!-- extend fields goes here... -->
					<?php if (Settings::get('use_extend_fields') == '1') :?>
						<?php foreach($extend_fields as $extend_field) :?>
							<?php if ($extend_field['translated'] == '1') :?>
							
								<dl>
									<dt>
										<label for="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" title="<?= $extend_field['description'] ?>"><?= $extend_field['label'] ?></label>
									</dt>
									<dd>
										<?php
											$extend_field[$lang]['content'] = (!empty($extend_field[$lang]['content'])) ? $extend_field[$lang]['content'] : $extend_field['default_value'];
										?>

										<?php if ($extend_field['type'] == '1') :?>
											<input id="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" class="inputtext" type="text" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" value="<?= $extend_field[$lang]['content'] ?>" />
										<?php endif ;?>
										
										<?php if ($extend_field['type'] == '2' || $extend_field['type'] == '3') :?>
											<textarea id="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" class="inputtext h80 <?php if($extend_field['type'] == '3'):?> tinyTextarea <?php endif ;?>" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>"><?= $extend_field[$lang]['content'] ?></textarea>
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
					<?php endif ;?>

				</div>
				
			<?php endforeach ;?>


			<!-- Files -->
			<div id="block-files" class="block data">
			
				<p>
					<a class="fmButton right"><img src="<?= theme_url() ?>images/icon_16_plus.png" /> <?= lang('ionize_label_attach_media') ?></a>
					<a class="right pr5" href="javascript:mediaManager.loadMediaList('file')"><img src="<?= theme_url() ?>images/icon_16_files.png" /> <?= lang('ionize_label_reload_media_list') ?></a>
					<a class="pr5" href="javascript:mediaManager.detachMediaByType('file')"><img src="<?= theme_url() ?>images/icon_16_delete.png" />  <?= lang('ionize_label_detach_all_files') ?></a>
				</p>
				
				<ul id="fileContainer">
					<span><?= lang('ionize_message_no_file') ?></span>
				</ul>

			</div>

			<!-- Music -->
			<div id="block-music" class="block data">
				
				<p>
					<a class="fmButton right"><img src="<?= theme_url() ?>images/icon_16_plus.png" /> <?= lang('ionize_label_attach_media') ?></a>
					<a class="right pr5" href="javascript:mediaManager.loadMediaList('music')"><img src="<?= theme_url() ?>images/icon_16_music.png" /> <?= lang('ionize_label_reload_media_list') ?></a>
					<a class="pr5" href="javascript:mediaManager.detachMediaByType('music')"><img src="<?= theme_url() ?>images/icon_16_delete.png" />  <?= lang('ionize_label_detach_all_musics') ?></a>
				</p>
				
				<ul id="musicContainer">
					<span><?= lang('ionize_message_no_music') ?></span>
				</ul>

			</div>

			<!-- Videos -->
			<div id="block-videos" class="block data">
			
				<p>
					<a class="fmButton right"><img src="<?= theme_url() ?>images/icon_16_plus.png" /> <?= lang('ionize_label_attach_media') ?></a>
					<a class="right pr5" href="javascript:mediaManager.loadMediaList('video')"><img src="<?= theme_url() ?>images/icon_16_video.png" /> <?= lang('ionize_label_reload_media_list') ?></a>
					<a class="pr5" href="javascript:mediaManager.detachMediaByType('video')"><img src="<?= theme_url() ?>images/icon_16_delete.png" />  <?= lang('ionize_label_detach_all_videos') ?></a>
				</p>

				<ul id="videoContainer">
					<span><?= lang('ionize_message_no_video') ?></span>
				</ul>

			</div>

			<!-- Pictures -->
			<div id="block-pictures" class="block data">
			
				<p>
					<a class="fmButton right"><img src="<?= theme_url() ?>images/icon_16_plus.png" /> <?= lang('ionize_label_attach_media') ?></a>
					<a class="right pr5" href="javascript:mediaManager.loadMediaList('picture')"><img src="<?= theme_url() ?>images/icon_16_imagelist.png" /> <?= lang('ionize_label_reload_media_list') ?></a>
					<a class="pr5" href="javascript:mediaManager.detachMediaByType('picture')"><img src="<?= theme_url() ?>images/icon_16_delete.png" />  <?= lang('ionize_label_detach_all_pictures') ?></a>
					<a href="javascript:mediaManager.initThumbsForParent()"><img src="<?= theme_url() ?>images/icon_16_refresh.png" /> <?= lang('ionize_label_init_all_thumbs') ?></a>
				</p>
			
				<ul id="pictureContainer">
					<span><?= lang('ionize_message_no_picture') ?></span>
				</ul>

			</div>

		</fieldset>
		
		
		<!-- Articles -->
		<?php if($id_page) :?>

			<fieldset id="articles" class="mt20">
			
				<!-- Tabs -->
				<div class="tab">
					<ul class="tab-content">
	
						<li id="tab-articles"><a><span><?= lang('ionize_label_articles') ?></span></a></li>
	
					</ul>
				</div>

				<!-- Articles list -->
				<div id="block-articles" class="block articles">
				
					<p>

						<a id="articleCreate" class="right" href="<?= admin_url() ?>article/create/<?= $id_page ?>"><img src="<?= theme_url() ?>images/icon_16_add_article.png" /> <?= lang('ionize_label_add_article') ?></a>

						<!-- Droppable to link one article to this page -->
						<input type="text" id="new_article" class="inputtext w120 italic droppable empty nofocus" alt="<?= lang('ionize_label_drop_article_here') ?>"></input>
						<label title="<?= lang('ionize_help_page_drop_article_here') ?>"></label>

					</p>
					
					<br />
				
					<ul id="articleList<?= $id_page ?>">
					
						<?php
			
							$nbLang = count(Settings::get_languages());
							$flag_width = (25 * $nbLang);

						?>
					
					
						<?php foreach ($articles as $article) :?>
						
							<?php
							
							$title = ($article['title'] != '') ? $article['title'] : $article['name'];
							
							$rel = $article['id_page'] . '.' . $article['id_article'];
							
							$flat_rel = $article['id_page'] . 'x' .  $article['id_article'];
							
							$status = (!$article['online']) ? 'offline' : 'online' ;

							// Content for each existing language
							$content_html = '';
							
							// Array of status
							$content = array();
							
							foreach($article['langs'] as $lang)
							{
								if ($lang['content'] != '') $content[] = '<img class="left pl5 pt3" src="'. theme_url() . 'images/world_flags/flag_' . $lang['lang'] . '.gif" />';
							}
							
							// HTML
							$content_html = implode('', $content);
							
							?>
	
							<li class="sortme article<?= $article['id_article'] ?> article<?= $flat_rel ?> <?= $status ;?>" rel="<?= $rel ?>">
								
								<!-- Unlink icon -->
								<a class="icon right unlink" rel="<?= $rel ?>" title="<?= lang('ionize_label_unlink') ?>"></a>
								
								<!-- Status icon -->
								<a class="icon right pr5 status article<?= $article['id_article'] ?> article<?= $flat_rel ?> <?= $status ;?>" rel="<?= $rel ?>"></a>
								
								<!-- Flags : Available content for language -->
								<span style="width:<?=$flag_width?>px;display:block;height:16px;" class="right mr20 ml20"><?= $content_html ?></span>

								<!-- Article Settings 
								<a class="right ml20 article_setting" rel="<?= $rel ?>" title="<?= lang('ionize_help_article_context') ?>"><?= lang('ionize_label_article_edit_context') ?></a>
								-->
								
								<!-- Type -->
								<span class="right ml20 type-block" rel="<?= $rel ?>">
									
									<select id="type<?= $flat_rel ?>" class="select w120 type" style="padding:0;" rel="<?= $rel ?>">
										<?php foreach($all_article_types as $idx => $type) :?>
											<option <?php if ($article['id_type'] == $idx) :?>selected="selected"<?php endif; ?>  value="<?= $idx ?>"><?= $type ?></option>
										<?php endforeach ;?>
									</select>

								</span>

								<!-- Used view -->
								<span class="right">
								
									<select id="view<?= $flat_rel ?>" class="select w120 view" style="padding:0;" rel="<?= $rel ?>">
										<?php foreach($all_article_views as $idx => $view) :?>
											<option <?php if ($article['view'] == $idx) :?>selected="selected"<?php endif; ?> value="<?= $idx ?>"><?= $view ?></option>
										<?php endforeach ;?>
									</select>
								
								</span>
								
								<!-- Drag icon -->
								<a class="icon left pr5 drag" />
								
								<!-- Title (draggable) -->
								<a style="overflow:hidden;height:16px;display:block;" class=" pl5 pr10 article article<?= $flat_rel ?> <?= $status ;?>" title="<?= lang('ionize_label_edit') ?> / <?= lang('ionize_label_drag_to_page') ?>" rel="<?= $rel ?>"><span class="flag flag<?= $article['flag'] ?>"></span><?= $title ?></a>
							</li>
						
						<?php endforeach ;?>
					
					</ul>
				
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
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler', 'div.element');


	/** 
	 * TinyMCE control add on first language only
	 */
	if ($$('.tinyTextarea'))
	{
//		tinyMCE.init(tinyMCEParam);
	}

	/**
	 * Init help tips on label
	 *
	 */
	MUI.initLabelHelpLinks('#pageForm');


	/**
	 * Panel toolbox
	 *
	 */
	MUI.initToolbox('page_toolbox');


	/**
	 * Droppables init
	 */
	ION.initDroppable();


	// Remove link event
	if ($('link_remove'))
	{
		$('link_remove').addEvent('click', function()
		{
			ION.removeLink();
		});
	}
	
	
	// Add edit link to the link (if internal)
	<?php if ($link != '') :?>
	
		ION.updateLinkInfo({
			'type': '<?php echo($link_type); ?>',
			'id': '<?php echo($link_id); ?>', 
			'text': '<?php echo($link); ?>'
		});
	
	<?php endif ;?>

	
	// Update parent select list when menu change
	$('id_menu').addEvent('change', function()
	{
		// Current page ID
		var id_current = ($('id_page').value) ? $('id_page').value : '0';
		
		// Parent page ID
		var id_parent = ($('origin_id_parent').value) ? $('origin_id_parent').value : '0';
		
		var xhr = new Request.HTML(
		{
			url: admin_url + 'page/get_parents_select/' + $('id_menu').value + '/' + id_current + '/' + id_parent,
			method: 'post',
			update: 'id_parent'
		}).send();
	});
	$('id_menu').fireEvent('change');
	
	
	// Auto-generate Main title
	$$('.data .title').each(function(input, idx)
	{
		input.addEvent('keyup', function(e)
		{
			$('main-title').set('text', this.value);
		});
	});


	// Auto-generates URL
	<?php if (empty($id_page)) :?>
		<?php foreach (Settings::get_languages() as $lang) :?>

			ION.initCorrectUrl('title_<?= $lang['lang']?>', 'url_<?= $lang['lang']?>');

		<?php endforeach ;?>
	<?php endif; ?>
	

	// Copy content
	$('copy_lang').addEvent('click', function(e)
	{
		e.stop();
	 	
		var url = admin_url + 'lang/copy_lang_content';

		var data = {
			'case': 'page',
			'id_page': $('id_page').value,
			'include_articles': (($('copy_article').getProperty('checked')) == true) ? 'true' : 'false',
			'from' : $('lang_copy_from').value,
			'to' : $('lang_copy_to').value
		};
	 	
 		MUI.sendData(url, data);
	});

	
	/**
	 * Articles view / type select for articles list
	 *
	 */
	$$('#articleList<?= $id_page ?> .type').each(function(item, idx)
	{
		ION.initArticleTypeEvent(item);
	});

	$$('#articleList<?= $id_page ?> .view').each(function(item, idx)
	{
		ION.initArticleViewEvent(item);
	});
	
	/**
	 * Makes article title draggable
	 *
	 */
	$$('#articleList<?= $id_page ?> .article').each(function(item, idx)
	{
		var id_article = item.getProperty('rel');
		var title = item.get('text');
		
		// Drag / Drop
		ION.makeLinkDraggable(item, 'article');
		
		// Edit link
		item.addEvent('click', function(e) {
			e.stop();
			MUI.updateContent({'element': $('mainPanel'),'loadMethod': 'xhr','url': admin_url + 'article/edit/' + id_article,'title': Lang.get('ionize_title_edit_article') + ' : ' + title});
		});
	});
	
	
	/** 
	 * Calendars
	 *
	 */
	datePicker.attach();
	
	
	/** 
	 * Show current tabs
	 */
 	ION.displayLangBlock('.data', '<?= Settings::get_lang('first') ?>');
	ION.displayBlock('.description', '<?= Settings::get_lang('first') ?>' + '-description');
	ION.displayBlock('.keywords', '<?= Settings::get_lang('first') ?>' + '-keywords');
	ION.displayBlock('.permanent_url', '<?= Settings::get_lang('first') ?>' + '-permanent_url');
	if ($('tab-articles'))
	{
		ION.displayBlock('.articles', 'articles');
	}
	
	/** 
	 * Add events to tabs
	 * - Lang Tab Events 
	 * - Options Tab Events
	 * - Wysiwyg buttons
	 */
	<?php foreach(Settings::get_languages() as $lang) :?>
		$('tab-<?= $lang["lang"] ?>').addEvent('click', function()
		{
			ION.displayLangBlock('.data', '<?= $lang["lang"] ?>'); 
			ION.setOpenTabToCookie('.data', '<?= $lang["lang"] ?>');
		});
		$('tab-<?= $lang["lang"] ?>-description').addEvent('click', function(){ ION.displayBlock('.description', '<?= $lang["lang"] ?>-description'); });
		$('tab-<?= $lang["lang"] ?>-keywords').addEvent('click', function(){ ION.displayBlock('.keywords', '<?= $lang["lang"] ?>-keywords'); });
		
		if ($('tab-<?= $lang["lang"] ?>-permanent_url'))
		{
			$('tab-<?= $lang["lang"] ?>-permanent_url').addEvent('click', function(){ ION.displayBlock('.permanent_url', '<?= $lang["lang"] ?>-permanent_url'); });
		}
	<?php endforeach ;?>
	

	/** 
	 * MediaManager
	 * The Media Manager manage pictures, music, videos, and other files add / remove / sorting
	 *
	 */
	<?php if (!empty($id_page)) :?>
		var mediaManager = new IonizeMediaManager(
		{
			baseUrl: base_url,
			adminUrl: admin_url,
			parent:'page', 
			idParent:'<?= $id_page ?>', 
			pictureContainer:'pictureContainer', 
			musicContainer:'musicContainer', 
			videoContainer:'videoContainer',
			fileContainer:'fileContainer',
//			imageButton:'.imagemanager',
			fileButton:'.fmButton',
			wait:'waitPicture',
			mode:'<?= Settings::get('filemanager') ?>',
			thumbSize: <?= (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120 ;?>,		
			pictureArray:Array('<?= str_replace(',', "','", Settings::get('media_type_picture')) ?>'),
			musicArray:Array('<?= str_replace(',', "','", Settings::get('media_type_music')) ?>'),
			videoArray:Array('<?= str_replace(',', "','", Settings::get('media_type_video')) ?>'),
			fileArray:Array('<?= str_replace(',', "','", Settings::get('media_type_file')) ?>')
		});
	
	
		/** 
		 * Media tabs events
		 */
		$('tab-files').addEvent('click', function(){ 
			ION.displayBlock('.data', 'files');
			ION.setOpenTabToCookie('.data', 'files');
			if ( ! this.retrieve('loaded')) { mediaManager.loadMediaList('file'); this.store('loaded', true);}
		});
		$('tab-music').addEvent('click', function(){ 
			ION.displayBlock('.data', 'music'); 
			ION.setOpenTabToCookie('.data', 'music');
			if ( ! this.retrieve('loaded')) { mediaManager.loadMediaList('music'); this.store('loaded', true);}
		});
		$('tab-videos').addEvent('click', function(){ 
			ION.displayBlock('.data', 'videos'); 
			ION.setOpenTabToCookie('.data', 'videos');
			if ( ! this.retrieve('loaded')) { mediaManager.loadMediaList('video'); this.store('loaded', true);}
		});
		
		$('tab-pictures').addEvent('click', function() {
			ION.displayBlock('.data', 'pictures');
			ION.setOpenTabToCookie('.data', 'pictures');
			if ( ! this.retrieve('loaded')) { mediaManager.loadMediaList('picture'); this.store('loaded', true);}
		});
	
		// Displays the tab regarding to the one in cookie
		ION.diplayCookieTab();

	<?php endif ;?>

	
	if ($('tab-articles'))
	{
		/**
		 * Article create button link
		 */
		var item = $('articleCreate');
		if (item != null)
		{
			var url = item.getProperty('href');
	
			item.addEvent('click', function(e)
			{
				var e = new Event(e).stop();
				
				MUI.updateContent({
					'element': $('mainPanel'),
					'loadMethod': 'xhr',
					'url': url,
					'title': Lang.get('ionize_title_create_article')
				});
			});
		}
	
		
		/**
		 * Article list itemManager
		 *
		 */
		articleManager = new ION.ArticleManager({container: 'articleList<?= $id_page ?>', 'id_parent':'<?= $id_page ?>'});
	}



</script>