<div id="maincolumn">

	<div style="overflow:hidden;">

		<!-- Icon create a page -->
		<div class="desktopIcon" id="iconAddPage">
			<img src="<?= theme_url() ?>images/icon_48_page.png" />
			<p><a><?= lang('ionize_dashboard_icon_add_page') ?></a></p>
		</div>
		
		<!-- Icon Articles -->
		<div class="desktopIcon" id="iconArticles">
			<img src="<?= theme_url() ?>images/icon_48_articles.png" />
			<p><a><?= lang('ionize_dashboard_icon_articles') ?></a></p>
		</div>
		
		<!-- Icon Media Manager -->
		<div class="desktopIcon" id="iconMediaManager">
			<img src="<?= theme_url() ?>images/icon_48_media.png" />
			<p><a><?= lang('ionize_dashboard_icon_mediamanager') ?></a></p>
		</div>
		
		<!-- Icon Static translations -->
		<div class="desktopIcon" id="iconTranslation">
			<img src="<?= theme_url() ?>images/icon_48_languages.png" />
			<p><a><?= lang('ionize_dashboard_icon_translation') ?></a></p>
		</div>
		
		<div class="desktopIcon" id="iconGA">
			<img src="<?= theme_url() ?>images/icon_48_stats.png" />
			<p><a><?= lang('ionize_dashboard_icon_google_analytics') ?></a></p>
		</div>
	
	</div>

	
	<div id="infos" class="mt20">	

		<!-- Last connected users -->
		<h3 class="toggler"><?= lang('ionize_dashboard_title_last_connected_users') ?></h3>
		
		<div class="element pl15">
			<table class="list mb20" id="usersList">
			
				<thead>
					<tr>
						<th axis="string"><?= lang('ionize_label_screen_name') ?></th>
						<th axis="string"><?= lang('ionize_label_last_visit') ?></th>
						<th axis="string"><?= lang('ionize_label_email') ?></th>				
					</tr>
				</thead>
				<tbody>
			
					<?php foreach($users as $user) :?>
						
						<tr>
							<td><?= $user['screen_name'] ?></td>
							<td><?= humanize_mdate($user['last_visit'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
							<td><?= mailto($user['email']) ?></td>
						</tr>
			
					<?php endforeach ;?>
			
				</tbody>
				
			</table>
		</div>
		
		<!-- Last registered users -->
		<?php if ( ! empty($last_registered_users)) :?>
		<h3 class="toggler"><?= lang('ionize_dashboard_title_last_registered_users') ?></h3>
		
		<div class="element pl15">
			<table class="list mb20" id="lastusersList">
			
				<thead>
					<tr>
						<th axis="string"><?= lang('ionize_label_screen_name') ?></th>
						<th axis="string"><?= lang('ionize_label_join_date') ?></th>
						<th axis="string"><?= lang('ionize_label_email') ?></th>				
					</tr>
				</thead>
				<tbody>
			
					<?php foreach($last_registered_users as $user) :?>
						
						<tr>
							<td><?= $user['screen_name'] ?></td>
							<td><?= humanize_mdate($user['join_date'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
							<td><?= mailto($user['email']) ?></td>
						</tr>
			
					<?php endforeach ;?>
			
				</tbody>
				
			</table>
		</div>

		<?php endif ;?>

		
		<!-- Last updated articles -->
		<h3 class="toggler"><?= lang('ionize_dashboard_title_last_modified_articles') ?></h3>
		
		<div class="element pl15">
		
			<table class="list mb20" id="articleList">
			
				<thead>
					<tr>
						<th axis="string"><?= lang('ionize_label_article') ?></th>
						<th axis="string"><?= lang('ionize_label_pages') ?></th>
						<th axis="string"><?= lang('ionize_label_author') ?></th>
						<th axis="string"><?= lang('ionize_label_updater') ?></th>
						<th axis="string"><?= lang('ionize_label_created') ?></th>				
						<th axis="string"><?= lang('ionize_label_updated') ?></th>				
					</tr>
				</thead>
			
				<tbody>
				
				<?php foreach ($last_articles as $article) :?>
					
					<?php
						$title = ($article['title'] != '') ? $article['title'] : $article['name'];
						
						// Article parent pages links
						$pages = array();
	
						foreach($article['pages'] as $page)
						{
							if (!empty($page['page']))
							{
								$page_title = (! empty($page['page']['title'])) ? $page['page']['title'] : $page['page']['name'];
								$pages[] = '<a class="page" rel="'.$page['id_page'].'" >' . $page_title . '</a>';
							}
						}					
						$pages_link = implode(', ', $pages);

					?>

					<tr>
						<td><a class="article" title="<?= lang('ionize_label_edit') ?>" rel="0.<?= $article['id_article'] ?>"><span class="flag flag<?= $article['flag'] ?>"></span><?= $title ?></a></td>
						<td <?php if(empty($pages_link)) :?>class="alert"<?php endif; ?>><a title="<?= lang('ionize_label_edit') ?>"><?= $pages_link ?></a></td>
						<td><?= $article['author'] ?></td>
						<td><?= $article['updater'] ?></td>
						<td><?= humanize_mdate($article['created'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
						<td><?= humanize_mdate($article['updated'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
					</tr>
			
				<?php endforeach ;?>
				
				</tbody>
			
			</table>
		</div>


		<!-- Orphan pages : Page linked to menu 0 -->
		<?php if ( ! empty($orphan_pages)) :?>

		<h3 class="toggler"><?= lang('ionize_dashboard_title_orphan_pages') ?></h3>

		<div class="element pl15">
			
			<table class="list mb20" id="orphanPagesList">
			
				<thead>
					<tr>
						<th axis="string"><?= lang('ionize_label_page') ?></th>
						<th axis="string"><?= lang('ionize_label_author') ?></th>
						<th axis="string"><?= lang('ionize_label_updater') ?></th>
						<th axis="string"><?= lang('ionize_label_created') ?></th>				
						<th axis="string"><?= lang('ionize_label_page_delete_date') ?></th>				
					</tr>
				</thead>
			
				<tbody>
				
				<?php foreach ($orphan_pages as $page) :?>

					<?php
						$title = ($page['title'] != '') ? $page['title'] : $page['name'];
					?>

					<tr>
						<td><a title="<?= lang('ionize_label_edit') ?>" rel="<?= $page['id_page'] ?>" class="page"><?= $title ?></a></td>
						<td><?= $page['author'] ?></td>
						<td><?= $page['updater'] ?></td>
						<td><?= humanize_mdate($page['created'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
						<td><?= humanize_mdate($page['updated'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
					</tr>
			
				<?php endforeach ;?>
				
				</tbody>
			
			</table>

		</div>

		<?php endif ;?>


		<!-- Orphan articles : no linked to any page -->
		<?php if ( ! empty($orphan_articles)) :?>

		<h3 class="toggler"><?= lang('ionize_dashboard_title_orphan_articles') ?></h3>

		<div class="element pl15">
			
			<table class="list mb20" id="orphanArticlesList">
			
				<thead>
					<tr>
						<th axis="string"><?= lang('ionize_label_article') ?></th>
						<th axis="string"><?= lang('ionize_label_author') ?></th>
						<th axis="string"><?= lang('ionize_label_updater') ?></th>
						<th axis="string"><?= lang('ionize_label_created') ?></th>				
						<th axis="string"><?= lang('ionize_label_page_delete_date') ?></th>				
					</tr>
				</thead>
			
				<tbody>
				
				<?php foreach ($orphan_articles as $article) :?>
					
					<?php
						$title = ($article['title'] != '') ? $article['title'] : $article['name'];
					?>

					<tr class="0x<?= $article['id_article'] ?>">
						<td><a class="article" title="<?= lang('ionize_label_edit') ?>" rel="0.<?= $article['id_article'] ?>"><span class="flag flag<?= $article['flag'] ?>"></span><?= $title ?></a></td>
						<td><?= $article['author'] ?></td>
						<td><?= $article['updater'] ?></td>
						<td><?= humanize_mdate($article['created'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
						<td><?= humanize_mdate($article['updated'], Settings::get('date_format'). ' %H:%i:%s') ?></td>
					</tr>
			
				<?php endforeach ;?>
				
				</tbody>
			
			</table>

		</div>

		<?php endif ;?>
	
	</div>

</div>


<script type="text/javascript">

	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	MUI.initToolbox('dashboard_toolbox');


	/**
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler', 'div.element');


	// Articles edit
	var articles = ($$('#articleList .article')).extend($$('#orphanArticlesList .article'));
	
	
	articles.each(function(item, idx)
	{
		var rel = (item.getProperty('rel')).split(".");
		var id_page = rel[0];
		var id_article = rel[1];

		var title = item.get('text');
		
		item.addEvent('click', function(e){
			e.stop();
			MUI.updateContent({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'article/edit/'+id_page+'.'+id_article,'title': Lang.get('ionize_title_edit_article') + ' : ' + title});
		});
		
		// Make draggable to tree
//		ION.makeLinkDraggable(item, 'article');
		ION.addDragDrop(item, '.dropArticleInPage,.dropArticleAsLink,.folder', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');
	});



	// Pages edit
	var pages = ($$('#articleList a.page')).extend($$('#orphanPagesList a.page'));

	pages.each(function(item, idx)
	{
		var id = item.getProperty('rel');
		var title = item.get('text');
		
		item.addEvent('click', function(e){
			e.stop();
			MUI.updateContent({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'page/edit/'+id,'title': Lang.get('ionize_title_edit_page') + ' : ' + title});
		});
	});




	// Main Icons actions
	$('iconAddPage').addEvent('click', function(e){
		e.stop();
		MUI.updateContent({
			element: $('mainPanel'),
			title: Lang.get('ionize_title_new_page'),
			url : admin_url + 'page/create/0'
		});
	});
	
	$('iconArticles').addEvent('click', function(e){
		e.stop();
		MUI.updateContent({
			element: $('mainPanel'),
			title: Lang.get('ionize_title_articles'),
			url : admin_url + 'article/list_articles'		
		});
	});
	
	$('iconMediaManager').addEvent('click', function(e){
		e.stop();
		MUI.updateContent({
			element: $('mainPanel'),
			title: Lang.get('ionize_menu_media_manager'),
			url : admin_url + 'media/get_media_manager',
			padding: {top:0, left:0, right:0}
		});
	});

	$('iconTranslation').addEvent('click', function(e){
		e.stop();
		MUI.updateContent({
			element: $('mainPanel'),
			title: Lang.get('ionize_title_translation'),
			url : admin_url + 'translation/'
		});
	});
	
	$('iconGA').addEvent('click', function(e){
		e.stop();
		window.location.href = 'http://www.google.com/analytics/'
	});


	// Flags edit event
	if ($('edit_flags'))
	{
		$('edit_flags').addEvent('click', function(e)
		{
			e.stop();
			MUI.updateContent({'element': $('mainPanel'),'loadMethod': 'xhr','url': admin_url + 'setting','title': Lang.get('ionize_menu_site_settings_global') });
		});
	}



</script>