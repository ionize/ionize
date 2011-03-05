
<form name="articleForm" id="articleForm" method="post" action="<?=site_url(config_item('admin_url').'/article/save/'.$id_article)?>">

	<input type="hidden" name="element" id="element" value="article" />
	<input type="hidden" name="id_article" id="id_article" value="<?= $id_article ?>" />
	<input type="hidden" name="rel" id="rel" value="<?= $id_page ?>.<?= $id_article ?>" />
	<input type="hidden" name="created" value="<?= $created ?>" />
	<input type="hidden" name="author" value="<?= $author ?>" />
	<input type="hidden" name="name" id="name" value="<?= $name ?>" />
	
	<div id="sidecolumn" class="close">

		<!-- Main informations -->
		<div class="info">

			<?php if ($id_article != '') :?>

				<dl class="small compact">
					<dt><label><?= lang('ionize_label_created') ?></label></dt>
					<dd><?= humanize_mdate($created, Settings::get('date_format'). ' %H:%m:%s') ?></dd>
				</dl>
		
				<?php if (humanize_mdate($updated, Settings::get('date_format'). ' %H:%m:%s') != '') :?>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_updated') ?></label></dt>
						<dd><?= humanize_mdate($updated, Settings::get('date_format'). ' %H:%m:%s') ?></dd>
					</dl>
				<?php endif ;?>
				
				<?php if (humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%m:%s') != '') :?>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_publish_on') ?></label></dt>
						<dd><?= humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%m:%s') ?></dd>
					</dl>
				<?php endif ;?>
			
			<?php endif ;?>
				
			<!-- Internal / External link Info -->
			<dl class="compact" id="link_info"></dl>

		</div>
			


		<div id="options">

			<!-- Options -->
			<h3 class="toggler"><?= lang('ionize_title_options') ?></h3>
		
			<div class="element">

				<!-- Existing Article -->
				<?php if( ! empty($id_article)) :?>

					<!-- Parent pages list -->
					<dl class="small">
						<dt>
							<label for="template" title="<?= lang('ionize_help_article_context') ?>"><?= lang('ionize_label_parents') ?></label>
						</dt>
						<dd>
							<div id="parents">
								<ul class="parent_list" id="parent_list">
								
									<?php foreach ($pages_list as $page) :?>
										
										<?php
											
											$title = ($page['title'] != '') ? $page['title'] : $page['name'];
											
											// All REL or ID which permit the DOM identification of one article MUST be written like this.
									//		$rel = $page['id_page']. '.' .$id_article;
										?>
								
										<li rel="<?= $page['id_page'] ?>.<?= $id_article ?>" class="parent_page"><a class="icon right unlink"></a><a class="page"><span class="link-img page left"></span><?= $title ?></a></li>
								
									<?php endforeach ;?>
								
								</ul>
								<input type="text" id="new_parent" class="inputtext w140 italic droppable empty nofocus" alt="<?= lang('ionize_label_drop_page_here') ?>"></input>
							</div>
						</dd>
					</dl>
				
				<!-- New Article -->
				<?php endif ;?>
					

			
				<!-- Indexed content -->
				<dl class="small">
					<dt>
						<label for="indexed" title="<?= lang('ionize_help_indexed') ?>"><?= lang('ionize_label_indexed') ?></label>
					</dt>
					<dd>
						<input id="indexed" name="indexed" type="checkbox" class="inputcheckbox" <?php if ($indexed == 1):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

				<!-- Internal / External link -->
				<dl class="small">
					<dt>
						<label for="link" title="<?= lang('ionize_help_article_link') ?>"><?= lang('ionize_label_link') ?></label>
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

				<!-- Flag -->
				<dl class="small">
					<dt>
						<label for="flag0" title="<?= lang('ionize_help_flag') ?>"><?= lang('ionize_label_flag') ?></label>
					</dt>
						<dd>
							<label class="flag flag0"><input id="flag0" name="flag" class="inputradio" type="radio" <?php if ($flag == 0):?> checked="checked" <?php endif;?> value="0" /></label>
							<label class="flag flag1"><input name="flag" class="inputradio" type="radio" <?php if ($flag == 1):?> checked="checked" <?php endif;?> value="1" /></label>
							<label class="flag flag2"><input name="flag" class="inputradio" type="radio" <?php if ($flag == 2):?> checked="checked" <?php endif;?> value="2" /></label>
							<label class="flag flag3"><input name="flag" class="inputradio" type="radio" <?php if ($flag == 3):?> checked="checked" <?php endif;?> value="3" /></label>
							<label class="flag flag4"><input name="flag" class="inputradio" type="radio" <?php if ($flag == 4):?> checked="checked" <?php endif;?> value="4" /></label>
							<label class="flag flag5"><input name="flag" class="inputradio" type="radio" <?php if ($flag == 5):?> checked="checked" <?php endif;?> value="5" /></label>
						</dd>
					</dt>
				</dl>

			</div>
			
			
			
			<!-- Advanced options -->
			<h3 class="toggler"><?= lang('ionize_title_advanced') ?></h3>
			
			<div class="element">

				<!-- Categories -->
				<dl class="small">
					<dt>
						<label for="template"><?= lang('ionize_label_categories') ?></label>
					</dt>
					<dd>
						<div id="categories">
							<?= $categories ?>
						</div>
						
						<!-- Categories list 
						<a onclick="javascript:MUI.dataWindow('Categories', '<?= lang('ionize_title_categories') ?>', '<?= admin_url() ?>category/get_categories/article/<?= $id_article ?>', {width:450, height:300});"><?= lang('ionize_label_edit_categories') ?></a><br/>
						-->
						
						<!-- Category create button 
						<a onclick="javascript:MUI.formWindow('Category', 'categoryForm', '<?= lang('ionize_title_category_new') ?>', 'category/get_form/article/<?= $id_article ?>', {width:360, height:230})"><?= lang('ionize_label_new_category') ?></a>
						-->
						
					</dd>
				</dl>

				<!-- Tags -->
				<dl class="small">
					<dt>
						<label for="template"><?= lang('ionize_label_tags') ?></label>
					</dt>
					<dd>
						<textarea id="tags" name="tags" class="inputtext w140 h40" type="text" onkeyup="formManager.toLowerCase(this, 'tags');"><?= $tags ?></textarea>
					</dd>
				</dl>

				<!-- Existing Tags 
				<dl class="small last">
					<dt>
						<label for="template"><?= lang('ionize_label_existing_tags') ?></label>
					</dt>
					<dd><?= $existing_tags ?></dd>
				</dl>
				-->

			</div>

			
			<!-- Dates -->
			<h3 class="toggler"><?= lang('ionize_title_dates') ?></h3>
			
			<div class="element">
				<dl class="small">
					<dt>
						<label for="publish_on"><?= lang('ionize_label_publish_on') ?></label>
					</dt>
					<dd>
						<input id="publish_on" name="publish_on" type="text" class="inputtext w120 date" value="<?= humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>
			
				<dl class="small last">
					<dt>
						<label for="publish_off"><?= lang('ionize_label_publish_off') ?></label>
					</dt>
					<dd>
						<input id="publish_off" name="publish_off" type="text" class="inputtext w120 date"  value="<?= humanize_mdate($publish_off, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>
			
			
			</div>

			<!-- Comments 
			<h3 class="toggler"><?= lang('ionize_title_comments') ?></h3>
			
			<div class="element">

				<dl class="small">
					<dt>
						<label for="comment_allow"><?= lang('ionize_label_comment_allow') ?></label>
					</dt>
					<dd>
						<input id="comment_allow" name="comment_allow" type="checkbox" class="inputcheckbox" <?php if ($comment_allow == 1):?> checked="checked" <?php endif;?>  />
					</dd>
				</dl>

				<dl class="small">
					<dt>
						<label for="comment_autovalid"><?= lang('ionize_label_comment_autovalid') ?></label>
					</dt>
					<dd>
						<input id="comment_autovalid" name="comment_autovalid" type="checkbox" class="inputcheckbox" <?php if ($comment_autovalid == 1):?> checked="checked" <?php endif;?>  />
					</dd>
				</dl>

				<dl class="small last">
					<dt>
						<label for="comment_expire"><?= lang('ionize_label_comment_expire') ?></label>
					</dt>
					<dd>
						<input id="comment_expire" name="comment_expire" type="text" class="inputtext w120 date"  value="<?= humanize_mdate($comment_expire, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>

			</div>
			
			-->


			<!-- Copy Content -->
			<?php if( ! empty($id_article)) :?>

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
				
					<!-- Submit button  -->
					<dl class="small">
						<dt>&#160;</dt>
						<dd>
							<input type="submit" value="<?= lang('ionize_button_copy_content') ?>" class="submit" id="copy_lang">
						</dd>
					</dl>
				
				</div>

			<?php endif ;?>

			<!-- Other info : Permanenet URL, etc. 
			<?php if (!empty($id_article) && !empty($page['name'])) :?>

			<h3 class="toggler"><?= lang('ionize_title_informations') ?></h3>
			
			<div class="element">

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
								<textarea id="permanent_url_<?= $language['lang'] ?>" class="w140 h80" onclick="javascript:this.select();" readonly="readonly"><?= base_url().$lang ?><?= $page['urls'][$language['lang']] ?>/<?= ${$language['lang']}['url'] ?></textarea>
							</div>
						<?php endforeach ;?>
						
					</dd>
				</dl>

			</div>

			<?php endif ;?>
			
-->
		</div>	<!-- /options -->
	
	</div> <!-- /sidecolumn -->



	<div id="maincolumn">

		<fieldset>

		<!-- Existing article -->
		<?php if( ! empty($id_article)) :?>
			
			<?php
				
				$title = ${Settings::get_lang('default')}['title'];
				
//				if ($title == '') $title = ${Settings::get_lang('default')}['name'];
				if ($title == '') $title = $name;
			
			?>

			<h2 class="main article" id="main-title"><?= $title ?></h2>

		<!-- New article -->
		<?php else :?>
			
			<h2 class="main article" id="main-title"><?= lang('ionize_title_new_article') ?></h2>
			
			<input type="hidden" name="id_page" id="id_page" value="<?= $id_page ?>" />
			
			<!-- Where is the article ? -->
			<dl>
				<dt><label><?= lang('ionize_label_article_in') ?></label></dt>
				<dd class="lite"><?= $menu ?> > <?= $parent ?></dd>
			</dl>	



			
			<!-- Menu & parent
			<dl>
				<dt>
					<label for="id_menu"><?= lang('ionize_label_menu') ?></label>
				</dt>
				<dd>
					<?= $menus ?>
				</dd>
			</dl>
	
			<dl>
				<dt>
					<label for="id_parent"><?= lang('ionize_label_parent') ?></label>
				</dt>
				<dd>
					<?= $parent_select ?>
				</dd>
			</dl>	
			-->
		
			<!-- Ordering -->
			<dl>
				<dt >
					<label for="ordering"><?= lang('ionize_label_ordering') ?></label>
				</dt>
				<dd>
					<select name="ordering_select" id="ordering_select" class="select">
						<?php if($id_article) :?>
							<option value="<?= $ordering ?>"><?= $ordering ?></option>
						<?php endif ;?>
						<option value="first"><?= lang('ionize_label_ordering_first') ?></option>
						<option value="last"><?= lang('ionize_label_ordering_last') ?></option>
						<option id="ordering_select_after" value="after" <?php if( empty($articles)) :?>style="display:none"<?php endif ;?>><?= lang('ionize_label_ordering_after') ?></option>	
					</select>
				</dd>
				<dd>
					<select name="ordering_after" id="ordering_after" style="display:none;" class="select w140 mt5">
						<?php foreach($articles as $article) :?>
							<?php
								$title = ($article['title'] != '') ? $article['title'] : $article['name'];
							?>
							<option value="<?= $article['id_article'] ?>"><?= $title ?></option>
						<?php endforeach ;?>
					</select>
				</dd>
			</dl>
	
	
			<!-- Online / Offline -->
			<dl class="mb20">
				<dt>
					<label for="online" title="<?= lang('ionize_help_article_online') ?>"><?= lang('ionize_label_article_online') ?></label>
				</dt>
				<dd>
					<div>
						<input id="online" <?php if ($online == 1):?> checked="checked" <?php endif;?> name="online" class="inputcheckbox" type="checkbox" value="1"/>
					</div>
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
									$extend_field['content'] = ($extend_field['content'] != '') ? $extend_field['content'] : $extend_field['default_value'];
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

					<li id="tab-files" class="right<?php if( empty($id_article)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_files') ?></span></a></li>
					<li id="tab-music" class="right<?php if( empty($id_article)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_music') ?></span></a></li>
					<li id="tab-videos" class="right<?php if( empty($id_article)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_videos') ?></span></a></li>
					<li id="tab-pictures" class="right<?php if( empty($id_article)) :?> unactive<?php endif ;?>"><a><span><?= lang('ionize_label_pictures') ?></span></a></li>

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
							<label for="url_<?= $lang ?>"><?= lang('ionize_label_url') ?></label>
						</dt>
						<dd>
							<input id="url_<?= $lang ?>" name="url_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['url'] ?>"/>
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
			
					<!-- Meta Title : Browser window title -->
					<dl>
						<dt>
							<label for="meta_title_<?= $lang ?>" title="<?= lang('ionize_help_article_window_title') ?>"><?= lang('ionize_label_meta_title') ?></label>
						</dt>
						<dd>
							<input id="meta_title_<?= $lang ?>" name="meta_title_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['meta_title'] ?>"/>
						</dd>
					</dl>
			
					<!-- Online -->
					<?php if(count(Settings::get_languages()) > 1) :?>
					
						<dl>
							<dt>
								<label for="online_<?= $lang ?>" title="<?= lang('ionize_help_article_content_online') ?>"><?= lang('ionize_label_article_content_online') ?></label>
							</dt>
							<dd>
								<input id="online_<?= $lang ?>" <?php if (${$lang}['online'] == 1):?> checked="checked" <?php endif;?> name="online_<?= $lang ?>" class="inputcheckbox" type="checkbox" value="1"/>
							</dd>
						</dl>
					
					<?php else :?>
					
						<input id="online_<?= $lang ?>" name="online_<?= $lang ?>" type="hidden" value="1"/>
					
					<?php endif ;?>
			
					<!-- extend fields goes here... -->
					<?php if (Settings::get('use_extend_fields') == '1' && $id_article != '') :?>
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
											<textarea id="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" class="<?php if($extend_field['type'] == '3'):?> tinyTextarea <?php endif ;?> inputtext h80" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>"><?= $extend_field[$lang]['content'] ?></textarea>
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

										<!-- Date & Time -->
										<?php if ($extend_field['type'] == '7') :?>
										
											<input id="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" class="inputtext w120 date" type="text" name="cf_<?= $extend_field['id_extend_field'] ?>_<?= $lang ?>" value="<?= $extend_field['content']  ?>" />
											
										<?php endif ;?>

										
									</dd>
								</dl>	
									
							<?php endif ;?>
						<?php endforeach ;?>
					<?php endif ;?>

					<!-- Text -->
					<dl class="first">
						<dt>
							<label for="content_<?= $lang ?>"><?= lang('ionize_label_text') ?></label>
						</dt>
						<dd>
							<textarea id="content_<?= $lang ?>" name="content_<?= $lang ?>" class="tinyTextarea w600 h260"><?= htmlentities(${$lang}['content'], ENT_QUOTES, 'utf-8') ?></textarea>
						</dd>
					</dl>
<!--
Needs to be corrected.
			
					<dl>
						<dt>
							<label for="wysiwyg_<?= $lang ?>">&#160;</label>
						</dt>
						<dd>
							<button id="wysiwyg_<?= $lang ?>" type="button" class="button" onclick="toggleTinyMCE('content_<?=$lang?>');"><?= lang('ionize_label_toggle_editor') ?></button>
						</dd>
					</dl>
-->
		
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
			
				<div id="pictureContainer">
					<span><?= lang('ionize_message_no_picture') ?></span>
				</div>

			</div>

		</fieldset>
	
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
 	 * Text editor
	 */
	<?php if( Settings::get('texteditor') == '' || Settings::get('texteditor') == 'tinymce' ) :?>
		// TinyMCE control add on first language only
		tinyMCE.init(tinyMCEParam);
	<?php elseif( Settings::get('texteditor') == 'ckeditor' ) :?>
		// CKEditor
		// Language fields
		<?php foreach(Settings::get_languages() as $l) :?>
			if ($('content_<?= $l["lang"] ?>'))
			{
				if(CKEDITOR.instances['content_<?= $l["lang"] ?>'])
				{
					CKEDITOR.remove(CKEDITOR.instances['content_<?= $l["lang"] ?>']);
				}
				CKEDITOR.replace( 'content_<?= $l["lang"] ?>');
			}
		<?php endforeach ;?>
		// Extend filds
		<?php foreach($extend_fields as $extend_field) :?>
			<?php if ($extend_field['translated'] != '1') :?>
				<?php if ($extend_field['type'] == '2' OR $extend_field['type'] == '3') :?>
					if ($('cf_<?= $extend_field['id_extend_field'] ?>'))
					{
						if(CKEDITOR.instances['cf_<?= $extend_field['id_extend_field'] ?>'])
						{
							CKEDITOR.remove(CKEDITOR.instances['cf_<?= $extend_field['id_extend_field'] ?>']);
						}
						CKEDITOR.replace( 'cf_<?= $extend_field['id_extend_field'] ?>');
					}
				<?php endif ;?>
			<?php endif ;?>
		<?php endforeach ;?>
	<?php endif ;?>


	/**
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler', 'div.element');
		

	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
	MUI.initLabelHelpLinks('#articleForm');


	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	MUI.initToolbox('article_toolbox');


	/**
	 * Article element in each of its parent context
	 * 
	 */
	ION.initDroppable();
	 
	 
	/**
	 * Add links on each parent page
	 *
	 */
	$$('#parent_list li.parent_page').each(function(item, idx)
	{
		ION.addParentPageEvents(item);
	});
	 
	
	// Auto-generate Main title
	$$('.data .title').each(function(input, idx)
	{
		input.addEvent('keyup', function(e)
		{
			$('main-title').set('text', this.value);
		});
	});
	
	 
	// Auto-generates URL
	<?php if ($id_article == '') :?>
		<?php foreach (Settings::get_languages() as $lang) :?>

			ION.initCorrectUrl('title_<?= $lang['lang']?>', 'url_<?= $lang['lang']?>');

		<?php endforeach ;?>
	<?php endif; ?>
	 

	// Copy content
	if ($('copy_lang'))
	{
		$('copy_lang').addEvent('click', function(e)
		{
			e.stop();
	
			var url = admin_url + 'lang/copy_lang_content';
	
			var data = {
				'case': 'article',
				'id_article': $('id_article').value,
				'rel': $('rel').value,
				'from' : $('lang_copy_from').value,
				'to' : $('lang_copy_to').value
			};
		 	
	 		MUI.sendData(url, data);
		});
	}

	
	// Article ordering : 
	// - Show / hide article list depending on Ordering select
	// - Update the article select list after parent change
	if ($('id_page'))
	{
		$('ordering_select').addEvent('change', function(e)
		{
			var e = new Event(e).stop();
			var el = e.target;
			
			if (el.value == 'after'){ $('ordering_after').setStyle('display', 'block');}
			else { $('ordering_after').setStyle('display', 'none');	}
		});
	}

	/**
	 * Links interaction
	 *
	 */

	// Remove link event
	if ($('link_remove'))
	{
		$('link_remove').addEvent('click', function(e)
		{
			e.stop();
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
	

	
	/** 
	 * Calendars
	 *
	 */
	datePicker.attach();

	/** 
	 * Show current tabs
	 */
 	ION.displayLangBlock('.data', '<?= Settings::get_lang('first') ?>');	
	ION.displayBlock('.permanent_url', '<?= Settings::get_lang('first') ?>' + '-permanent_url');
	
	
	/** 
	 * Add events to tabs
	 * - Lang Tab Events 
	 * - Options Tab Events
	 * - Wysiwyg buttons
	 */
	<?php foreach(Settings::get_languages() as $l) :?>
	
		$('tab-<?= $l["lang"] ?>').addEvent('click', function()	
		{
			ION.displayLangBlock('.data', '<?= $l["lang"] ?>'); 
			ION.setOpenTabToCookie('.data', '<?= $l["lang"] ?>');
		});

		if ($('tab-<?= $l["lang"] ?>-permanent_url'))
		{
			$('tab-<?= $l["lang"] ?>-permanent_url').addEvent('click', function(){ ION.displayBlock('.permanent_url', '<?= $l["lang"] ?>-permanent_url'); });
		}
	
	<?php endforeach ;?>
	


	<?php if (!empty($id_article)) :?>
		/** 
		 * MediaManager
		 * The Media Manager manage pictures, music, videos, and other files add / remove / sorting
		 * Only visible if article exists
		 *
		 */
		var mediaManager = new IonizeMediaManager(
		{
			baseUrl:base_url,
			adminUrl: admin_url,
			parent:'article', 
			idParent:'<?= $id_article ?>', 
			pictureContainer:'pictureContainer', 
			musicContainer:'musicContainer', 
			videoContainer:'videoContainer',
			fileContainer:'fileContainer',
//			imageButton:'.imagemanager',
			fileButton:'.fmButton',
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


</script>