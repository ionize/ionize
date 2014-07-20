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

			<?php if (User()->is('super-admin') == TRUE) :?>

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

	<!-- Options -->
	<div id="options">

		<!-- Module Placeholder -->
		<?php if ( ! empty($id_article)) :?>
			<?php echo get_modules_addons('article', 'options_top'); ?>
		<?php endif ;?>

		<!-- Attributes -->
		<h3 class="toggler toggler-options"><?php echo lang('ionize_title_attributes'); ?></h3>
		<div class="element element-options">

			<!-- Indexed content -->
			<dl class="small">
				<dt>
					<label for="indexed" title="<?php echo lang('ionize_help_indexed'); ?>">
						<?php echo lang('ionize_label_indexed'); ?>
					</label>
				</dt>
				<dd>
					<input id="indexed" name="indexed" type="checkbox" class="inputcheckbox" <?php if ($indexed == 1):?> checked="checked" <?php endif;?> value="1" />
				</dd>
			</dl>

			<!-- Parent -->
			<?php if( ! empty($id_article)) :?>

				<!-- Parent pages list -->
				<dl class="small dropPageInArticle" data-id="<?php echo $id_article ?>">
					<dt>
						<label for="parents" title="<?php echo lang('ionize_help_article_context'); ?>"><?php echo lang('ionize_label_parents'); ?></label>
					</dt>
					<dd>

						<div id="parents">
							<ul class="parent_list" id="parent_list">

								<?php foreach ($pages_list as $page) :?>

									<?php
										$title = ($page['title'] != '') ? $page['title'] : $page['name'];
									?>
									<li data-id="<?php echo $page['id_page']; ?>.<?php echo $id_article; ?>" class="parent_page"><a class="icon right unlink"></a><a class="page"><span class="link-img page left mr5<?php if($page['main_parent'] == '1') :?> main-parent<?php endif; ?>"></span><?php echo $title; ?></a></li>

								<?php endforeach ;?>

							</ul>
						</div>
					</dd>
				</dl>

			<?php endif ;?>


			<!-- Categories & Tags -->
			<div class="element-options-content">

				<!-- Tags -->
				<h4><?php echo lang('ionize_label_tags'); ?></h4>
				<dfn><?php echo lang('ionize_help_tag_new') ?></dfn>
				<input type="text" name="tags" value="" id="tags" />

				<!-- Categories -->
				<h4><?php echo lang('ionize_label_categories'); ?></h4>
				<div id="categories">
					<?php echo $categories; ?>
				</div>

				<!-- Category create button -->
				<a class="button light mb10" onclick="javascript:ION.formWindow('category', 'categoryForm', '<?php echo lang('ionize_title_category_new'); ?>', 'category/get_form/article/<?php echo $id_article; ?>', {width:360, height:230})">
					<i class="icon-plus"></i>
					<?php echo lang('ionize_label_new_category'); ?>
				</a>

			</div>
		</div>

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

		<!-- Permissions -->
		<?php if(Authority::can('access', 'admin/article/permissions')) :?>
			<h3 class="toggler toggler-options"><?php echo lang('ionize_title_permissions'); ?>
				<?php if ( ! empty($frontend_role_ids) OR ! empty($backend_role_ids)) : ?>
					<a class="icon protected right mr5" ></a>
				<?php endif ;?>
			</h3>
			<div class="element element-options">

				<?php if(Authority::can('access', 'admin/article/permissions/frontend')) :?>
					<?php if ( ! empty($frontend_roles_resources)): ?>
						<dl class="x-small">
							<dt><label><?php echo lang('ionize_label_frontend'); ?></label></dt>
							<dd id="frontRoles">
								<?php foreach($frontend_roles_resources as $id_role => $role_resources): ?>
									<div id="roleRulesContainer<?php echo $id_role ?>"></div>
								<?php endforeach;?>
							</dd>
						</dl>
					<?php endif ;?>

					<!--

					Behavior options
					 Here :
					 - Do not display if choosen
					 - Remove 400 codes, has no 400 codes on articles : Hum.... to see...


					-->
					<div id="denyFrontAction">
						<dl class="x-small">
							<dt><label><?php echo lang('ionize_label_behavior'); ?></label></dt>
							<dd>
								<label><input type="radio" name="deny_code" class="mr5 ml5" value="401" <?php if ( $deny_code == '401'): ?>checked="checked"<?php endif;?>/><a title="<?php echo lang('ionize_help_denied_action_401') ;?>"><?php echo lang('ionize_label_denied_action_401') ;?></a></label><br/>
								<label><input type="radio" name="deny_code" class="mr5 ml5" value="403" <?php if ( $deny_code == '403'): ?>checked="checked"<?php endif;?> /><a title="<?php echo lang('ionize_help_denied_action_403') ;?>"><?php echo lang('ionize_label_denied_action_403') ;?></a></label><br/>
								<label><input type="radio" name="deny_code" class="mr5 ml5" value="404" <?php if ( $deny_code == '404'): ?>checked="checked"<?php endif;?> /><a title="<?php echo lang('ionize_help_denied_action_404') ;?>"><?php echo lang('ionize_label_denied_action_404') ;?></a></label>
							</dd>
						</dl>
					</div>

				<?php endif ;?>

				<?php if(Authority::can('access', 'admin/article/permissions/backend')) :?>
					<?php if ( ! empty($backend_roles_resources)): ?>

						<dl class="x-small">
							<dt><label><?php echo lang('ionize_label_backend'); ?></label></dt>
							<dd>
								<?php foreach($backend_roles_resources as $id_role => $role_resources): ?>
									<div id="roleRulesContainer<?php echo $id_role ?>"></div>
								<?php endforeach;?>
							</dd>
						</dl>

					<?php endif;?>
				<?php endif ;?>

			</div>
		<?php endif ;?>


		<!-- SEO -->
		<h3 class="toggler toggler-options"><?php echo lang('ionize_title_seo'); ?></h3>
		<div class="element element-options">


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
			<div class="element-options-content">

				<h4 title="<?php echo lang('ionize_help_article_meta_description'); ?>"><?php echo lang('ionize_label_meta_description'); ?></h4>

				<div id="metaDescriptionTab" class="mainTabs small">
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
						<textarea id="meta_description_<?php echo $language['lang']; ?>" name="meta_description_<?php echo $language['lang']; ?>" class="autogrow w95p"><?php echo $languages[$language['lang']]['meta_description']; ?></textarea>
					</div>
					<?php endforeach ;?>

				</div>
			</div>


			<!-- Meta_Keywords -->
			<div class="element-options-content">
				<h4 title="<?php echo lang('ionize_help_article_meta_keywords'); ?>"><?php echo lang('ionize_label_meta_keywords'); ?></h4>

				<div id="metaKeywordsTab" class="mainTabs small">
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
						<textarea id="meta_keywords_<?php echo $language['lang']; ?>" name="meta_keywords_<?php echo $language['lang']; ?>" class="autogrow w95p"><?php echo $languages[$language['lang']]['meta_keywords']; ?></textarea>
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
						<div class="w30 h50 left ml5" style="background:url('<?php echo admin_style_url(); ?>images/icon_24_from_to.png') no-repeat 50% 50%;"></div>
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
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>', {timePicker:true});
	ION.initClearField('#articleOptionsForm');

	/**
	 * Add links on each parent page
	 *
	 */
	$$('#parent_list li.parent_page').each(function(item)
	{
		ION.addParentPageEvents(item);
	});

	new TabSwapper({tabsContainer: 'metaDescriptionTab', sectionsContainer: 'metaDescriptionTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'articleMetaDescriptionTab'	});
	new TabSwapper({tabsContainer: 'metaKeywordsTab', sectionsContainer: 'metaKeywordsTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'articleMetaKeywordsTab' });

	// Tags
	var tags = new TextboxList(
		'tags',
		{
			unique: true,
			plugins: {autocomplete: {placeholder:null}}
		}
	);

	tags.container.addClass('textboxlist-loading');

	ION.JSON(
		ION.adminUrl + 'tag/get_json_list',{},
		{
			onSuccess: function(r)
			{
				tags.plugins['autocomplete'].setValues(r);

				ION.JSON(
					ION.adminUrl + 'tag/get_json_list',
					{
						'parent': 'article',
						'id_parent':'<?php echo $id_article; ?>'
					},
					{
						onSuccess: function(r)
						{
							tags.container.removeClass('textboxlist-loading');
							tags.plugins['autocomplete'].setSelected(r);
						}
					}
				);
			}
		}
	);

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

	<?php if ( ! empty($id_article)) :?>

		// Indexed XHR & Categories update
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

		<?php foreach($backend_roles_resources as $id_role => $role_resources): ?>

			var modRules<?php echo $id_role ?> = new ION.PermissionTree(
				'roleRulesContainer<?php echo $id_role ?>',
				<?php echo json_encode($role_resources['resources'], true) ?>,
				{
					'cb_name':'backend_rule[<?php echo $id_role ?>][]',
					'key': 'id_resource',
					'data': [
						{'key':'resource', 'as':'resource'},
						{'key':'title', 'as':'title'},
						{'key':'description', 'as':'description'},
						{'key':'actions', 'as':'actions'}
					],
					'rules' : <?php echo json_encode($role_resources['rules'], true) ?>
				}
			);

		<?php endforeach;?>

		<?php foreach($frontend_roles_resources as $id_role => $role_resources): ?>

			var modRules<?php echo $id_role ?> = new ION.PermissionTree(
				'roleRulesContainer<?php echo $id_role ?>',
				<?php echo json_encode($role_resources['resources'], true) ?>,
				{
					'cb_name':'frontend_rule[<?php echo $id_role ?>][]',
					'key': 'id_resource',
					'data': [
						{'key':'resource', 'as':'resource'},
						{'key':'title', 'as':'title'},
						{'key':'description', 'as':'description'},
						{'key':'actions', 'as':'actions'}
					],
					'rules' : <?php echo json_encode($role_resources['rules'], true) ?>,
					'onCheck': function()
					{
						frontRoleCheck();
					}
				}
			);

		<?php endforeach;?>

		var frontRoleCheck = function()
		{
			var checked = false;
			var cbs = $('frontRoles').getElements('input[type=checkbox]');
			cbs.each(function(cb) {
				if (cb.getProperty('checked') == true)
					checked = true;
			});
			if (checked)
				$('denyFrontAction').show();
			else
				$('denyFrontAction').hide();
		};
		frontRoleCheck();


		// Name Edit
		ION.initInputChange('#articleOptionsForm .dynamic-input');

	<?php endif; ?>

</script>