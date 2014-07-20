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
$user_role = User()->get_role();
?>
<div class="p10" >

<!-- Row 1 -->
<div class="row" id="dashboardContainer">

	<?php if (
		Settings::get('dashboard_google') == '1'
		&& Settings::get('google_analytics_profile_id') !=''
		&& Settings::get('google_analytics_email') !=''
		&& Settings::get('google_analytics_password') !=''
	) :?>

		<!-- Google Analytics -->
		<div id="gaBloc" class="desktopBloc fullwidth" data-title="<?php echo lang('ionize_dashboard_title_visits') ?>">

			<div id="gaReport" style="min-height: 200px;" class="loading"></div>

		</div>
	<?php endif ;?>


	<!-- Shortcuts -->
	<?php if (Settings::get('display_dashboard_shortcuts') == '1'): ?>
		<div id="shortcutBloc" class="desktopBloc" data-title="<?php echo lang('ionize_label_display_dashboard_shortcuts') ?>">

			<?php if(Authority::can('create', 'admin/page')) :?>
				<div class="desktopIcon" id="iconAddPage" data-url="page/create/0" data-title="ionize_title_new_page">
					<i class="page-new"></i>
					<p><a><?php echo lang('ionize_dashboard_icon_add_page'); ?></a></p>
				</div>
			<?php endif ;?>

			<?php if(Authority::can('access', 'admin/article')) :?>
				<div class="desktopIcon" data-url="article/articles" data-title="ionize_title_articles">
					<i class="articles"></i>
					<p><a><?php echo lang('ionize_dashboard_icon_articles') ?></a></p>
				</div>
			<?php endif ;?>

			<?php if(Authority::can('access', 'admin/filemanager')) :?>
				<div class="desktopIcon" data-url="media/get_media_manager" data-title="ionize_menu_media_manager">
					<i class="media"></i>
					<p><a><?php echo lang('ionize_dashboard_icon_mediamanager'); ?></a></p>
				</div>
			<?php endif ;?>

			<?php if(Authority::can('access', 'admin/translations')) :?>
				<div class="desktopIcon" id="iconTranslation" data-url="translation" data-title="ionize_title_translation">
					<i class="translation"></i>
					<p><a><?php echo lang('ionize_dashboard_icon_translation'); ?></a></p>
				</div>
			<?php endif ;?>

			<?php if(Authority::can('access', 'admin/users_roles')) :?>
				<div class="desktopIcon" data-url="user" data-title="ionize_title_users">
					<i class="users"></i>
					<p><a><?php echo lang('ionize_dashboard_icon_users'); ?></a></p>
				</div>
			<?php endif ;?>

			<?php if(Authority::can('access', 'admin/tools/google_analytics')) :?>
				<div class="desktopIcon" id="iconGA" data-url="http://www.google.com/analytics/" data-external="true">
					<i class="stats"></i>
					<p><a><?php echo lang('ionize_dashboard_icon_google_analytics'); ?></a></p>
				</div>
			<?php endif ;?>
		</div>
	<?php endif ;?>


	<!-- Tracker -->
	<?php if (Settings::get('enable_backend_tracker') == '1') :?>
		<div id="trackerBloc" class="desktopBloc" data-title="<?php echo lang('ionize_dashboard_title_current_connected_users'); ?>">
			<div class="pb15" id="trackerCurrentConnectedUsers"></div>
		</div>
	<?php endif; ?>


	<!-- Modules -->
	<?php if ( ! empty($modules) && Settings::get('display_dashboard_modules') == '1') :?>
		<div id="modulesBloc" class="desktopBloc" data-title="<?php echo lang('ionize_menu_modules'); ?>">
			<?php foreach($modules as $module) :?>
				<div class="desktopIcon desktopModuleIcon" data-url="module/<?php echo $module['uri']; ?>/<?php echo $module['uri']; ?>/index" data-title="<?php echo $module['name']; ?>">
					<?php
						$src = NULL;
						if (is_file(MODPATH.$module['folder'].'/assets/images/icon_40_module.png'))
							$src = base_url().'modules/'.$module['folder'].'/assets/images/icon_40_module.png';
						if (is_file(MODPATH.$module['folder'].'/assets/images/icon_48_module.png'))
							$src = base_url().'modules/'.$module['folder'].'/assets/images/icon_48_module.png';
					?>
					<?php if ( ! is_null($src)) :?>
						<img src="<?php echo $src ?>" alt="<?php echo $module['description']; ?>" />
					<?php else: ?>
						<i></i>
					<?php endif; ?>
					<p><a><?php echo $module['name']; ?></a></p>
				</div>
			<?php endforeach ;?>
		</div>
	<?php endif ;?>


	<!-- Quick Settings -->
	<?php if (Settings::get('display_dashboard_quick_settings') == '1'): ?>
		<div  id="quickSettingsBloc" class="desktopBloc" data-title="<?php echo lang('ionize_dashboard_title_quick_settings'); ?>">
			<div class="p5">

				<form name="quickSettingsForm" id="quickSettingsForm">

					<!-- Form keys : Needed to be able to process checkboxes -->
					<input type="hidden" name="keys" value="display_front_offline_content">

					<dl class="card" data-id="hide_front_offline_content">
						<dt>
							<input class="inputcheckbox" type="checkbox" name="display_front_offline_content" id="display_front_offline_content" <?php if (Settings::get('display_front_offline_content') == '1'):?> checked="checked" <?php endif;?> value="1" />
						</dt>
						<dd>
							<label for="display_front_offline_content" ><?php echo lang('ionize_label_display_front_offline_content'); ?>
								<span class="help"><?php echo lang('ionize_help_display_front_offline_content'); ?></span>
							</label>
						</dd>
					</dl>
				</form>
			</div>
		</div>
	<?php endif ;?>



	<!-- Users -->
	<?php if (Settings::get('display_dashboard_users') == '1'): ?>
		<div  id="usersBloc" class="desktopBloc" data-title="<?php echo lang('ionize_dashboard_title_users'); ?>">

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
							<th axis="date" class="w110"><?php echo lang('ionize_label_last_visit'); ?></th>
							<th></th>
						</tr>
						</thead>
						<tbody>

						<?php foreach($users as $user) :?>

							<tr data-id="<?php echo $user['id_user'] ?>">
								<td><?php echo $user['screen_name']; ?></td>
								<td><a class="edit" data-id="<?php echo $user['id_user'] ?>"><?php echo $user['email']; ?></a></td>
								<td><?php echo humanize_mdate($user['last_visit'], Settings::get('date_format'). ' %H:%i'); ?></td>
								<td><a class="icon mail" href="mailto:<?php echo $user['email'] ?>"></a></td>
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
								<th axis="date" class="w110"><?php echo lang('ionize_label_join_date'); ?></th>
								<th></th>
							</tr>
							</thead>
							<tbody>

							<?php foreach($last_registered_users as $user) :?>

								<tr data-id="<?php echo $user['id_user'] ?>">
									<td><?php echo $user['screen_name']; ?></td>
									<td><a class="edit" data-id="<?php echo $user['id_user'] ?>"><?php echo $user['email']; ?></a></td>
									<td><?php echo humanize_mdate($user['join_date'], Settings::get('date_format'). ' %H:%i'); ?></td>
									<td><a class="icon mail" href="mailto:<?php echo $user['email'] ?>"></a></td>
								</tr>

							<?php endforeach ;?>

							</tbody>

						</table>

					</div>

				<?php endif ;?>
			</div>

		</div>
	<?php endif ;?>

	<!-- Content : Pages, Articles -->
	<?php if (Settings::get('display_dashboard_content') == '1'): ?>
		<div id="contentBloc" class="desktopBloc" data-title="<?php echo lang('ionize_dashboard_title_content'); ?>">

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
							<th></th>
							<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
							<th axis="string"><?php echo lang('ionize_label_pages'); ?></th>
							<th axis="date" class="w80"><?php echo lang('ionize_label_updated'); ?></th>
						</tr>
						</thead>

						<tbody>

						<?php foreach ($last_articles as $article) :?>

							<?php
							$title = ($article['title'] != '') ? $article['title'] : $article['name'];
							?>

							<tr>
								<td>
									<a class="article icon edit mr5 left" data-id="<?php echo $article['id_page']; ?>.<?php echo $article['id_article']; ?>"></a>
								</td>
								<td>
									<a class="article" title="<?php echo lang('ionize_label_edit'); ?>" data-id="<?php echo $article['id_page']; ?>.<?php echo $article['id_article']; ?>">
										<?php echo $title; ?><br/>
									</a>
								</td>
								<td>
									<span class="lite"><?php echo $article['breadcrumb']; ?></span>
								</td>
								<td><?php echo humanize_mdate($article['updated'], Settings::get('date_format')); ?></td>
							</tr>

						<?php endforeach ;?>

						</tbody>

					</table>

				</div>

				<!-- Orphan article -->
				<?php if ( ! empty($orphan_articles)) :?>

					<div class="tabcontent">

						<p class="lite mt10">
							<?php echo lang('ionize_help_orphan_articles') ?>
						</p>

						<table class="list mb20 mt0" id="orphanArticlesList">

							<thead>
							<tr>
								<th class="w20"></th>
								<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
								<th axis="date" class="w80"><?php echo lang('ionize_label_page_delete_date'); ?></th>
							</tr>
							</thead>

							<tbody>

							<?php foreach ($orphan_articles as $article) :?>

								<?php
								$title = ($article['title'] != '') ? $article['title'] : $article['name'];
								?>

								<tr class="0x<?php echo $article['id_article']; ?>">
									<td>
										<a class="article icon edit mr5 left" data-id="0.<?php echo $article['id_article']; ?>"></a>
									</td>
									<td>
										<a class="article" title="<?php echo lang('ionize_label_edit'); ?>" data-id="0.<?php echo $article['id_article']; ?>">
											<?php echo $title; ?>
										</a>
									</td>
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

						<p class="lite mt10">
							<?php echo lang('ionize_help_orphan_pages') ?>
						</p>

						<table class="list mb20 mt0" id="orphanPagesList">

							<thead>
							<tr>
								<th axis="string"><?php echo lang('ionize_label_page'); ?></th>
								<th axis="date" class="w80"><?php echo lang('ionize_label_page_delete_date'); ?></th>
							</tr>
							</thead>

							<tbody>

							<?php foreach ($orphan_pages as $page) :?>

								<?php
								$title = ($page['title'] != '') ? $page['title'] : $page['name'];
								?>

								<tr>
									<td>
										<a title="<?php echo lang('ionize_label_edit'); ?>" data-id="<?php echo $page['id_page']; ?>" class="page">
											<span class="icon edit mr5 left"></span>
											<?php echo $title; ?>
										</a>
									</td>
									<td><?php echo humanize_mdate($page['updated'], Settings::get('date_format'). ' %H:%i'); ?></td>
								</tr>

							<?php endforeach ;?>

							</tbody>
						</table>
					</div>
				<?php endif ;?>
			</div>
		</div>
	<?php endif ;?>

	<?php if (Settings::get('notification') == '1'): ?>
		<div id="notificationBloc" class="desktopBloc" data-title="<?php echo lang('ionize_title_notification'); ?>">
			<div id="notificationContainer" class="p5"></div>
		</div>
	<?php endif ;?>


</div>


<script type="text/javascript">

	// Panel toolbox
	ION.initToolbox('empty_toolbox');

	// Togglers
	ION.initAccordion('.toggler', 'div.element', false, 'dashboardAccordion');

	// Titles
	$$('.desktopBloc').each(function(bloc)
	{
		new ION.ContentPanel({
			'id': bloc.id,
			'title': bloc.getAttribute('data-title'),
			'container':bloc
		});
	});

	// Add columns
	var col1 = new Element('div', {'class':'col'});
	var col2 = col1.clone();
	$('dashboardContainer').adopt(col1,col2);

	$$('.desktopBloc:not(div.fullwidth)').each(function(bloc, idx)
	{
		if (idx%2 !=0) col2.adopt(bloc);
		else col1.adopt(bloc);
	});

	<?php if (
		Settings::get('dashboard_google') == '1'
		&& Settings::get('google_analytics_profile_id') !=''
		&& Settings::get('google_analytics_email') !=''
		&& Settings::get('google_analytics_password') !=''
	) :?>
		// Diagnostic : Data table
		ION.HTML(
			admin_url + 'google/get_dashboard_report',
			{},
			{
				'update': 'gaReport',
				'onSuccess' : function(){
					$('gaReport').removeClass('loading');
				}
			}
		);
	<?php endif ;?>


	// Articles edit
	var articles = ($$('#articleList .article')).append($$('#orphanArticlesList .article'));

	// Tabs
	if ($('dashBoardUsersTab'))
	{
		var dashBoardUsersTabSwapper = new TabSwapper(
		{
			tabsContainer: 'dashBoardUsersTab',
			sectionsContainer: 'dashBoardUsersTabContent',
			selectedClass: 'selected',
			deselectedClass: '',
			tabs: 'li', clickers: 'li a', sections: 'div.tabcontent',
			cookieName: 'dashBoardUsersTab'
		});
	}
	if ($('dashBoardContentTab'))
	{
		var dashBoardContentTabSwapper = new TabSwapper(
		{
			tabsContainer: 'dashBoardContentTab',
			sectionsContainer: 'dashBoardContentTabContent',
			selectedClass: 'selected',
			deselectedClass: '',
			tabs: 'li', clickers: 'li a', sections: 'div.tabcontent',
			cookieName: 'dashBoardContentTab'
		});
	}

	articles.each(function(item, idx)
	{
		item.addEvent('click', function(e){
			e.stop();
            ION.splitPanel({
                'urlMain': admin_url + 'article/edit/' + item.getProperty('data-id'),
                'urlOptions': admin_url + 'article/get_options/' + item.getProperty('data-id'),
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
		var id = item.getProperty('data-id');
		var title = item.get('text');
		
		item.addEvent('click', function(e)
		{
			e.stop();

			ION.splitPanel({
				'urlMain': admin_url + 'page/edit/' + id,
				'urlOptions': admin_url + 'page/get_options/' + id,
				'title': Lang.get('ionize_title_edit_page') + ' : ' + title
			});
		});
	});

	if ($('shortcutBloc'))
	{
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
	}

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

	/*
	 * @TODO: Send the controller to use to reload the user's list
	 *
	 */
	// Users : Last logged
	$$('#usersList tbody tr td .edit').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			e.stop();
			var id = item.getProperty('data-id');
			ION.formWindow(
				'user'+ id,
				'userForm'+ id,
				'ionize_title_user_edit',
				'user/edit',
				{width: 400, resize:true},
				{
					'id_user': id,
					'from':'dashboard'
				}
			);
		});
	});

	// Users : Last registered
	$$('#lastusersList tbody tr td .edit').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			e.stop();
			var id = item.getProperty('data-id');
			ION.formWindow(
				'user'+ id,
				'userForm'+ id,
				'ionize_title_user_edit',
				'user/edit',
				{width: 400, resize:true},
				{
					'id_user': id,
					'from':'dashboard'
				}
			);
		});
	});

	// Quick Settings
	$$('#quickSettingsForm dl.card').each(function(card)
	{
		card.addEvent('mouseup', function()
		{
			ION.JSON.delay(150, ION, [ION.adminUrl + 'setting/save_quicksettings',$('quickSettingsForm')]);
		});
	});

	// Notifications
	if ($('notificationContainer'))
	{
		ION.loadAsset(
			ION.themeUrl + 'javascript/ionize/ionize_notifications.js',
			{
				onComplete: function()
				{
					var n = new ION.Notifications({container:'notificationContainer'});
					n.get();
				}
			}
		);
	}

</script>