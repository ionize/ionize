<?php
/**
 * Dashboard
 *
 * Receives vars :
 * $modules :				Installed modules to which the user has access
 * $last_articles :			Last 10 edited / created articles
 * $orphan_pages : 			Orphan pages
 * $orphan_articles : 		Orphan articles
 * $users : 				Users list
 * $last_registered_users : Last 10 registered	Users list
 *
 */
?>

<div id="maincolumn">

	<!-- Row 1 -->
	<div class="row">

		<div class="col col2">

			<!-- Shortcuts -->
			<div id="shortcutBloc" class="desktopBloc" data-title="Shortcuts">

				<?php if(Authority::can('create', 'admin/page')) :?>
					<div class="desktopIcon" id="iconAddPage" data-url="page/create/0" data-title="ionize_title_new_page">
						<i class="page-new"></i>
						<img src="<?php echo theme_url(); ?>images/icon_48_page_new.png" />
						<p><a><?php echo lang('ionize_dashboard_icon_add_page'); ?></a></p>
					</div>
				<?php endif ;?>

				<?php if(Authority::can('access', 'admin/article')) :?>
					<div class="desktopIcon" data-url="article/list_articles" data-title="ionize_title_articles">
                        <i class="articles"></i>
						<img src="<?php echo theme_url(); ?>images/icon_48_articles.png"/>
						<p><a><?php echo lang('ionize_dashboard_icon_articles'); ?></a></p>
					</div>
				<?php endif ;?>

				<?php if(Authority::can('access', 'admin/filemanager')) :?>
					<div class="desktopIcon" data-url="media/get_media_manager" data-title="ionize_menu_media_manager">
                        <i class="media"></i>
						<img src="<?php echo theme_url(); ?>images/icon_48_media.png" />
						<p><a><?php echo lang('ionize_dashboard_icon_mediamanager'); ?></a></p>
					</div>
				<?php endif ;?>

				<?php if(Authority::can('access', 'admin/users_roles')) :?>
					<div class="desktopIcon" data-url="user" data-title="ionize_title_users">
                        <i class="users"></i>
						<img src="<?php echo theme_url(); ?>images/icon_48_groups.png" />
						<p><a><?php echo lang('ionize_dashboard_icon_users'); ?></a></p>
					</div>
				<?php endif ;?>

				<?php if(Authority::can('access', 'admin/translations')) :?>
					<div class="desktopIcon" id="iconTranslation" data-url="translation" data-title="ionize_title_translation">
                        <i class="translation"></i>
						<img src="<?php echo theme_url(); ?>images/icon_48_languages.png" />
						<p><a><?php echo lang('ionize_dashboard_icon_translation'); ?></a></p>
					</div>
				<?php endif ;?>

				<?php if(Authority::can('access', 'admin/tools/google_analytics')) :?>
					<div class="desktopIcon" id="iconGA" data-url="http://www.google.com/analytics/" data-external="true">
                        <i class="stats"></i>
						<img src="<?php echo theme_url(); ?>images/icon_48_stats.png" />
						<p><a><?php echo lang('ionize_dashboard_icon_google_analytics'); ?></a></p>
					</div>
				<?php endif ;?>
			</div>

			<!-- Tracker -->
			<?php if (Settings::get('enable_backend_tracker') == '1') :?>
				<div  id="trackerBloc" class="desktopBloc">
					<h2><?php echo lang('ionize_dashboard_title_current_connected_users'); ?></h2>
					<div class="pb15"  id="trackerCurrentConnectedUsers"></div>
				</div>
			<?php endif; ?>


			<!-- Users -->
			<div  id="usersBloc"class="desktopBloc" data-title="<?php echo lang('ionize_dashboard_title_users'); ?>">

				<!-- Tabs -->
				<div id="dashBoardUsersTab" class="mainTabs mt5 mb0">
					<ul class="tab-menu ">
						<li><a><?php echo lang('ionize_dashboard_title_last_connected_users') ?></a></li>
						<?php if ( ! empty($last_registered_users)) :?>
							<li><a><?php echo lang('ionize_dashboard_title_last_registered_users') ?></a></li>
						<?php endif ;?>
					</ul>
					<div class="clear"></div>
				</div>

				<div id="dashBoardUsersTabContent">

					<!-- Last logged in -->
					<div class="tabcontent">

						<table class="list mb20 mt10" id="usersList">

							<thead>
							<tr>
								<th axis="string"><?php echo lang('ionize_label_name'); ?></th>
								<th axis="string"><?php echo lang('ionize_label_email'); ?></th>
								<th axis="string"><?php echo lang('ionize_label_last_visit'); ?></th>
							</tr>
							</thead>
							<tbody>

							<?php foreach($users as $user) :?>

								<tr>
									<td><?php echo $user['screen_name']; ?></td>
									<td><?php echo mailto($user['email']); ?></td>
									<td><?php echo humanize_mdate($user['last_visit'], Settings::get('date_format'). ' %H:%i'); ?></td>
								</tr>

							<?php endforeach ;?>

							</tbody>

						</table>

					</div>

					<!-- Last Registered -->
					<?php if ( ! empty($last_registered_users)) :?>

						<div class="tabcontent">
							<table class="list mb20 mt10" id="lastusersList">

								<thead>
								<tr>
									<th axis="string"><?php echo lang('ionize_label_name'); ?></th>
									<th axis="string"><?php echo lang('ionize_label_email'); ?></th>
									<th axis="string"><?php echo lang('ionize_label_join_date'); ?></th>
								</tr>
								</thead>
								<tbody>

								<?php foreach($last_registered_users as $user) :?>

									<tr>
										<td><?php echo $user['screen_name']; ?></td>
										<td><?php echo mailto($user['email']); ?></td>
										<td><?php echo humanize_mdate($user['join_date'], Settings::get('date_format'). ' %H:%i'); ?></td>
									</tr>

								<?php endforeach ;?>

								</tbody>

							</table>

						</div>

					<?php endif ;?>
				</div>

			</div>



		</div>

		<div class="col col2">

			<!-- Modules -->
			<?php if ( ! empty($modules)) :?>

				<div id="modulesBloc" class="desktopBloc" data-title="<?php echo lang('ionize_menu_modules'); ?>">

					<?php foreach($modules as $module) :?>

						<div class="desktopIcon desktopModuleIcon" data-url="module/<?php echo $module['uri']; ?>/<?php echo $module['uri']; ?>/index" data-title="<?php echo $module['name']; ?>">
							<img src="<?php echo base_url(); ?>modules/<?php echo $module['folder']; ?>/assets/images/icon_48_module.png" alt="<?php echo $module['description']; ?>" />
							<p><a><?php echo $module['name']; ?></a></p>
						</div>

					<?php endforeach ;?>
				</div>

			<?php endif ;?>


			<!-- Content : Pages, Articles -->
			<div  id="contentBloc" class="desktopBloc" data-title="<?php echo lang('ionize_dashboard_title_content'); ?>">

				<!-- Tabs -->
				<div id="dashBoardContentTab" class="mainTabs mt5 mb0">
					<ul class="tab-menu ">
						<li><a><?php echo lang('ionize_dashboard_title_last_modified_articles') ?></a></li>
						<?php if ( ! empty($orphan_articles)) :?>
							<li><a><?php echo lang('ionize_dashboard_title_orphan_articles') ?></a></li>
						<?php endif ;?>
						<?php if ( ! empty($orphan_pages)) :?>
							<li><a><?php echo lang('ionize_dashboard_title_orphan_pages') ?></a></li>
						<?php endif ;?>
					</ul>
					<div class="clear"></div>
				</div>

				<div id="dashBoardContentTabContent">

					<!-- Last edited articles-->
					<div class="tabcontent">

						<table class="list mb20" id="articleList">

							<thead>
							<tr>
								<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
								<th axis="string"><?php echo lang('ionize_label_pages'); ?></th>
								<th axis="string"><?php echo lang('ionize_label_updater'); ?></th>
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
										<a class="article" title="<?php echo lang('ionize_label_edit'); ?>" rel="<?php echo $article['id_page']; ?>.<?php echo $article['id_article']; ?>">
											<span class="icon edit mr5 left"></span>
											<?php echo $title; ?><br/>
										</a>
									</td>
									<td>
										<span class="lite"><?php echo $article['breadcrumb']; ?></span>
									</td>
									<td><?php echo $article['updater']; ?></td>
									<td><?php echo humanize_mdate($article['updated'], Settings::get('date_format')); ?></td>
								</tr>

							<?php endforeach ;?>

							</tbody>

						</table>

					</div>

					<!-- Orphan article -->
					<?php if ( ! empty($orphan_articles)) :?>

						<div class="tabcontent">

							<table class="list mb20" id="orphanArticlesList">

								<thead>
								<tr>
									<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
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
										<td>
											<a class="article" title="<?php echo lang('ionize_label_edit'); ?>" rel="0.<?php echo $article['id_article']; ?>">
												<span class="icon edit mr5 left"></span>
												<?php echo $title; ?>
											</a>
										</td>
										<td><?php echo $article['updater']; ?></td>
										<td><?php echo humanize_mdate($article['created'], Settings::get('date_format')); ?></td>
										<td><?php echo humanize_mdate($article['updated'], Settings::get('date_format')); ?></td>
									</tr>

								<?php endforeach ;?>

								</tbody>

							</table>

						</div>

					<?php endif ;?>

					<!-- Orphan pages : Page linked to menu 0 -->
					<?php if ( ! empty($orphan_pages)) :?>

						<div class="tabcontent">

							<table class="list mb20" id="orphanPagesList">

								<thead>
								<tr>
									<th axis="string"><?php echo lang('ionize_label_page'); ?></th>
									<th axis="string"><?php echo lang('ionize_label_author'); ?></th>
									<th axis="string"><?php echo lang('ionize_label_updater'); ?></th>
									<th axis="string"><?php echo lang('ionize_label_page_delete_date'); ?></th>
								</tr>
								</thead>

								<tbody>

								<?php foreach ($orphan_pages as $page) :?>

									<?php
									$title = ($page['title'] != '') ? $page['title'] : $page['name'];
									?>

									<tr>
										<td>
											<a title="<?php echo lang('ionize_label_edit'); ?>" rel="<?php echo $page['id_page']; ?>" class="page">
												<span class="icon edit mr5"></span>
												<?php echo $title; ?>
											</a>
										</td>
										<td><?php echo $page['author']; ?></td>
										<td><?php echo $page['updater']; ?></td>
										<td><?php echo humanize_mdate($page['updated'], Settings::get('date_format'). ' %H:%i'); ?></td>
									</tr>

								<?php endforeach ;?>

								</tbody>

							</table>


						</div>

					<?php endif ;?>
				</div>
			</div>


		</div>


	</div>


</div>



<script type="text/javascript">

	// Panel toolbox
	ION.initToolbox('empty_toolbox');

	// Togglers
	ION.initAccordion('.toggler', 'div.element', false, 'dashboardAccordion');

	$$('.desktopBloc').each(function(bloc){
		new ION.ContentPanel({
			'id': bloc.id,
			'title': bloc.getAttribute('data-title'),
			'container':bloc
		});
	});

	// Articles edit
	var articles = ($$('#articleList .article')).append($$('#orphanArticlesList .article'));

	// Tabs
	var dashBoardUsersTabSwapper = new TabSwapper(
	{
		tabsContainer: 'dashBoardUsersTab',
		sectionsContainer: 'dashBoardUsersTabContent',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li', clickers: 'li a', sections: 'div.tabcontent',
		cookieName: 'dashBoardUsersTab'
	});
	var dashBoardContentTabSwapper = new TabSwapper(
	{
		tabsContainer: 'dashBoardContentTab',
		sectionsContainer: 'dashBoardContentTabContent',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li', clickers: 'li a', sections: 'div.tabcontent',
		cookieName: 'dashBoardContentTab'
	});


	articles.each(function(item, idx)
	{
		item.addEvent('click', function(e){
			e.stop();
            ION.splitPanel({
                'urlMain': admin_url + 'article/edit/' + item.getProperty('rel'),
                'urlOptions': admin_url + 'article/get_options/' + item.getProperty('rel'),
                'title': Lang.get('ionize_title_edit_article') + ' : ' + item.get('text')
            });
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
            ION.contentUpdate({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'page/edit/'+id,'title': Lang.get('ionize_title_edit_page') + ' : ' + title});
		});
	});


	var desktopIcons = $('shortcutBloc').getElements('.desktopIcon');

    desktopIcons.each(function(icon)
	{
        icon.addEvent('click', function(e)
		{
			if (icon.getProperty('data-external'))
			{
                window.location.href = icon.getProperty('data-url');
            }
			else
			{
				var options = {
                    element: $('mainPanel'),
                    title: Lang.get(icon.getProperty('data-title')),
                    url : icon.getProperty('data-url')
				};
				if (icon.getProperty('data-url') == 'media/get_media_manager')
                    options.padding = {top: 0, right: 0, bottom: 0, left: 0};

                ION.contentUpdate(options);
            }
        });
	});

	// Modules Icons actions
	$$('.desktopModuleIcon').each(function(item)
	{
		item.addEvent('click', function(e){
            ION.contentUpdate({
				element: $('mainPanel'),
				title: item.getProperty('data-title'),
				url : ION.cleanUrl(item.getProperty('data-url'))
			});
		});
	});



	
</script>