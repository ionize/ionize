
<form name="pageForm" id="pageForm" method="post" action="<?= admin_url() . 'page/save'?>">

	<input type="hidden" name="element" id="element" value="page" />
	<input type="hidden" name="action" id="action" value="save" />
	<input type="hidden" name="id_menu" value="<?= $id_menu ?>" />
	<input type="hidden" name="created" value="<?= $created ?>" />
	<input type="hidden" name="id_page" id="id_page" value="<?= $id_page ?>" />
	<input type="hidden" name="rel" id="rel" value="<?= $id_page ?>" />
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
		
				<?php if (humanize_mdate($logical_date, Settings::get('date_format')) != '') :?>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_date') ?></label></dt>
						<dd><?= humanize_mdate($logical_date, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($logical_date, '%H:%m:%s') ?></span></dd>
					</dl>
				<?php endif ;?>

				<dl class="compact small">
					<dt><label><?= lang('ionize_label_created') ?></label></dt>
					<dd><?= humanize_mdate($created, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($created, '%H:%m:%s') ?></span></dd>
				</dl>
		
				<?php if (humanize_mdate($updated, Settings::get('date_format')) != '') :?>
					<dl class="compact small">
						<dt><label><?= lang('ionize_label_updated') ?></label></dt>
						<dd><?= humanize_mdate($updated, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($updated, '%H:%m:%s') ?></span></dd>
					</dl>
				<?php endif ;?>

				<!-- Internal / External link Info -->
				<dl class="small compact" id="link_info"></dl>

			</div>
			
		<?php endif ;?>


		<div id="options" class="mt20">

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
				

				<?php if ($id_page != '') :?>
				<!-- Internal / External link -->
				<dl class="small last dropArticleAsLink dropPageAsLink">
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
				<?php endif ;?>

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
						<label for="logical_date"><?= lang('ionize_label_date') ?></label>
					</dt>
					<dd>
						<input id="logical_date" name="logical_date" type="text" class="inputtext w120 date" value="<?= humanize_mdate($logical_date, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>
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
					
						<!-- Tabs -->
						<div id="metaDescriptionTab" class="mainTabs small gray">
							<ul class="tab-menu">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li><a><?= ucfirst($language['lang']) ?></a></li>
								<?php endforeach ;?>
							</ul>
							<div class="clear"></div>
						</div>
						<div id="metaDescriptionTabContent" class="w160">
						
							<?php foreach(Settings::get_languages() as $language) :?>
								<div class="tabcontent">
									<textarea id="meta_description_<?= $language['lang'] ?>" name="meta_description_<?= $language['lang'] ?>" class="h80" style="border-top:none;width:142px;"><?= ${$language['lang']}['meta_description'] ?></textarea>
								</div>
							<?php endforeach ;?>
						
						</div>

					</dd>
				</dl>
			
				<!-- Meta_Keywords -->
				<dl class="small last">
					<dt>
						<label title="<?= lang('ionize_help_page_meta') ?>"><?= lang('ionize_label_meta_keywords') ?></label>
					</dt>
					<dd>
						<!-- Tabs -->
						<div id="metaKeywordsTab" class="mainTabs small gray">
							<ul class="tab-menu">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li><a><?= ucfirst($language['lang']) ?></a></li>
								<?php endforeach ;?>
							</ul>
							<div class="clear"></div>
						</div>
						<div id="metaKeywordsTabContent" class="w160">
						
							<?php foreach(Settings::get_languages() as $language) :?>
								<div class="tabcontent">
									<textarea id="meta_keywords_<?= $language['lang'] ?>" name="meta_keywords_<?= $language['lang'] ?>" class="h40" style="border-top:none;width:142px;"><?= ${$language['lang']}['meta_keywords'] ?></textarea>
								</div>
							<?php endforeach ;?>
						
						</div>

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
						<div class="w100 left">
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
						<!-- Tabs -->
						<div id="permanentUrlTab" class="mainTabs small gray">
							<ul class="tab-menu">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li><a><?= ucfirst($language['lang']) ?></a></li>
								<?php endforeach ;?>
							</ul>
							<div class="clear"></div>
						</div>
						<div id="permanentUrlTabContent" class="w160">
						
							<?php foreach(Settings::get_languages() as $language) :?>
								<?php
									$lang = (count(Settings::get_online_languages()) > 1) ? $language['lang'].'/' : '';
								?>
								<div class="tabcontent">
									<textarea id="permanent_url_<?= $language['lang'] ?>" name="permanent_url_<?= $language['lang'] ?>" class="h40" style="border-top:none;width:142px;" onclick="javascript:this.select();" readonly="readonly"><?= base_url().$lang ?><?= ${$language['lang']}['url'] ?></textarea>
								</div>
							<?php endforeach ;?>
						
						</div>

					</dd>
				</dl>
				
				<!-- Technical info 
				<dl class="small compact">
					<dt><label for="">Ordering</label></dt>
					<dd><?= $ordering ?></dd>
				</dl>
				-->

				<?php endif ;?>
			
			</div>
			
			
		</div>	<!-- /options -->
	
	</div> <!-- /sidecolumn -->



	<div id="maincolumn" class="">
		
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

		</fieldset>


		<fieldset class="mt10 clear">
	

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
		
				<?php $lang = $language['lang']; ?>
				
				<div class="tabcontent">
		
					<p class="clear h15">
						<a class="right icon copy copyLang" rel="<?= $lang ?>" title="<?= lang('ionize_label_copy_to_other_languages') ?>"></a>
					</p>

					<!-- title -->
					<dl class="first">
						<dt>
							<label for="title_<?= $lang ?>"><?= lang('ionize_label_title') ?></label>
						</dt>
						<dd>
							<input id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="inputtext title" type="text" value="<?= ${$lang}['title'] ?>" title="<?= lang('ionize_label_title') ?>"/>
						</dd>
					</dl>

					<!-- URL -->
					<dl>
						<dt>
							<label for="url_<?= $lang ?>" title="<?= lang('ionize_help_page_url') ?>"><?= lang('ionize_label_url') ?></label>
						</dt>
						<dd>
							<input id="url_<?= $lang ?>" name="url_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['url'] ?>" title="<?= lang('ionize_help_page_url') ?>" />
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

				</div>
				
			<?php endforeach ;?>


			<!-- Files -->
			<div class="tabcontent">
			
				<p class="h20">
<!--					<button class="fmButton right light-button plus"><?= lang('ionize_label_attach_media') ?></button> -->
					<button class="right light-button files" onclick="javascript:mediaManager.loadMediaList('file');return false;"><?= lang('ionize_label_reload_media_list') ?></button>
					<button class="left light-button delete" onclick="javascript:mediaManager.detachMediaByType('file');return false;"><?= lang('ionize_label_detach_all_files') ?></button>
				</p>
				
				<ul id="fileContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_file') ?></span>
				</ul>

			</div>

			<!-- Music -->
			<div class="tabcontent">
				
				<p class="h20"> 
<!--					<button class="fmButton right light-button plus"><?= lang('ionize_label_attach_media') ?></button> -->
					<button class="right light-button music" onclick="javascript:mediaManager.loadMediaList('music');return false;"><?= lang('ionize_label_reload_media_list') ?></button>
					<button class="left light-button delete" onclick="javascript:mediaManager.detachMediaByType('music');return false;"><?= lang('ionize_label_detach_all_musics') ?></button>
				</p>
				
				<ul id="musicContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_music') ?></span>
				</ul>

			</div>

			<!-- Videos -->
			<div class="tabcontent">
			
				<p class="h20">
<!--					<button class="fmButton right light-button plus"><?= lang('ionize_label_attach_media') ?></button>-->
					<button class="right light-button video" onclick="javascript:mediaManager.loadMediaList('video');return false;"><?= lang('ionize_label_reload_media_list') ?></button>
					<button class="left light-button delete" onclick="javascript:mediaManager.detachMediaByType('video');return false;"><?= lang('ionize_label_detach_all_videos') ?></button>
				</p>

				<ul id="videoContainer" class="sortable-container">
					<span><?= lang('ionize_message_no_video') ?></span>
				</ul>

			</div>

			<!-- Pictures -->
			<div class="tabcontent">
			
				<p class="h20">
<!--					<button class="fmButton right light-button plus"><?= lang('ionize_label_attach_media') ?></button>-->
					<button class="right light-button pictures" onclick="javascript:mediaManager.loadMediaList('picture');return false;"><?= lang('ionize_label_reload_media_list') ?></button>
					<button class="left light-button delete" onclick="javascript:mediaManager.detachMediaByType('picture');return false;"><?= lang('ionize_label_detach_all_pictures') ?></button>
					<button class="left light-button refresh" onclick="javascript:mediaManager.initThumbsForParent();return false;"><?= lang('ionize_label_init_all_thumbs') ?></button>

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
<!--						<input id="articleCreate" type="button" class="light-button plus right" value="<?= lang('ionize_label_add_article') ?>" rel="<?= $id_page ?>" />-->

							<button class="right light-button helpme type"><?= lang('ionize_label_help_articles_types') ?></button>

							<!-- Droppable to link one article to this page -->
							<input type="text" id="new_article" class="inputtext w120 italic droppable empty nofocus" alt="<?= lang('ionize_label_drop_article_here') ?>"></input>
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
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler', 'div.element', true);


	ION.initHelp('#articles .type.helpme', 'article_type', Lang.get('ionize_title_help_articles_types'));

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#pageForm');


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
	ION.initDatepicker();



	/**
	 * Copy Lang data to other languages dynamically
	 *
	 */
	ION.initCopyLang('.copyLang', Array('title', 'subtitle', 'url', 'meta_title'));

	// Remove link event
	if ($('link_remove'))
	{
		$('link_remove').addEvent('click', function(e)
		{
			e.stop();
			ION.removeElementLink();
		});

		// External link Add
		$('link').addEvent('blur', function(e)
		{
			if (ION.checkUrl(this.value))
			{
				// type of receiver, url, ID of textarea which receive the link after save.
				ION.addExternalLink('page', this.value, 'link');
			}
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
			onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
				$('id_parent').empty();
				if (Browser.ie)
					$('id_parent').set('html', responseHTML);
				else
					$('id_parent').adopt(responseTree);
			}
		}).send();

	});
	$('id_menu').fireEvent('change');
	
	
	// Auto-generate Main title
	$$('.tabcontent .title').each(function(input, idx)
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


	// Tabs
	var pageTab = new TabSwapper({tabsContainer: 'pageTab', sectionsContainer: 'pageTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'mainTab' });
	new TabSwapper({tabsContainer: 'metaDescriptionTab', sectionsContainer: 'metaDescriptionTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'metaDescriptionTab'	});
	new TabSwapper({tabsContainer: 'metaKeywordsTab', sectionsContainer: 'metaKeywordsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'metaKeywordsTab' });
	new TabSwapper({tabsContainer: 'permanentUrlTab', sectionsContainer: 'permanentUrlTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'permanentUrlTab' });
	

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
	 	
 		ION.sendData(url, data);
	});



	<?php if (!empty($id_page)) :?>

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

	<?php endif ;?>

</script>