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
<form name="pageOptionsForm" id="pageOptionsForm" method="post" action="<?php echo admin_url() . 'page/save_options'?>">

	<input type="hidden" name="element" id="element" value="page" />
	<input type="hidden" name="id_menu" value="<?php echo $id_menu; ?>" />
	<input type="hidden" name="created" value="<?php echo $created; ?>" />
	<input type="hidden" name="id_page" id="id_page" value="<?php echo $id_page; ?>" />
	<input type="hidden" id="origin_id_parent" value="<?php echo $id_parent; ?>" />


	<!-- Informations -->
	<div class="info">

		<?php if ($id_page != '') :?>

			<dl class="compact small">
				<dt><label><?php echo lang('ionize_label_status'); ?></label></dt>
				<dd>
					<a id="iconPageStatus" class="icon page<?php echo $id_page; ?> <?php echo($online == '1') ? 'online' : 'offline' ;?>"></a>
				</dd>
			</dl>

			<?php if ($this->connect->is('super-admins')) :?>

				<dl class="compact small">
					<dt><label><?php echo lang('ionize_label_name'); ?></label></dt>
					<dd>
						<a class="edit dynamic-input left" data-id="<?php echo $id_page; ?>" data-name="name" data-url="page/update_name"><?php echo $name; ?></a>
					</dd>
				</dl>

			<?php endif ;?>


			<?php if (humanize_mdate($logical_date, Settings::get('date_format')) != '') :?>
				<dl class="small compact">
					<dt><label><?php echo lang('ionize_label_date'); ?></label></dt>
					<dd><?php echo humanize_mdate($logical_date, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($logical_date, '%H:%i:%s'); ?></span></dd>
				</dl>
			<?php endif ;?>

			<dl class="compact small">
				<dt><label><?php echo lang('ionize_label_created'); ?></label></dt>
				<dd><?php echo humanize_mdate($created, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($created, '%H:%i:%s'); ?></span></dd>
			</dl>

			<?php if (humanize_mdate($updated, Settings::get('date_format')) != '') :?>
				<dl class="compact small">
					<dt><label><?php echo lang('ionize_label_updated'); ?></label></dt>
					<dd><?php echo humanize_mdate($updated, Settings::get('date_format')); ?> <span class="lite"><?php echo humanize_mdate($updated, '%H:%i:%s'); ?></span></dd>
				</dl>
			<?php endif ;?>

			<!-- Link ? -->
			<div id="linkContainer"></div>

		<?php endif ;?>
	</div>



	<div id="options">

		<?php if ($id_page != '') :?>

			<!-- Modules PlaceHolder -->
			<?php echo get_modules_addons('page', 'options_top'); ?>

		<?php endif ;?>


		<!-- Options -->
		<h3 class="toggler"><?php echo lang('ionize_title_attributes'); ?></h3>

		<div class="element">

			<!-- Existing page -->
			<?php if ($id_page != '') :?>

				<!-- Appears as menu item in menu ? -->
				<dl class="small">
					<dt>
						<label for="appears" title="<?php echo lang('ionize_help_appears'); ?>"><?php echo lang('ionize_label_appears'); ?></label>
					</dt>
					<dd>
						<input id="appears" name="appears" type="checkbox" class="inputcheckbox" <?php if ($appears == 1):?> checked="checked" <?php endif;?> value="1" />
					</dd>
				</dl>

			<?php endif ;?>

			<!-- Has one URL ? Means is reachable through its URL -->
			<dl class="small">
				<dt>
					<label for="has_url" title="<?php echo lang('ionize_help_has_url'); ?>"><?php echo lang('ionize_label_has_url'); ?></label>
				</dt>
				<dd>
					<input id="has_url" name="has_url" type="checkbox" class="inputcheckbox" <?php if ($has_url == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>

			<!-- Type of page -->
			<?php if ( ! empty($types)) :?>
			<dl class="small">
				<dt>
					<label for="id_type" title="<?php echo lang('ionize_help_page_type'); ?>"><?php echo lang('ionize_label_type'); ?></label>
				</dt>
				<dd>
					<?php echo $types; ?>
				</dd>
			</dl>
			<?php endif ;?>

			<!-- Page view -->
			<?php if ($id_page !='' && isset($views)) :?>
			<dl class="small">
				<dt>
					<label for="view" title="<?php echo lang('ionize_help_page_view'); ?>"><?php echo lang('ionize_label_view'); ?></label>
				</dt>
				<dd>
					<?php echo $views; ?>
				</dd>
			</dl>
			<?php endif ;?>

			<!-- Single Article Page view -->
			<?php if ($id_page !='' && isset($single_views)) :?>
			<dl class="small<?php if (!isset($article_views) && ! isset($article_list_views)) :?> last<?php endif ;?>">
				<dt>
					<label for="view" title="<?php echo lang('ionize_help_page_single_view'); ?>"><?php echo lang('ionize_label_page_single_view'); ?></label>
				</dt>
				<dd>
					<?php echo $single_views; ?>
				</dd>
			</dl>
			<?php endif ;?>

			<!-- Article List Template -->
			<?php if (isset($article_list_views)) :?>
			<dl class="small<?php if (!isset($article_views)) :?> last<?php endif ;?>">
				<dt>
					<label for="article_list_view" title="<?php echo lang('ionize_help_article_list_template'); ?>"><?php echo lang('ionize_label_article_list_template'); ?></label>
				</dt>
				<dd>
					<?php echo $article_list_views; ?>
				</dd>
			</dl>
			<?php endif ;?>

			<!-- Article Template -->
			<?php if (isset($article_views)) :?>
			<dl class="small last">
				<dt>
					<label for="article_view" title="<?php echo lang('ionize_help_article_template'); ?>"><?php echo lang('ionize_label_article_template'); ?></label>
				</dt>
				<dd>
					<?php echo $article_views; ?>
				</dd>
			</dl>
			<?php endif ;?>

		</div>
		<!-- / Options -->


		<!-- Parent -->
		<?php if ($id_page != '') :?>

			<h3 class="toggler"><?php echo lang('ionize_title_page_parent'); ?></h3>

			<div class="element">

				<!-- Menu -->
				<dl class="small">
					<dt>
						<label for="id_menu"><?php echo lang('ionize_label_menu'); ?></label>
					</dt>
					<dd>
						<?php echo $menus; ?>
					</dd>
				</dl>

				<!-- Parent -->
				<dl class="small last">
					<dt>
						<label for="id_parent"><?php echo lang('ionize_label_parent'); ?></label>
					</dt>
					<dd>
						<div id="parentSelectContainer"></div>
					</dd>
				</dl>

			</div>

		<?php endif ;?>


		<!-- Dates -->
		<h3 class="toggler"><?php echo lang('ionize_title_dates'); ?></h3>

		<div class="element">
			<dl class="small">
				<dt>
					<label for="logical_date"><?php echo lang('ionize_label_date'); ?></label>
				</dt>
				<dd>
					<input id="logical_date" name="logical_date" type="text" class="inputtext date" value="<?php echo humanize_mdate($logical_date, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
					<a class="icon clearfield date" data-id="logical_date"></a>
				</dd>
			</dl>
			<dl class="small">
				<dt>
					<label for="publish_on" title="<?php echo lang('ionize_help_publish_on'); ?>"><?php echo lang('ionize_label_publish_on'); ?></label>
				</dt>
				<dd>
					<input id="publish_on" name="publish_on" type="text" class="inputtext date" value="<?php echo humanize_mdate($publish_on, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
					<a class="icon clearfield date" data-id="publish_on"></a>
				</dd>
			</dl>

			<dl class="small last">
				<dt>
					<label for="publish_off" title="<?php echo lang('ionize_help_publish_off'); ?>"><?php echo lang('ionize_label_publish_off'); ?></label>
				</dt>
				<dd>
					<input id="publish_off" name="publish_off" type="text" class="inputtext date"  value="<?php echo humanize_mdate($publish_off, Settings::get('date_format'). ' %H:%i:%s'); ?>" />
					<a class="icon clearfield date" data-id="publish_off"></a>
				</dd>
			</dl>
		</div>

		<!-- Subnavigation -->
		<?php if ($id_page != '') :?>

			<h3 class="toggler"><?php echo lang('ionize_title_sub_navigation'); ?></h3>

			<div class="element">

				<!-- Subnav Menu -->
				<dl class="small">
					<dt>
						<label for="id_subnav_menu"><?php echo lang('ionize_label_menu'); ?></label>
					</dt>
					<dd>
						<?php echo $subnav_menu; ?>
					</dd>
				</dl>

				<!-- ID sub navigation Page -->
				<dl class="small last">
					<dt>
						<label for="id_subnav"><?php echo lang('ionize_label_page'); ?></label>
					</dt>
					<dd>
						<div id="subnavSelectContainer"></div>
						<!--
						<select name="id_subnav" id="id_subnav" class="select"></select>
						-->
					</dd>
				</dl>

				<!-- Title -->
				<div class="small optionInputTab">
					<!-- Tabs -->
					<div id="subnavTitleTab" class="mainTabs small gray">
						<ul class="tab-menu">
							<?php foreach(Settings::get_languages() as $language) :?>
							<li><a><?php echo lang('ionize_label_title'); ?> <?php echo ucfirst($language['lang']); ?></a></li>
							<?php endforeach ;?>
						</ul>
						<div class="clear"></div>
					</div>
					<div id="subnavTitleTabContent" >

						<?php foreach(Settings::get_languages() as $language) :?>
						<div class="tabcontent">
							<textarea id="subnav_title_<?php echo $language['lang']; ?>" name="subnav_title_<?php echo $language['lang']; ?>" class="autogrow"><?php echo ${$language['lang']}['subnav_title']; ?></textarea>
						</div>
						<?php endforeach ;?>

					</div>
				</div>
			</div>

		<?php endif ;?>


		<!-- Advanced Options -->
		<h3 class="toggler"><?php echo lang('ionize_title_advanced'); ?></h3>

		<div class="element">

			<!-- Home page -->
			<dl class="small">
				<dt>
					<label for="home" title="<?php echo lang('ionize_help_home_page'); ?>"><?php echo lang('ionize_label_home_page'); ?></label>
				</dt>
				<dd>
					<input id="home" name="home" type="checkbox" class="inputcheckbox" <?php if ($home == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>

			<!-- Used by module -->
			<dl class="small">
				<dt>
					<label for="used_by_module" title="<?php echo lang('ionize_help_page_used_by_module'); ?>"><?php echo lang('ionize_label_page_used_by_module'); ?></label>
				</dt>
				<dd>
					<input id="used_by_module" name="used_by_module" type="checkbox" class="inputcheckbox" <?php if ($used_by_module == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>

			<!-- Pagination -->
			<dl class="small last">
				<dt>
					<label for="pagination" title="<?php echo lang('ionize_help_pagination'); ?>"><?php echo lang('ionize_label_pagination_nb'); ?></label>
				</dt>
				<dd>
					<input id="pagination" name="pagination" type="text" class="inputtext w40" value="<?php echo $pagination; ?>" />
				</dd>
			</dl>
		</div>


		<!-- SEO -->
		<h3 class="toggler"><?php echo lang('ionize_title_seo'); ?></h3>

		<div class="element">

			<!-- Priority -->
			<dl class="small">
				<dt>
					<label for="priority" title="<?php echo lang('ionize_help_sitemap_priority'); ?>"><?php echo lang('ionize_label_sitemap_priority'); ?></label>
				</dt>
				<dd>
					<select name="priority" id="priority" class="inputtext w40">
						<?php for($i=0; $i<=10; $i++) :?>

						<option value="<?php echo $i; ?>"<?php if($priority == $i) :?> selected="selected"<?php endif ;?>><?php echo $i; ?></option>

						<?php endfor; ?>
					</select>
				</dd>
			</dl>

			<!-- Meta_Description -->
			<h4 class="help" title="<?php echo lang('ionize_help_page_meta'); ?>"><?php echo lang('ionize_label_meta_description'); ?></h4>

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
			<h4 class="help" title="<?php echo lang('ionize_help_page_meta'); ?>"><?php echo lang('ionize_label_meta_keywords'); ?></h4>
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



		<!-- Access authorization -->
		<h3 class="toggler"><?php echo lang('ionize_title_authorization'); ?></h3>

		<div class="element">
			<dl class="small last">
				<dt>
					<label for="template"><?php echo lang('ionize_label_groups'); ?></label>
				</dt>
				<dd>
					<div id="groups">
						<?php echo $groups; ?>
					</div>
				</dd>
			</dl>
		</div>


		<!-- Operations on Page -->
		<h3 class="toggler"><?php echo lang('ionize_title_operation'); ?></h3>

		<div class="element">

			<!-- Copy Content -->
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
					<div class="w30 h50 left ml5" style="background:url(<?php echo theme_url(); ?>images/icon_24_from_to.png) no-repeat 50% 50%;"></div>
				</dd>
			</dl>

			<!-- Inlude article content  -->
			<dl class="small">
				<dt>
					<label for="copy_article" title="<?php echo lang('ionize_help_copy_article_content'); ?>"><?php echo lang('ionize_label_copy_article_content'); ?></label></dt>
				<dd>
					<input type="checkbox" name="copy_article" id="copy_article" value="1" />
				</dd>
			</dl>

			<!-- Submit button  -->
			<dl class="small last">
				<dt>&#160;</dt>
				<dd>
					<input type="submit" value="<?php echo lang('ionize_button_copy_content'); ?>" class="submit" id="copy_lang">
				</dd>
			</dl>

			<?php if ($id_page != '') :?>

				<hr class="ml10" />

				<dl class="small compact mt10">
					<dt><label for="reorder_direction" title="<?php echo lang('ionize_label_help_articles_reorder'); ?>"><?php echo lang('ionize_label_article_reorder'); ?></label></dt>
					<dd>
						<select name="reorder_direction" id="reorder_direction" class="select">

							<option value="DESC"><?php echo lang('ionize_label_date_desc'); ?></option>
							<option value="ASC"><?php echo lang('ionize_label_date_asc'); ?></option>

						</select>
					</dd>
				</dl>

				<!-- Submit button  -->
				<dl class="small last">
					<dt>&#160;</dt>
					<dd>
						<input type="submit" value="<?php echo lang('ionize_button_reorder'); ?>" class="submit mt10" id="button_reorder_articles">
					</dd>
				</dl>
			<?php endif ;?>

		</div>


		<?php if ($id_page != '') :?>

			<!-- Modules PlaceHolder -->
			<?php echo get_modules_addons('page', 'options_bottom'); ?>

		<?php endif ;?>

	</div>

</form>

<script type="text/javascript">

	ION.initFormAutoGrow();

	<?php if (!empty($id_page)) :?>

		// Link to page or article or what else...
		if ($('linkContainer'))
		{
			ION.HTML(admin_url + 'page/get_link', {'id_page': '<?php echo $id_page; ?>'}, {'update': 'linkContainer'});
		}

		/**
		 * XHR updates
		 *
		 */
		// Dates
		/*
		   ION.datePicker.options['onClose'] = function()
		   {
			   ION.JSON('page/update_field', {'field': ION.datePicker.input.id, 'value': ION.datePicker.input.value, 'type':'date', 'id_page': $('id_page').value});
		   }
		   */

		// Page status
		ION.initRequestEvent($('iconPageStatus'), admin_url + 'page/switch_online/<?php echo $id_page; ?>');

//		var id_current = ($('id_page').value) ? $('id_page').value : '0';
//		var id_parent = ($('origin_id_parent').value) ? $('origin_id_parent').value : '0';

		$('id_subnav_menu').addEvent('change', function()
		{
			ION.HTML(
				admin_url + 'page/get_parents_select',
				{
					'id_menu' : $('id_subnav_menu').value,
					'id_current': 0,
					'id_parent': '<?php echo $id_subnav; ?>',
					'element_id' : 'id_subnav'
				},
				{
					'update': 'subnavSelectContainer'
				}
			);
		});
		$('id_subnav_menu').fireEvent('change');


		// Reorder articles
		$('button_reorder_articles').addEvent('click', function(e)
		{
			e.stop();

			var url = admin_url + 'page/reorder_articles';

			var data = {
				'id_page': $('id_page').value,
				'direction': $('reorder_direction').value
			};

			ION.sendData(url, data);
		});

	<?php endif; ?>


	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler', 'div.element', true, 'pageAccordion');

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#pageOptionsForm');

	/**
	 * Droppables init
	 *
	 */
	ION.initDroppable();

	/**
	 * Calendars init
	 *
	 */
	ION.initDatepicker('<?php echo Settings::get('date_format'); ?>');

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


	// Tabs
	new TabSwapper({tabsContainer: 'subnavTitleTab', sectionsContainer: 'subnavTitleTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'subnavTitleTab'	});
	new TabSwapper({tabsContainer: 'metaDescriptionTab', sectionsContainer: 'metaDescriptionTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'metaDescriptionTab'	});
	new TabSwapper({tabsContainer: 'metaKeywordsTab', sectionsContainer: 'metaKeywordsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'metaKeywordsTab' });

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


	/**
	 * Name Edit
	 *
	 */
	ION.initInputChange('#pageOptionsForm .dynamic-input');


</script>
