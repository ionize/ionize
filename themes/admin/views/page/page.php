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

$tracker_title = $languages[Settings::get_lang('default')]['title'];
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
	<input type="hidden" id="origin_id_parent" value="<?php echo $id_parent; ?>" />
	<input type="hidden" id="origin_id_subnav" value="<?php echo $id_subnav; ?>" />

	<?php if ($id_page != '') :?>
		<input type="hidden" name="online" value="<?php echo $online; ?>" class="online<?php echo $id_page; ?>" />
	<?php endif ;?>


	<div id="maincolumn" class="">
		
		<fieldset>
				
		<?php if( ! empty($id_page)) :?>
			
			<?php
				
				$title = $languages[Settings::get_lang('default')]['title'];

				if ($title == '') $title = $languages[Settings::get_lang('default')]['url'];
			
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


		</fieldset>


		<fieldset class="mt10">
	
			<!-- Tabs -->
			<div id="pageTab" class="mainTabs">
				
				<ul class="tab-menu">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						<li class="tab_page<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['name']); ?></a></li>
					<?php endforeach ;?>

					<?php if ( ! empty($id_page)) :?>

						<?php if(Authority::can('access', 'admin/page/media')) :?>
							<li id="mediaTab" class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>"><a><?php echo lang('ionize_label_medias'); ?></a></li>
						<?php endif ;?>

						<li id="articlesTab" class="right<?php if( empty($id_page)) :?> inactive<?php endif ;?>"><a><?php echo lang('ionize_label_articles'); ?></a></li>

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

						<p class="clear h25">
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
									<label for="online_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_content_online'); ?>"><?php echo lang('ionize_label_online'); ?></label>
								</dt>
								<dd>
									<input id="online_<?php echo $lang; ?>" <?php if ($languages[$lang]['online'] == 1):?> checked="checked" <?php endif;?> name="online_<?php echo $lang; ?>" class="inputcheckbox" type="checkbox" value="1"/>
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
								<textarea id="title_<?php echo $lang; ?>" name="title_<?php echo $lang; ?>" class="textarea title autogrow" type="text" title="<?php echo lang('ionize_label_title'); ?>"><?php echo $languages[$lang]['title']; ?></textarea>
							</dd>
						</dl>

						<!-- Sub title -->
						<dl>
							<dt>
								<label for="subtitle_<?php echo $lang; ?>"><?php echo lang('ionize_label_subtitle'); ?></label>
							</dt>
							<dd>
								<textarea id="subtitle_<?php echo $lang; ?>" name="subtitle_<?php echo $lang; ?>" class="textarea autogrow" type="text"><?php echo $languages[$lang]['subtitle']; ?></textarea>
							</dd>
						</dl>


						<!-- URL -->
						<dl>
							<dt>
								<label for="url_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_url'); ?>"><?php echo lang('ionize_label_url'); ?></label>
							</dt>
							<dd>
								<input id="url_<?php echo $lang; ?>" name="url_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo $languages[$lang]['url']; ?>" title="<?php echo lang('ionize_help_page_url'); ?>" />

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
								<input id="nav_title_<?php echo $lang; ?>" name="nav_title_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo $languages[$lang]['nav_title']; ?>"/>
							</dd>
						</dl>

						<!-- Meta title : used for browser window title -->
						<dl>
							<dt>
								<label for="meta_title_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_page_window_title'); ?>"><?php echo lang('ionize_label_meta_title'); ?></label>
							</dt>
							<dd>
								<input id="meta_title_<?php echo $lang; ?>" name="meta_title_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo $languages[$lang]['meta_title']; ?>"/>
							</dd>
						</dl>
					</div>

				<?php endforeach ;?>

				<?php
				/*
				 * Medias
				 */
				?>
				<?php if ( ! empty($id_page)) :?>

					<?php if(Authority::can('access', 'admin/page/media')) :?>

						<!-- Medias -->
						<div class="tabcontent">

							<p class="h30">
									<a id="addMedia" class="fmButton button light right">
										<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
									</a>
									<a id="btnAddVideoUrl" class="right light button">
										<i class="icon-video"></i><?php echo lang('ionize_label_add_video'); ?>
									</a>

								<a class="left light button" onclick="javascript:mediaManager.loadMediaList();return false;">
									<i class="icon-refresh"></i><?php echo lang('ionize_label_reload_media_list'); ?>
								</a>

									<a class="left light button" onclick="javascript:mediaManager.detachAllMedia();return false;">
										<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all_files'); ?>
									</a>
							</p>

							<div id="mediaContainer" class="sortable-container"></div>
						</div>

					<?php endif ;?>

					<?php
					/*
					 * Articles
					 */
					?>
					<div class="tabcontent">

						<div class="h50">
							<a id="btnArticleTypeHelp" class="right light button helpme type">
								<i class="icon-helpme"></i>
								<?php echo lang('ionize_label_help_articles_types'); ?>
							</a>

							<!-- Droppable to link one article to this page -->
							<div id="new_article" class="droppable w260 lite h30 left dropArticleInPage" data-id="<?php echo $id_page; ?>">
								<?php echo lang('ionize_label_drop_article_here'); ?>
							</div>
						</div>

						<div id="articleListContainer"></div>

					</div>
				<?php endif ;?>


			</div>
		</fieldset>
		

	</div>
</form>


<!-- File Manager Form : Mandatory for the filemanager -->
<form name="fileManagerForm" id="fileManagerForm">
	<input type="hidden" name="hiddenFile" />
</form>


<script type="text/javascript">

	// Makes all elements with the class '.selectable' selectable
	ION.initSelectableText();

	ION.initHelp('#btnArticleTypeHelp', 'article_type', Lang.get('ionize_title_help_articles_types'));

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
	var pageTab = new TabSwapper({
		tabsContainer: 'pageTab',
		sectionsContainer: 'pageTabContent',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent',
		cookieName: 'mainTab'
	});


	<?php if ( ! empty($id_page)) :?>

		var id_page = '<?php echo $id_page; ?>';

		// Articles List
		ION.HTML('article/get_list', {'id_page':id_page}, {'update': 'articleListContainer'});

		/**
		 * Get Content Tabs & Elements
		 * 1. ION.getContentElements calls element_definition/get_definitions_from_parent : returns the elements definitions wich have elements for the current parent.
		 * 2. ION.getContentElements calls element/get_elements_from_definition : returns the elements for each definition
		 */
		$('desktop').store('tabSwapper', pageTab);
		ION.getContentElements('page', id_page);


		// Media Manager & tabs events
		mediaManager.initParent('page', id_page);

		<?php if(Authority::can('access', 'admin/article/media')) :?>
			mediaManager.loadMediaList();
		<?php endif ;?>

		// Add Media button
		$('addMedia').addEvent('click', function(e)
		{
			e.stop();
			mediaManager.initParent('page', id_page);
			mediaManager.toggleFileManager();
		});

		// Init the staticItemManager
		staticItemManager.init({
			'parent': 'page',
			'id_parent': id_page,
			'destination': 'pageTab'
		});

		// Get Static Items
		staticItemManager.getParentItemList();

		// Extend Fields
		extendManager.init({
			parent: 'page',
			id_parent: id_page,
			destination: 'pageTab',
			destinationTitle: Lang.get('ionize_title_extend_fields')
		});
		extendManager.getParentInstances();


		// Add Video button
		// @todo: rewrite
		<?php if(Authority::can('link', 'admin/page/media')) :?>

			$('btnAddVideoUrl').addEvent('click', function()
			{
				ION.dataWindow(
					'addExternalMedia',
					'ionize_label_add_video',
					'media/add_external_media_window',
					{width:600, height:150},
					{
						'parent': 'page',
						'id_parent': id_page
					}
				)
			});

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