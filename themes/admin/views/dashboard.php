
<div id="maincolumn">

	<div style="overflow:hidden;">

		<!-- Icon create a page -->
		<div class="desktopIcon" id="iconAddPage">
			<img src="<?php echo theme_url(); ?>images/icon_48_page.png" />
			<p><a><?php echo lang('ionize_dashboard_icon_add_page'); ?></a></p>
		</div>
		
		<!-- Icon Articles -->
		<div class="desktopIcon" id="iconArticles">
			<img src="<?php echo theme_url(); ?>images/icon_48_articles.png" />
			<p><a><?php echo lang('ionize_dashboard_icon_articles'); ?></a></p>
		</div>
		
		<!-- Icon Media Manager -->
		<div class="desktopIcon" id="iconMediaManager">
			<img src="<?php echo theme_url(); ?>images/icon_48_media.png" />
			<p><a><?php echo lang('ionize_dashboard_icon_mediamanager'); ?></a></p>
		</div>
		
		<!-- Icon Static translations -->
		<div class="desktopIcon" id="iconTranslation">
			<img src="<?php echo theme_url(); ?>images/icon_48_languages.png" />
			<p><a><?php echo lang('ionize_dashboard_icon_translation'); ?></a></p>
		</div>
		
		<div class="desktopIcon" id="iconGA">
			<img src="<?php echo theme_url(); ?>images/icon_48_stats.png" />
			<p><a><?php echo lang('ionize_dashboard_icon_google_analytics'); ?></a></p>
		</div>
	
	</div>

	<?php if ( ! empty($modules)) :?>

		<h3 class="mt20"><?php echo lang('ionize_menu_modules'); ?></h3>

		<div style="overflow:hidden;">

			<?php foreach($modules as $uri => $module) :?>
	
				<?php if(Connect()->is($module['access_group'])) :?>
				
					<div class="desktopIcon desktopModuleIcon">
						<img src="<?php echo base_url(); ?>modules/<?php echo $module['folder']; ?>/assets/images/icon_48_module.png" />
						<p><a title="<?php echo $module['name']; ?>" href="module/<?php echo $uri; ?>/<?php echo $uri; ?>/index"><?php echo $module['name']; ?></a></p>
					</div>
				
				<?php endif ;?>								
	
			<?php endforeach ;?>

		</div>

	<?php endif ;?>


	<div id="infos" class="mt20">

    	<!-- Current connected users -->
		<?php if (Settings::get('enable_backend_tracker') == '1') :?>

			<h3 class="toggler"><?php echo lang('ionize_dashboard_title_current_connected_users'); ?></h3>
			<div class="element pl15">
				<div class="pb15"  id="trackerCurrentConnectedUsers"></div>
			</div>

		<?php endif; ?>

		<!-- Last connected users -->
    	<h3 class="toggler"><?php echo lang('ionize_dashboard_title_last_connected_users'); ?></h3>


    	<div class="element pl15">
			<table class="list mb20" id="usersList">
			
				<thead>
					<tr>
						<th axis="string"><?php echo lang('ionize_label_screen_name'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_last_visit'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_email'); ?></th>				
					</tr>
				</thead>
				<tbody>
			
					<?php foreach($users as $user) :?>
						
						<tr>
							<td><?php echo $user['screen_name']; ?></td>
							<td><?php echo humanize_mdate($user['last_visit'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
							<td><?php echo mailto($user['email']); ?></td>
						</tr>
			
					<?php endforeach ;?>
			
				</tbody>
				
			</table>
		</div>
		
		<!-- Last registered users -->
		<?php if ( ! empty($last_registered_users)) :?>
		<h3 class="toggler"><?php echo lang('ionize_dashboard_title_last_registered_users'); ?></h3>
		
		<div class="element pl15">
			<table class="list mb20" id="lastusersList">
			
				<thead>
					<tr>
						<th axis="string"><?php echo lang('ionize_label_screen_name'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_join_date'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_email'); ?></th>				
					</tr>
				</thead>
				<tbody>
			
					<?php foreach($last_registered_users as $user) :?>
						
						<tr>
							<td><?php echo $user['screen_name']; ?></td>
							<td><?php echo humanize_mdate($user['join_date'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
							<td><?php echo mailto($user['email']); ?></td>
						</tr>
			
					<?php endforeach ;?>
			
				</tbody>
				
			</table>
		</div>

		<?php endif ;?>

		
		<!-- Last updated articles -->
		<h3 class="toggler"><?php echo lang('ionize_dashboard_title_last_modified_articles'); ?></h3>
		
		<div class="element pl15">
		
			<table class="list mb20" id="articleList">
			
				<thead>
					<tr>
						<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
						<!--<th axis="string"><?php echo lang('ionize_label_pages'); ?></th>-->
						<th axis="string"><?php echo lang('ionize_label_author'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_updater'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_created'); ?></th>				
						<th axis="string"><?php echo lang('ionize_label_updated'); ?></th>				
					</tr>
				</thead>
			
				<tbody>
				
				<?php foreach ($last_articles as $article) :?>
					
					<?php
						$title = ($article['title'] != '') ? $article['title'] : $article['name'];
					?>

					<tr>
						<td>
							<a class="article" title="<?php echo lang('ionize_label_edit'); ?>" rel="<?php echo $article['id_page']; ?>.<?php echo $article['id_article']; ?>"><span class="flag flag<?php echo $article['flag']; ?>"></span><?php echo $title; ?></a><br/>
							<span class="lite pl5"> > <?php echo $article['breadcrumb']; ?></span>
						</td>
						<td><?php echo $article['author']; ?></td>
						<td><?php echo $article['updater']; ?></td>
						<td><?php echo humanize_mdate($article['created'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
						<td><?php echo humanize_mdate($article['updated'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
					</tr>
			
				<?php endforeach ;?>
				
				</tbody>
			
			</table>
		</div>


		<!-- Orphan pages : Page linked to menu 0 -->
		<?php if ( ! empty($orphan_pages)) :?>

		<h3 class="toggler"><?php echo lang('ionize_dashboard_title_orphan_pages'); ?></h3>

		<div class="element pl15">
			
			<table class="list mb20" id="orphanPagesList">
			
				<thead>
					<tr>
						<th axis="string"><?php echo lang('ionize_label_page'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_author'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_updater'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_created'); ?></th>				
						<th axis="string"><?php echo lang('ionize_label_page_delete_date'); ?></th>				
					</tr>
				</thead>
			
				<tbody>
				
				<?php foreach ($orphan_pages as $page) :?>

					<?php
						$title = ($page['title'] != '') ? $page['title'] : $page['name'];
					?>

					<tr>
						<td><a title="<?php echo lang('ionize_label_edit'); ?>" rel="<?php echo $page['id_page']; ?>" class="page"><?php echo $title; ?></a></td>
						<td><?php echo $page['author']; ?></td>
						<td><?php echo $page['updater']; ?></td>
						<td><?php echo humanize_mdate($page['created'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
						<td><?php echo humanize_mdate($page['updated'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
					</tr>
			
				<?php endforeach ;?>
				
				</tbody>
			
			</table>

		</div>

		<?php endif ;?>


		<!-- Orphan articles : no linked to any page -->
		<?php if ( ! empty($orphan_articles)) :?>

		<h3 class="toggler"><?php echo lang('ionize_dashboard_title_orphan_articles'); ?></h3>

		<div class="element pl15">
			
			<table class="list mb20" id="orphanArticlesList">
			
				<thead>
					<tr>
						<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_author'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_updater'); ?></th>
						<th axis="string"><?php echo lang('ionize_label_created'); ?></th>				
						<th axis="string"><?php echo lang('ionize_label_page_delete_date'); ?></th>				
					</tr>
				</thead>
			
				<tbody>
				
				<?php foreach ($orphan_articles as $article) :?>
					
					<?php
						$title = ($article['title'] != '') ? $article['title'] : $article['name'];
					?>

					<tr class="0x<?php echo $article['id_article']; ?>">
						<td><a class="article" title="<?php echo lang('ionize_label_edit'); ?>" rel="0.<?php echo $article['id_article']; ?>"><span class="flag flag<?php echo $article['flag']; ?>"></span><?php echo $title; ?></a></td>
						<td><?php echo $article['author']; ?></td>
						<td><?php echo $article['updater']; ?></td>
						<td><?php echo humanize_mdate($article['created'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
						<td><?php echo humanize_mdate($article['updated'], Settings::get('date_format'). ' %H:%i:%s'); ?></td>
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
	ION.initToolbox('dashboard_toolbox');


	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler', 'div.element', false, 'dashboardAccordion');


	// Articles edit
	var articles = ($$('#articleList .article')).append($$('#orphanArticlesList .article'));
	
	
	articles.each(function(item, idx)
	{
		var rel = (item.getProperty('rel')).split(".");
		var id_page = rel[0];
		var id_article = rel[1];

		var title = item.get('text');
		
		item.addEvent('click', function(e){
			e.stop();
			MUI.Content.update({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'article/edit/'+id_page+'.'+id_article,'title': Lang.get('ionize_title_edit_article') + ' : ' + title});
		});
		
		// Make draggable to tree
		ION.addDragDrop(item, '.dropArticleInPage,.dropArticleAsLink,.folder', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');
	});



	// Pages edit
	var pages = ($$('#articleList a.page')).append($$('#orphanPagesList a.page'));

	pages.each(function(item, idx)
	{
		var id = item.getProperty('rel');
		var title = item.get('text');
		
		item.addEvent('click', function(e){
			e.stop();
			MUI.Content.update({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'page/edit/'+id,'title': Lang.get('ionize_title_edit_page') + ' : ' + title});
		});
	});


	// Main Icons actions
	$('iconAddPage').addEvent('click', function(e){
		e.stop();
		MUI.Content.update({
			element: $('mainPanel'),
			title: Lang.get('ionize_title_new_page'),
			url : admin_url + 'page/create/0'
		});
	});
	
	$('iconArticles').addEvent('click', function(e){
		e.stop();
		MUI.Content.update({
			element: $('mainPanel'),
			title: Lang.get('ionize_title_articles'),
			url : admin_url + 'article/list_articles'		
		});
	});
	
	$('iconMediaManager').addEvent('click', function(e){
		e.stop();
		MUI.Content.update({
			element: $('mainPanel'),
			title: Lang.get('ionize_menu_media_manager'),
			url : admin_url + 'media/get_media_manager',
			padding: {top:0, left:0, right:0}
		});
	});

	$('iconTranslation').addEvent('click', function(e){
		e.stop();
		MUI.Content.update({
			element: $('mainPanel'),
			title: Lang.get('ionize_title_translation'),
			url : admin_url + 'translation/'
		});
	});
	
	$('iconGA').addEvent('click', function(e){
		e.stop();
		window.location.href = 'http://www.google.com/analytics/'
	});
	
	
	// Modules Icons actions
	$$('.desktopModuleIcon').each(function(item)
	{
		var a = item.getElement('a');
		var href = a.getProperty('href');
		var title = a.getProperty('title');
		
		item.addEvent('click', function(e){
			MUI.Content.update({
				element: $('mainPanel'),
				title: title,
				url : admin_url + ION.cleanUrl(href)
			});
		});
	});
	

</script>