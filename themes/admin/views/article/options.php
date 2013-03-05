<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @subpackage	Views
 * @category	Article
 * @author		Ionize Dev Team
 *
 */

?>

<form name="articleOptionsForm" id="articleOptionsForm" method="post" action="<?php echo admin_url() . 'article/save_options'?>">

	<input type="hidden" name="element" id="element" value="article" />
	<input type="hidden" name="id_article" id="id_article" value="<?php echo $id_article; ?>" />
	<input type="hidden" name="rel" id="rel" value="<?php echo $id_page; ?>.<?php echo $id_article; ?>" />
	<input type="hidden" name="created" value="<?php echo $created; ?>" />
	<input type="hidden" name="author" value="<?php echo $author; ?>" />
	<input type="hidden" name="main_parent" id="main_parent" value="<?php echo $main_parent; ?>" />
	<input type="hidden" name="has_url" id="has_url" value="<?php echo $has_url; ?>" />


	<!-- Informations -->
	<div class="info">

		<?php if ($id_article != '') :?>

			<?php if ($this->connect->is('super-admins')) :?>

				<dl class="compact small">
					<dt><label><?php echo lang('ionize_label_name'); ?></label></dt>
					<dd>
						<a class="edit dynamic-input" data-id="<?php echo $id_article; ?>" data-name="name" data-id_page="<?php echo $id_page; ?>" data-url="article/update_name"><?php echo $name; ?></a>
					</dd>
				</dl>

			<?php endif ;?>



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


		<!-- Link ? -->
		<?php if ($id_article != '') :?>

		<div id="linkContainer"></div>

		<?php endif ;?>
		
	</div>



	<div id="options">


		<?php if ( ! empty($id_article)) :?>

			<!-- Modules PlaceHolder -->
			<?php echo get_modules_addons('article', 'options_top'); ?>

		<?php endif ;?>

		<!-- Options -->
		<h3 class="toggler toggler-options"><?php echo lang('ionize_title_attributes'); ?></h3>

		<div class="element element-options">

			<!-- Indexed content -->
			<dl class="small">
				<dt>
					<label for="indexed" title="<?php echo lang('ionize_help_indexed'); ?>"><?php echo lang('ionize_label_indexed'); ?></label>
				</dt>
				<dd>
					<input id="indexed" name="indexed" type="checkbox" class="inputcheckbox" <?php if ($indexed == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>

			<!-- Categories -->
			<dl class="small">
				<dt>
					<label for="categories"><?php echo lang('ionize_label_categories'); ?></label>
				</dt>
				<dd>
					<div id="categories">
						<?php echo $categories; ?>
					</div>

					<!-- Category create button -->
					<a class="button light" onclick="javascript:ION.formWindow('category', 'categoryForm', '<?php echo lang('ionize_title_category_new'); ?>', 'category/get_form/article/<?php echo $id_article; ?>', {width:360, height:230})">
						<i class="icon-plus"></i>
						<?php echo lang('ionize_label_new_category'); ?>
					</a>

				</dd>
			</dl>

			<!-- Existing Article -->
			<?php if( ! empty($id_article)) :?>

			<!-- Parent pages list -->
			<dl class="small dropPageInArticle">
				<dt>
					<label for="parents" title="<?php echo lang('ionize_help_article_context'); ?>"><?php echo lang('ionize_label_parents'); ?></label>
				</dt>
				<dd>

					<div id="parents">
						<ul class="parent_list" id="parent_list">

							<?php foreach ($pages_list as $page) :?>

							<?php

							$title = ($page['title'] != '') ? $page['title'] : $page['name'];

							// All REL or ID which permit the DOM identification of one article MUST be written like this.
							// $rel = $page['id_page']. '.' .$id_article;
							?>

							<li rel="<?php echo $page['id_page']; ?>.<?php echo $id_article; ?>" class="parent_page"><a class="icon right unlink"></a><a class="page"><span class="link-img page left mr5<?php if($page['main_parent'] == '1') :?> main-parent<?php endif; ?>"></span><?php echo $title; ?></a></li>

							<?php endforeach ;?>

						</ul>
						<!--
										<input type="text" id="new_parent" class="inputtext w140 italic empty nofocus droppable" alt="<?php echo lang('ionize_label_drop_page_here'); ?>"></input>
										-->
					</div>
				</dd>
			</dl>

			<?php endif ;?>

		</div>



		<!-- Advanced options
					<h3 class="toggler"><?php echo lang('ionize_title_advanced'); ?></h3>

					<div class="element">


						<!-- Tags
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


					</div>

					-->


		<!-- Dates -->
		<h3 class="toggler toggler-options"><?php echo lang('ionize_title_dates'); ?></h3>

		<div class="element element-options">
			<dl class="small">
				<dt>
					<label for="logical_date"><?php echo lang('ionize_label_date'); ?></label>
				</dt>
				<dd>
					<input id="logical_date" name="logical_date" type="text" class="inputtext w120 date" value="<?php echo humanize_mdate($logical_date, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
					<a class="icon clearfield date" data-id="logical_date"></a>
				</dd>
			</dl>
			<dl class="small">
				<dt>
					<label for="publish_on"><?php echo lang('ionize_label_publish_on'); ?></label>
				</dt>
				<dd>
					<input id="publish_on" name="publish_on" type="text" class="inputtext w120 date" value="<?php echo humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
					<a class="icon clearfield date" data-id="publish_on"></a>
				</dd>
			</dl>

			<dl class="small last">
				<dt>
					<label for="publish_off"><?php echo lang('ionize_label_publish_off'); ?></label>
				</dt>
				<dd>
					<input id="publish_off" name="publish_off" type="text" class="inputtext w120 date"  value="<?php echo humanize_mdate($publish_off, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
					<a class="icon clearfield date" data-id="publish_off"></a>
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


		<!-- SEO -->
		<h3 class="toggler toggler-options"><?php echo lang('ionize_title_seo'); ?></h3>

		<div class="element element-options">
			<!-- Meta_Description -->
			<h4 class="help" title="<?php echo lang('ionize_help_article_meta_description'); ?>"><?php echo lang('ionize_label_meta_description'); ?></h4>

			<div class="small optionInputTab">
				<div id="metaDescriptionTab" class="mainTabs small gray">
					<ul class="tab-menu">
						<?php foreach(Settings::get_languages() as $language) :?>
						<li><a><?php echo ucfirst($language['lang']); ?></a></li>
						<?php endforeach ;?>
					</ul>
					<div class="clear"></div>
				</div>
				<div id="metaDescriptionTabContent" >

					<?php foreach(Settings::get_languages() as $language) :?>
					<div class="tabcontent">
						<textarea id="meta_description_<?php echo $language['lang']; ?>" name="meta_description_<?php echo $language['lang']; ?>" class="autogrow"><?php echo ${$language['lang']}['meta_description']; ?></textarea>
					</div>
					<?php endforeach ;?>

				</div>
			</div>


			<!-- Meta_Keywords -->
			<h4 class="help" title="<?php echo lang('ionize_help_article_meta_keywords'); ?>"><?php echo lang('ionize_label_meta_keywords'); ?></h4>
			<div class="small optionInputTab">
				<div id="metaKeywordsTab" class="mainTabs small gray">
					<ul class="tab-menu">
						<?php foreach(Settings::get_languages() as $language) :?>
						<li><a><?php echo ucfirst($language['lang']); ?></a></li>
						<?php endforeach ;?>
					</ul>
					<div class="clear"></div>
				</div>
				<div id="metaKeywordsTabContent" >

					<?php foreach(Settings::get_languages() as $language) :?>
					<div class="tabcontent">
						<textarea id="meta_keywords_<?php echo $language['lang']; ?>" name="meta_keywords_<?php echo $language['lang']; ?>" class="autogrow"><?php echo ${$language['lang']}['meta_keywords']; ?></textarea>
					</div>
					<?php endforeach ;?>

				</div>
			</div>

		</div>


		<!-- Copy Content -->
		<?php if( ! empty($id_article)) :?>

		<h3 class="toggler toggler-options"><?php echo lang('ionize_title_operation'); ?></h3>

		<div class="element element-options">

			<dl class="small">
				<dt>
					<label for="lang_copy_from" title="<?php echo lang('ionize_help_copy_content'); ?>"><?php echo lang('ionize_label_copy_content'); ?></label>
				</dt>
				<dd>
					<div class="w100 left">
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
					<div class="w30 h50 left ml5" style="background:url('<?php echo theme_url(); ?>images/icon_24_from_to.png') no-repeat 50% 50%;"></div>
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


		<?php if ( ! empty($id_article)) :?>

			<!-- Modules PlaceHolder -->
			<?php echo get_modules_addons('article', 'options_bottom'); ?>

		<?php endif ;?>


	</div>	<!-- /options -->
</form>

<script type="text/javascript">

	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler-options', 'div.element-options', true, 'articleAccordion');

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
	ION.initClearField('#articleOptionsForm');

	/**
	 * Add links on each parent page
	 *
	 */
	$$('#parent_list li.parent_page').each(function(item, idx)
	{
		ION.addParentPageEvents(item);
	});

	new TabSwapper({tabsContainer: 'metaDescriptionTab', sectionsContainer: 'metaDescriptionTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'articleMetaDescriptionTab'	});
	new TabSwapper({tabsContainer: 'metaKeywordsTab', sectionsContainer: 'metaKeywordsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'articleMetaKeywordsTab' });

	// Copy content from one lang to another
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
		var nbCategories = ($('categories').getElements('option')).length;
		if (nbCategories > 5)
		{
			$$('#categories select').setStyles({
				'height': (nbCategories * 15) + 'px'
			});
		}

		// Link to page or article or what else...
		if ($('linkContainer'))
		{
			ION.HTML(admin_url + 'article/get_link', {'id_page': '<?php echo $id_page; ?>', 'id_article': '<?php echo $id_article; ?>'}, {'update': 'linkContainer'});
		}

		/**
		 * Name Edit
		 *
		 */
		ION.initInputChange('#articleOptionsForm .dynamic-input');

	<?php endif; ?>

</script>