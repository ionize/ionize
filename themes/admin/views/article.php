
<form name="articleForm" id="articleForm" method="post" action="<?=site_url(config_item('admin_url').'/article/save/'.$id_article)?>">

	<input type="hidden" name="element" id="element" value="article" />
	<input type="hidden" name="id_article" id="id_article" value="<?= $id_article ?>" />
	<input type="hidden" name="rel" id="rel" value="<?= $id_page ?>.<?= $id_article ?>" />
	<input type="hidden" name="created" value="<?= $created ?>" />
	<input type="hidden" name="author" value="<?= $author ?>" />
	<input type="hidden" name="name" id="name" value="<?= $name ?>" />
	<input type="hidden" name="main_parent" id="main_parent" value="<?= $main_parent ?>" />
	
	<!-- JS storing element -->
	<input type="hidden" id="memory" />

	<div id="sidecolumn" class="close">

		<!-- Main informations -->
		<div class="info">

			<?php if ($id_article != '') :?>

				<?php if ($this->connect->is('super-admins') ) :?>
					<dl class="small compact">
						<dt><label>ID</label></dt>
						<dd><span class="lite"><?= $id_article ?></span></dd>
					</dl>
				<?php endif ;?>
				

				<?php if (humanize_mdate($logical_date, Settings::get('date_format')) != '') :?>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_date') ?></label></dt>
						<dd><?= humanize_mdate($logical_date, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($logical_date, '%H:%m:%s') ?></span></dd>
					</dl>
				<?php endif ;?>

				<dl class="small compact">
					<dt><label><?= lang('ionize_label_created') ?></label></dt>
					<dd><?= humanize_mdate($created, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($created, '%H:%m:%s') ?></span></dd>
				</dl>
		
				<?php if (humanize_mdate($updated, Settings::get('date_format')) != '') :?>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_updated') ?></label></dt>
						<dd><?= humanize_mdate($updated, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($updated, '%H:%m:%s') ?></span></dd>
					</dl>
				<?php endif ;?>
				
				<?php if (humanize_mdate($publish_on, Settings::get('date_format')) != '') :?>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_publish_on') ?></label></dt>
						<dd><?= humanize_mdate($publish_on, Settings::get('date_format')) ?> <span class="lite"><?= humanize_mdate($publish_on, '%H:%m:%s') ?></span></dd>
					</dl>
				<?php endif ;?>
			
			<?php endif ;?>
				

			<!-- Link ? -->
			<?php if ($id_article != '') :?>
			
				<div id="linkContainer"></div>
				
			<?php endif ;?>



			<!-- Modules PlaceHolder -->
			<?php if ( ! empty($id_article)) :?>
			
				<?= get_modules_addons('article', 'side_top'); ?>
			
			
			<?php endif ;?>


		</div>
			


		<div id="options">

			<!-- Options -->
			<h3 class="toggler"><?= lang('ionize_title_options') ?></h3>
		
			<div class="element">

				<!-- Existing Article -->
				<?php if( ! empty($id_article)) :?>

					<!-- Parent pages list -->
					<dl class="small dropPageInArticle">
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
								
										<li rel="<?= $page['id_page'] ?>.<?= $id_article ?>" class="parent_page"><a class="icon right unlink"></a><a class="page"><span class="link-img page left mr5<?php if($page['main_parent'] == '1') :?> main-parent<?php endif; ?>"></span><?= $title ?></a></li>
								
									<?php endforeach ;?>
									
								</ul>
								<!--
								<input type="text" id="new_parent" class="inputtext w140 italic empty nofocus droppable" alt="<?= lang('ionize_label_drop_page_here') ?>"></input>
								-->
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
						
						<!-- Category create button -->
						<a onclick="javascript:ION.formWindow('category', 'categoryForm', '<?= lang('ionize_title_category_new') ?>', 'category/get_form/article/<?= $id_article ?>', {width:360, height:230})"><?= lang('ionize_label_new_category') ?></a>

					</dd>
				</dl>

				<!-- Tags 
				<dl class="small">
					<dt>
						<label for="template"><?= lang('ionize_label_tags') ?></label>
					</dt>
					<dd>
						<textarea id="tags" name="tags" class="inputtext w140 h40" type="text" onkeyup="formManager.toLowerCase(this, 'tags');"><?= $tags ?></textarea>
					</dd>
				</dl>
				-->
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
						<label for="logical_date"><?= lang('ionize_label_date') ?></label>
					</dt>
					<dd>
						<input id="logical_date" name="logical_date" type="text" class="inputtext w120 date" value="<?= humanize_mdate($logical_date, Settings::get('date_format'). ' %H:%m:%s') ?>" />
					</dd>
				</dl>
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
						
						<div id="permanentUrlTab" class="mainTabs small gray">
							<ul class="tab-menu">
								<?php foreach(Settings::get_languages() as $language) :?>
									<li><a><?= ucfirst($language['lang']) ?></a></li>
								<?php endforeach ;?>
							</ul>
							<div class="clear"></div>
						</div>
						<div id="permanentUrlTabContent">
						
							<?php foreach(Settings::get_languages() as $language) :?>
								<?php
									$lang = (count(Settings::get_online_languages()) > 1) ? $language['lang'].'/' : '';
								?>
								<div class="tabcontent">
									<textarea id="permanent_url_<?= $language['lang'] ?>" name="permanent_url_<?= $language['lang'] ?>" class="h40" style="border-top:none;width:142px;" onclick="javascript:this.select();" readonly="readonly"><?= base_url().$lang ?><?= $page['urls'][$language['lang']] ?>/<?= ${$language['lang']}['url'] ?></textarea>
								</div>
							<?php endforeach ;?>
						
						</div>
						
					</dd>
				</dl>

			</div>

			<?php endif ;?>
			-->
			
			
			<!-- Modules PlaceHolder -->
			<?php if ( ! empty($id_article)) :?>
			
				<?= get_modules_addons('article', 'side_bottom'); ?>
			
			
			<?php endif ;?>
			
			
		</div>	<!-- /options -->
	
	</div> <!-- /sidecolumn -->



	<div id="maincolumn">

		<fieldset>

		<!-- Existing article -->
		<?php if( ! empty($id_article)) :?>
			
			<?php
				
				$title = ${Settings::get_lang('default')}['title'];
				if ($title == '') $title = $name;
			
			?>

			<h2 class="main article" id="main-title"><?= $title ?></h2>

			<?php if( ! empty($breadcrump)) :?>

				<div style="margin: -15px 0pt 20px 72px;">
							
					<p><span class="lite"><?= lang('ionize_label_article_context_edition') ?> : </span><?=$breadcrump?></p>
					
				</div>
			
			<?php endif ;?>



		<!-- New article -->
		<?php else :?>
			
			<h2 class="main article" id="main-title"><?= lang('ionize_title_new_article') ?></h2>
			
			<input type="hidden" name="id_page" id="id_page" value="<?= $id_page ?>" />
			
			<!-- Where is the article ? -->
			<dl>
				<dt><label><?= lang('ionize_label_article_in') ?></label></dt>
				<dd class="lite"><?= $menu ?> > <?= $parent ?></dd>
			</dl>	


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


		</fieldset>

		<fieldset id="blocks" class="mt20">
	
			<!-- Tabs -->
			<div id="articleTab" class="mainTabs">
				
				<ul class="tab-menu">
					
					<?php foreach(Settings::get_languages() as $language) :?>
					
						<li class="tab_article<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?= $language['lang'] ?>"><a><?= ucfirst($language['name']) ?></a></li>
					
					<?php endforeach ;?>
					
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="fileTab"><a><?= lang('ionize_label_files') ?></a></li>
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="musicTab"><a><?= lang('ionize_label_music') ?></a></li>
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="videoTab"><a><?= lang('ionize_label_videos') ?></a></li>
					<li class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>" id="pictureTab"><a><?= lang('ionize_label_pictures') ?></a></li>

				</ul>
				<div class="clear"></div>
			
			</div>

			<div id="articleTabContent">

				<!-- Text block -->
				<?php foreach(Settings::get_languages() as $language) :?>
					
				<?php $lang = $language['lang']; ?>

				<div class="tabcontent <?= $lang ?>">

						<p class="clear h15">
							<a class="right icon copy copyLang" rel="<?= $lang ?>" title="<?= lang('ionize_label_copy_to_other_languages') ?>"></a>
						</p>
			
						<!-- title -->
						<dl class="first">
							<dt>
								<label for="title_<?= $lang ?>"><?= lang('ionize_label_title') ?></label>
							</dt>
							<dd>
								<input id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="inputtext title" type="text" value="<?= ${$lang}['title'] ?>"/>
							</dd>
						</dl>
	
						<!-- sub title -->
						<dl>
							<dt>
								<label for="subtitle_<?= $lang ?>"><?= lang('ionize_label_subtitle') ?></label>
							</dt>
							<dd>
								<textarea id="subtitle_<?= $lang ?>" name="subtitle_<?= $lang ?>" class="textarea subtitleTiny h30" type="text"><?= ${$lang}['subtitle'] ?></textarea>
<!--								<a class="icon edit subtitle"></a> -->
							</dd>
						</dl>

						<!-- URL -->
						<dl class="mt15">
							<dt>
								<label for="url_<?= $lang ?>"><?= lang('ionize_label_url') ?></label>
							</dt>
							<dd>
								<input id="url_<?= $lang ?>" name="url_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['url'] ?>"/>
							</dd>
						</dl>
				
				
						<!-- Meta Title : Browser window title -->
						<dl class="mb20">
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
	
						<!-- Text -->
						<div>
							<textarea id="content_<?= $lang ?>" name="content_<?= $lang ?>" class="tinyTextarea w600 h260" rel="<?= $lang ?>"><?= htmlentities(${$lang}['content'], ENT_QUOTES, 'utf-8') ?></textarea>
							
							<p class="clear h15">
								<button id="wysiwyg_<?= $lang ?>" type="button" class="light-button left" onclick="tinymce.execCommand('mceToggleEditor',false,'content_<?= $lang ?>');return false;"><?= lang('ionize_label_toggle_editor') ?></button>
							</p>
						</div>

				</div>
				<?php endforeach ;?>
	
	
				<!-- Files -->
				<div class="tabcontent">
				
					<p class="h20">
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
	<!--					<a class="fmButton right"><img src="<?= theme_url() ?>images/icon_16_plus.png" /> <?= lang('ionize_label_attach_media') ?></a>-->
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
	ION.initAccordion('.toggler', 'div.element', true, 'articleAccordion');
		

	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
	ION.initLabelHelpLinks('#articleForm');


	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	ION.initToolbox('article_toolbox');
	
	
	/**
	 * Article element in each of its parent context
	 * 
	 */
	ION.initDroppable();
	 
	 
	/**
	 * Calendars init
	 *
	 */
	ION.initDatepicker();

	/**
	 * Add links on each parent page
	 *
	 */
	$$('#parent_list li.parent_page').each(function(item, idx)
	{
		ION.addParentPageEvents(item);
	});
	 
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
		 	
	 		ION.sendData(url, data);
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
	 * Copy Lang data to other languages dynamically
	 *
	 */
	ION.initCopyLang('.copyLang', Array('title', 'subtitle', 'url', 'content', 'meta_title'));
	
	var nbCategories = ($('categories').getElements('option')).length;
	if (nbCategories > 5)
	{
		$$('#categories select').setStyles({
			'height': (nbCategories * 15) + 'px'
		});
	}
	
	/** 
	 * Show current tabs
	 */
	var articleTab = new TabSwapper({tabsContainer: 'articleTab', sectionsContainer: 'articleTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'articleTab' });
	new TabSwapper({tabsContainer: 'permanentUrlTab', sectionsContainer: 'permanentUrlTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'permanentUrlTab' });


	/**
	 * TinyEditors
	 * Must be called after tabs init.
	 *
	 */
	ION.initTinyEditors('.tab_article', '#articleTabContent .tinyTextarea');
	
	<?php if (!empty($id_article)) :?>
	
		/**
		 * Indexed XHR update
		 * Categories XHR update
		 *
		 */
		// Indexed
		$('indexed').addEvent('click', function(e)
		{
			var value = (this.checked) ? '1' : '0';
			ION.JSON('article/update_field', {'field': 'indexed', 'value': value, 'id_article': $('id_article').value});
		});

		// Dates
		ION.datePicker.options['onClose'] = function()	
		{
			ION.JSON('article/update_field', {'field': ION.datePicker.input.id, 'value': ION.datePicker.input.value, 'type':'date', 'id_article': $('id_article').value});
		}

		// Categories
		var categoriesSelect = $('categories').getFirst('select');
		categoriesSelect.addEvent('change', function(e)
		{
			var ids = new Array();
			var sel = this;
			for (var i = 0; i < sel.options.length; i++) {
				if (sel.options[i].selected) ids.push(sel.options[i].value);
			}		
			ION.JSON('article/update_categories', {'categories': ids, 'id_article': $('id_article').value});
		});

		
		// Link to page or article or what else...
		if ($('linkContainer'))
		{
			ION.HTML(admin_url + 'article/get_link', {'id_page': '<?= $id_page ?>', 'id_article': '<?= $id_article ?>'}, {'update': 'linkContainer'});
		}


		/**
		 * Get Content Elements Tabs & Elements
		 *
		 */
		$('desktop').store('tabSwapper', articleTab);
		ION.getContentElements('article', '<?= $id_article ?>');


		/** 
		 * Media Manager & tabs events
		 *
		 */
		mediaManager.initParent('article', '<?= $id_article ?>');
		
		mediaManager.loadMediaList('file');
		mediaManager.loadMediaList('music');
		mediaManager.loadMediaList('picture');
		mediaManager.loadMediaList('video');
		


	<?php endif ;?>

</script>