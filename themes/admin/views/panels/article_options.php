<?php

/**
 * Right Panel Article Options 
 *
 */

?>

<!-- Main informations -->
<div class="info">

	<?php if ($id_article != '') :?>

		<?php if (humanize_mdate($logical_date, Settings::get('date_format')) != '') :?>
			<dl class="small compact">
				<dt><label><?php echo lang('ionize_label_date'); ?></label></dt>
				<dd><?php echo humanize_mdate($logical_date, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($logical_date, '%H:%i:%s'); ?></span></dd>
			</dl>
		<?php endif ;?>

		<dl class="small compact">
			<dt><label><?php echo lang('ionize_label_created'); ?></label></dt>
			<dd><?php echo humanize_mdate($created, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($created, '%H:%i:%s'); ?></span></dd>
		</dl>

		<?php if (humanize_mdate($updated, Settings::get('date_format')) != '') :?>
			<dl class="small compact">
				<dt><label><?php echo lang('ionize_label_updated'); ?></label></dt>
				<dd><?php echo humanize_mdate($updated, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($updated, '%H:%i:%s'); ?></span></dd>
			</dl>
		<?php endif ;?>
		
		<?php if (humanize_mdate($publish_on, Settings::get('date_format')) != '') :?>
			<dl class="small compact">
				<dt><label><?php echo lang('ionize_label_publish_on'); ?></label></dt>
				<dd><?php echo humanize_mdate($publish_on, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($publish_on, '%H:%i:%s'); ?></span></dd>
			</dl>
		<?php endif ;?>
	
	<?php endif ;?>
		
	<!-- Internal / External link Info -->
	<dl class="compact" id="link_info"></dl>

</div>
	


<div id="options">

	<!-- Options -->
	<h3 class="toggler"><?php echo lang('ionize_title_options'); ?></h3>

	<div class="element">

		<!-- Existing Article -->
		<?php if( ! empty($id_article)) :?>

			<!-- Parent pages list -->
			<dl class="small dropPageInArticle">
				<dt>
					<label for="template" title="<?php echo lang('ionize_help_article_context'); ?>"><?php echo lang('ionize_label_parents'); ?></label>
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
						
								<li rel="<?php echo $page['id_page']; ?>.<?php echo $id_article; ?>" class="parent_page"><a class="icon right unlink"></a><a class="page"><span class="link-img page left"></span><?php echo $title; ?></a></li>
						
							<?php endforeach ;?>
						
						</ul>
						<!--
						<input type="text" id="new_parent" class="inputtext w140 italic empty nofocus droppable" alt="<?php echo lang('ionize_label_drop_page_here'); ?>"></input>
						-->
					</div>
				</dd>
			</dl>
		
		<!-- New Article -->
		<?php endif ;?>
			

	
		<!-- Indexed content -->
		<dl class="small">
			<dt>
				<label for="indexed" title="<?php echo lang('ionize_help_indexed'); ?>"><?php echo lang('ionize_label_indexed'); ?></label>
			</dt>
			<dd>
				<input id="indexed" name="indexed" type="checkbox" class="inputcheckbox" <?php if ($indexed == 1):?> checked="checked" <?php endif;?> value="1" />
			</dd>
		</dl>

		<!-- Internal / External link -->
		<?php if ($id_article != '' && $id_page != '0') :?>
		<dl class="small dropArticleAsLink dropPageAsLink">
			<dt>
				<label for="link" title="<?php echo lang('ionize_help_article_link'); ?>"><?php echo lang('ionize_label_link'); ?></label>
				<br/>
				
			</dt>
			<dd>
				<textarea id="link" name="link" class="inputtext w140 h40 droppable" alt="<?php echo lang('ionize_label_drop_link_here'); ?>"><?php echo $link; ?></textarea>
				<br />
				
				<a id="link_remove"><?php echo lang('ionize_label_remove_link'); ?></a><br/>
			</dd>
		</dl>
		<?php endif ;?>

		<!-- Flag -->
		<dl class="small">
			<dt>
				<label for="flag0" title="<?php echo lang('ionize_help_flag'); ?>"><?php echo lang('ionize_label_flag'); ?></label>
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
	<h3 class="toggler"><?php echo lang('ionize_title_advanced'); ?></h3>
	
	<div class="element">

		<!-- Categories -->
		<dl class="small">
			<dt>
				<label for="template"><?php echo lang('ionize_label_categories'); ?></label>
			</dt>
			<dd>
				<div id="categories">
					<?php echo $categories; ?>
				</div>
				
				<!-- Categories list 
				<a onclick="javascript:ION.dataWindow('Categories', '<?php echo lang('ionize_title_categories'); ?>', '<?php echo admin_url(); ?>category/get_categories/article/<?php echo $id_article; ?>', {width:450, height:300});"><?php echo lang('ionize_label_edit_categories'); ?></a><br/>
				-->
				
				<!-- Category create button -->
				<a onclick="javascript:ION.formWindow('Category', 'categoryForm', '<?php echo lang('ionize_title_category_new'); ?>', 'category/get_form/article/<?php echo $id_article; ?>', {width:360, height:230})"><?php echo lang('ionize_label_new_category'); ?></a>
				
				
			</dd>
		</dl>

		<!-- Tags -->
		<dl class="small">
			<dt>
				<label for="template"><?php echo lang('ionize_label_tags'); ?></label>
			</dt>
			<dd>
				<textarea id="tags" name="tags" class="inputtext w140 h40" type="text" onkeyup="formManager.toLowerCase(this, 'tags');"><?php echo $tags; ?></textarea>
			</dd>
		</dl>

		<!-- Existing Tags 
		<dl class="small last">
			<dt>
				<label for="template"><?php echo lang('ionize_label_existing_tags'); ?></label>
			</dt>
			<dd><?php echo $existing_tags; ?></dd>
		</dl>
		-->

	</div>

	
	<!-- Dates -->
	<h3 class="toggler"><?php echo lang('ionize_title_dates'); ?></h3>
	
	<div class="element">
		<dl class="small">
			<dt>
				<label for="logical_date"><?php echo lang('ionize_label_date'); ?></label>
			</dt>
			<dd>
				<input id="logical_date" name="logical_date" type="text" class="inputtext w120 date" value="<?php echo humanize_mdate($logical_date, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
			</dd>
		</dl>
		<dl class="small">
			<dt>
				<label for="publish_on"><?php echo lang('ionize_label_publish_on'); ?></label>
			</dt>
			<dd>
				<input id="publish_on" name="publish_on" type="text" class="inputtext w120 date" value="<?php echo humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
			</dd>
		</dl>
	
		<dl class="small last">
			<dt>
				<label for="publish_off"><?php echo lang('ionize_label_publish_off'); ?></label>
			</dt>
			<dd>
				<input id="publish_off" name="publish_off" type="text" class="inputtext w120 date"  value="<?php echo humanize_mdate($publish_off, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
			</dd>
		</dl>
	
	
	</div>

	<!-- Comments 
	<h3 class="toggler"><?php echo lang('ionize_title_comments'); ?></h3>
	
	<div class="element">

		<dl class="small">
			<dt>
				<label for="comment_allow"><?php echo lang('ionize_label_comment_allow'); ?></label>
			</dt>
			<dd>
				<input id="comment_allow" name="comment_allow" type="checkbox" class="inputcheckbox" <?php if ($comment_allow == 1):?> checked="checked" <?php endif;?>  />
			</dd>
		</dl>

		<dl class="small">
			<dt>
				<label for="comment_autovalid"><?php echo lang('ionize_label_comment_autovalid'); ?></label>
			</dt>
			<dd>
				<input id="comment_autovalid" name="comment_autovalid" type="checkbox" class="inputcheckbox" <?php if ($comment_autovalid == 1):?> checked="checked" <?php endif;?>  />
			</dd>
		</dl>

		<dl class="small last">
			<dt>
				<label for="comment_expire"><?php echo lang('ionize_label_comment_expire'); ?></label>
			</dt>
			<dd>
				<input id="comment_expire" name="comment_expire" type="text" class="inputtext w120 date"  value="<?php echo humanize_mdate($comment_expire, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
			</dd>
		</dl>

	</div>
	
	-->


	<!-- Copy Content -->
	<?php if( ! empty($id_article)) :?>

		<h3 class="toggler"><?php echo lang('ionize_title_content'); ?></h3>
		
		<div class="element">
		
			<dl class="small">
				<dt>
					<label for="lang_copy_from" title="<?php echo lang('ionize_help_copy_content'); ?>"><?php echo lang('ionize_label_copy_content'); ?></label>
				</dt>
				<dd>
					<div class="w100 h50 left">
						<select name="lang_copy_from" id="lang_copy_from" class="w100 select">
							<?php foreach(Settings::get_languages() as $language) :?>
								<option value="<?php echo $language['lang']; ?>"><?php echo ucfirst($language['name']); ?></option>
							<?php endforeach ;?>
						</select>
						
						<br/>
					
						<select name="lang_copy_to" id="lang_copy_to" class="w100 select mt5">
							<?php foreach(Settings::get_languages() as $language) :?>
								<option value="<?php echo $language['lang']; ?>"><?php echo ucfirst($language['name']); ?></option>
							<?php endforeach ;?>
						</select>
					
					</div>
					<div class="w30 h50 left ml5" style="background:url(<?php echo theme_url(); ?>images/icon_24_from_to.png) no-repeat 50% 50%;"></div>
				</dd>
			</dl>
		
			<!-- Submit button  -->
			<dl class="small">
				<dt>&#160;</dt>
				<dd>
					<input type="submit" value="<?php echo lang('ionize_button_copy_content'); ?>" class="submit" id="copy_lang">
				</dd>
			</dl>
		
		</div>

	<?php endif ;?>

	<!-- Other info : Permanenet URL, etc. 
	<?php if (!empty($id_article) && !empty($page['name'])) :?>

	<h3 class="toggler"><?php echo lang('ionize_title_informations'); ?></h3>
	
	<div class="element">

		<dl class="small compact">
			<dt><label for="permanent_url"><?php echo lang('ionize_label_permanent_url'); ?></label></dt>
			<dd>
				
				<div id="permanentUrlTab" class="mainTabs small gray">
					<ul class="tab-menu">
						<?php foreach(Settings::get_languages() as $language) :?>
							<li><a><?php echo ucfirst($language['lang']); ?></a></li>
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
							<textarea id="permanent_url_<?php echo $language['lang']; ?>" name="permanent_url_<?php echo $language['lang']; ?>" class="h40" style="border-top:none;width:142px;" onclick="javascript:this.select();" readonly="readonly"><?php echo base_url().$lang; ?><?php echo $page['urls'][$language['lang']]; ?>/<?php echo ${$language['lang']}['url']; ?></textarea>
						</div>
					<?php endforeach ;?>
				
				</div>
				
			</dd>
		</dl>

	</div>

	<?php endif ;?>
	
-->
</div>	<!-- /options -->


<script type="text/javascript">

	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler', 'div.element', true);
		

	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
	ION.initLabelHelpLinks('#articleForm');

	/**
	 * Add links on each parent page
	 *
	 */
	$$('#parent_list li.parent_page').each(function(item, idx)
	{
		ION.addParentPageEvents(item);
	});

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
	
	
	/**
	 * Calendars init
	 *
	 */
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');


	new TabSwapper({tabsContainer: 'permanentUrlTab', sectionsContainer: 'permanentUrlTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'permanentUrlTab' });

	<?php if (!empty($id_article)) :?>
	
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
				ION.removeElementLink();
			});
	
			// External link Add
			$('link').addEvent('blur', function(e)
			{
				if (ION.checkUrl(this.value))
				{
					// type of receiver, url, ID of textarea which receive the link after save.
					ION.addExternalLink('article', this.value, 'link');
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

	<?php endif ;?>

</script>
