<?php

$tracker_title = $languages[Settings::get_lang('default')]['title'];
if ($tracker_title == '')
	$tracker_title = $name;

?>

<form name="articleForm" id="articleForm" method="post" action="<?php echo site_url(config_item('admin_url').'/article/save/'.$id_article); ?>">

	<input type="hidden" name="element" id="element" value="article" />
	<input type="hidden" class="data-tracker" name="data_tracker" data-element="article" data-id="<?php echo $id_article; ?>" data-title="<?php echo $tracker_title; ?>" data-url="article/edit/<?php echo $id_page; ?>.<?php echo $id_article; ?>" />
	<input type="hidden" name="id_article" id="id_article" value="<?php echo $id_article; ?>" />
	<input type="hidden" name="rel" id="rel" value="<?php echo $id_page; ?>.<?php echo $id_article; ?>" />
	<input type="hidden" name="created" value="<?php echo $created; ?>" />
	<input type="hidden" name="author" value="<?php echo $author; ?>" />
	<input type="hidden" name="name" id="name" value="<?php echo $name; ?>" />
	<input type="hidden" name="main_parent" id="main_parent" value="<?php echo $main_parent; ?>" />
	<input type="hidden" name="has_url" id="has_url" value="<?php echo $has_url; ?>" />
	
	<!-- JS storing element -->
	<input type="hidden" id="memory" />

	<div id="maincolumn">

		<fieldset class="article-header">

			<!-- Existing article -->
			<?php if( ! empty($id_article)) :?>

				<?php

					$title = $languages[Settings::get_lang('default')]['title'];
					if ($title == '') $title = $name;

				?>

	            <div id="article-tracker-<?php echo $id_article; ?>"></div>

    	        <h2 class="main article" id="main-title"><?php echo $title; ?></h2>

				<div class="main subtitle">
					<p>
						<span class="lite">ID : </span>
						<?php echo $id_article; ?>

						<?php if( ! empty($breadcrump)) :?>
							| <span class="lite"><?php echo lang('ionize_label_article_context_edition'); ?> : </span><?php echo$breadcrump?>
						<?php endif ;?>
					</p>
				</div>

			<!-- New article -->
			<?php else :?>

				<h2 class="main article" id="main-title"><?php echo lang('ionize_title_new_article'); ?></h2>

				<input type="hidden" name="id_page" id="id_page" value="<?php echo $id_page; ?>" />

				<!-- Where is the article ? -->
				<dl>
					<dt><label><?php echo lang('ionize_label_article_in'); ?></label></dt>
					<dd class="lite"><?php echo $menu; ?>
						<?php foreach ($breadcrumbs as $breadcrumb) :?>
							> <?php echo $breadcrumb['title']; ?>
						<?php endforeach ;?>
					</dd>
				</dl>

				<!-- Ordering -->
				<dl>
					<dt >
						<label for="ordering_select"><?php echo lang('ionize_label_ordering'); ?></label>
					</dt>
					<dd>
						<select name="ordering_select" id="ordering_select" class="select">
							<?php if($id_article) :?>
								<option value="<?php echo $ordering; ?>"><?php echo $ordering; ?></option>
							<?php endif ;?>
							<option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
							<option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
							<option id="ordering_select_after" value="after" <?php if( empty($articles)) :?>style="display:none"<?php endif ;?>><?php echo lang('ionize_label_ordering_after'); ?></option>
						</select>
					</dd>
					<dd>
						<select name="ordering_after" id="ordering_after" style="display:none;" class="select w140 mt5">
							<?php foreach($articles as $article) :?>
								<?php
									$title = ($article['title'] != '') ? $article['title'] : $article['name'];
								?>
								<option value="<?php echo $article['id_article']; ?>"><?php echo $title; ?></option>
							<?php endforeach ;?>
						</select>
					</dd>
				</dl>

				<!-- Online / Offline -->
				<dl class="mb20">
					<dt>
						<label for="online" title="<?php echo lang('ionize_help_article_online'); ?>"><?php echo lang('ionize_label_article_online'); ?></label>
					</dt>
					<dd>
						<div>
							<input id="online" <?php if ($online == 1):?> checked="checked" <?php endif;?> name="online" class="inputcheckbox" type="checkbox" value="1"/>
						</div>
					</dd>
				</dl>

			<?php endif ;?>

            <?php if ( ! empty($id_article)) :?>

                <!-- Modules PlaceHolder -->
                <?php echo get_modules_addons('article', 'main_top'); ?>

            <?php endif ;?>

		</fieldset>

		<fieldset id="blocks" class="mt10">
	
			<!-- Tabs -->
			<div id="articleTab" class="mainTabs">
				
				<ul class="tab-menu">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						<li class="tab_article<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['name']); ?></a></li>
					<?php endforeach ;?>

					<!-- Media Tab : id is important for media items number on tab -->
					<?php if(Authority::can('access', 'admin/article/media')) :?>
    					<li id="mediaTab" class="right<?php if( empty($id_article)) :?> inactive<?php endif ;?>"><a><?php echo lang('ionize_label_medias'); ?></a></li>
					<?php endif;?>

				</ul>
				<div class="clear"></div>
			
			</div>

			<div id="articleTabContent">

				<!-- Text block -->
				<?php foreach(Settings::get_languages() as $language) :?>
					
				<?php $lang = $language['lang']; ?>

				<div class="tabcontent <?php echo $lang; ?>">

					<!-- Copy data -->
					<p class="clear h25">
						<a class="button light right copyLang"rel="<?php echo $lang; ?>" title="<?php echo lang('ionize_label_copy_to_other_languages'); ?>">
							<i class="icon copy"></i>
							<?php echo lang('ionize_label_copy_to_other_languages') ?>
						</a>
					</p>

					<div class="article-header">

						<!-- Online -->
						<?php if(count(Settings::get_languages()) > 1) :?>

							<dl>
								<dt>
									<label for="online_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_article_content_online'); ?>"><?php echo lang('ionize_label_online'); ?></label>
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
								<textarea id="title_<?php echo $lang; ?>" name="title_<?php echo $lang; ?>" class="textarea title autogrow" type="text"><?php echo $languages[$lang]['title']; ?></textarea>
							</dd>
						</dl>

						<!-- sub title -->
						<dl>
							<dt>
								<label for="subtitle_<?php echo $lang; ?>"><?php echo lang('ionize_label_subtitle'); ?></label>
							</dt>
							<dd>
								<textarea id="subtitle_<?php echo $lang; ?>" name="subtitle_<?php echo $lang; ?>" class="textarea text autogrow" type="text"><?php echo $languages[$lang]['subtitle']; ?></textarea>
								<!-- <a class="icon edit subtitle"></a> -->
							</dd>
						</dl>

						<!-- URL -->
						<dl>
							<dt>
								<label for="url_<?php echo $lang; ?>"><?php echo lang('ionize_label_url'); ?></label>
							</dt>
							<dd>
								<input id="url_<?php echo $lang; ?>" name="url_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo $languages[$lang]['url']; ?>"/>
							</dd>
						</dl>

						<!-- Meta Title : Browser window title -->
						<dl>
							<dt>
								<label for="meta_title_<?php echo $lang; ?>" title="<?php echo lang('ionize_help_article_window_title'); ?>"><?php echo lang('ionize_label_meta_title'); ?></label>
							</dt>
							<dd>
								<input id="meta_title_<?php echo $lang; ?>" name="meta_title_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo $languages[$lang]['meta_title']; ?>"/>
							</dd>
						</dl>

					</div>

					<!-- Text -->
					<h3 class="toggler-<?php echo $lang; ?> article-header"><?php echo lang('ionize_label_text'); ?></h3>
		
					<div class="element-<?php echo $lang; ?> mb40">
						<div>
							<textarea id="content_<?php echo $lang; ?>" name="content_<?php echo $lang; ?>" class="tinyTextarea h260 w100p" rel="<?php echo $lang; ?>"><?php echo htmlentities($languages[$lang]['content'], ENT_QUOTES, 'utf-8'); ?></textarea>
							<p class="mt5"><a class="btnToggleEditor block" data-editor="content_<?php echo $lang; ?>">Toggle Editor</a></p>
						</div>
					</div>

				</div>
				<?php endforeach ;?>

				<?php if(Authority::can('access', 'admin/article/media')) :?>

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
							<a class="left light button unlink" onclick="javascript:mediaManager.detachAllMedia();return false;">
								<i class="icon-unlink"></i><?php echo lang('ionize_label_detach_all'); ?>
							</a>
                        </p>

						<div id="mediaContainer" class="sortable-container"></div>
					</div>

				<?php endif;?>
			</div>
		</fieldset>
	</div>
</form>


<!-- File Manager Form : Mandatory for the filemanager -->
<form name="fileManagerForm" id="fileManagerForm" action="">
	<input type="hidden" name="hiddenFile" />
</form>


<script type="text/javascript">

	ION.initFormAutoGrow();

	// Toolbox
	ION.initToolbox('article_toolbox');

	// Init the Edit Mode
    ION.initEditMode('editionModeSwitcher', 'article', '.article-header');

	// Article element in each of its parent context
	ION.initDroppable();
	 
	// Calendars init
	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');
    ION.initClearField('#articleForm');


	// Auto-generate Main title
	$$('.tabcontent .title').each(function(input)
	{
		input.addEvent('keyup', function()
		{
			$('main-title').set('text', this.value);
		});
	});

	$$('.btnToggleEditor').each(function(btn){
		btn.addEvent('click', function()
		{
			if ($(this.getProperty('data-editor') + '_ifr'))
				tinyMCE.execCommand("mceRemoveControl",false, this.getProperty('data-editor'));
			else
				tinyMCE.execCommand("mceAddControl",false, this.getProperty('data-editor'));
		});
	});

	// Auto-generates URL
	<?php if ($id_article == '') :?>
		<?php foreach (Settings::get_languages() as $lang) :?>
			ION.initCorrectUrl('title_<?php echo $lang['lang']; ?>', 'url_<?php echo $lang['lang']; ?>');
		<?php endforeach ;?>
	<?php endif; ?>

	// Article ordering :
	// - Show / hide article list depending on Ordering select
	// - Update the article select list after parent change
	if ($('id_page'))
	{
		$('ordering_select').addEvent('change', function()
		{
			if (this.value == 'after'){ $('ordering_after').setStyle('display', 'block');}
			else { $('ordering_after').setStyle('display', 'none');	}
		});

	}

	if ($('ordering_select'))
	{
		var cookieName = 'new-article-order';
		var order_options = 'first';

		$('ordering_select').addEvent('change', function()
		{
			Cookie.write(cookieName, this.value);
		});

		if (Cookie.read(cookieName))
		{
			order_options = Cookie.read(cookieName);
			$('ordering_select').getElement('[value="'+order_options+'"]').setProperty('selected', 'selected');
			if (order_options == 'after')
				$('ordering_select').fireEvent('change');
		}
	}

	// Copy Lang data to other languages dynamically
	ION.initCopyLang('.copyLang', Array('title', 'subtitle', 'url', 'content', 'meta_title'));
	
	// Tabs
	var articleTab = new TabSwapper({
		tabsContainer: 'articleTab',
		sectionsContainer: 'articleTabContent',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent',
		cookieName: 'articleTab'
	});

	// TinyEditors. Must be called after tabs init.
	ION.initTinyEditors('.tab_article', '#articleTabContent .tinyTextarea');

	<?php if ( ! empty($id_article)) :?>

		var id_article = '<?php echo $id_article; ?>';

		// Get Content Elements Tabs & Elements
		$('desktop').store('tabSwapper', articleTab);
		ION.getContentElements('article', id_article);

    	// Media Manager & tabs events
		mediaManager.initParent('article', id_article);

		<?php if(Authority::can('access', 'admin/article/media')) :?>
        	mediaManager.loadMediaList();
		<?php endif ;?>

		// Add Media button
		$('addMedia').addEvent('click', function(e)
		{
			e.stop();
			mediaManager.initParent('article', id_article);
			mediaManager.toggleFileManager();
		});

		// Init the staticItemManager
		staticItemManager.init({
			'parent': 'article',
			'id_parent': id_article,
			'destination': 'articleTab'
		});

		// Get Static Items
		staticItemManager.getParentItemList();

		// Add video button
		<?php if(Authority::can('link', 'admin/page/media')) :?>

			$('btnAddVideoUrl').addEvent('click', function()
			{
				ION.dataWindow(
					'addExternalMedia',
					'ionize_label_add_video',
					'media/add_external_media_window',
					{width:600, height:150},
					{
						'parent': 'article',
						'id_parent': id_article
					}
				)
			});

		<?php endif ;?>

		// Extend Fields
		extendManager.init({
			parent: 'article',
			id_parent: id_article,
			destination: 'articleTab',
			destinationTitle: Lang.get('ionize_title_extend_fields')
		});
		extendManager.getParentInstances();

	<?php endif ;?>

</script>