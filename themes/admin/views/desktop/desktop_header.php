
<div id="desktopBar">

<?php

	$lang_url = base_url().Settings::get_lang('current').'/'.Settings::get('admin_url');
?>

<div class="desktopTitlebarWrapper">
	<div class="desktopTitlebar">
		<h1 class="applicationTitle">ionize <?php echo($this->config->item('version')) ;?></h1>
		<a id="logoAnchor"></a>
		
		<div class="topNav">
			<ul class="menu-right">
				<li><?= lang('ionize_logged_as') ?> : <?= $current_user['screen_name'] ?></li>
				<li><a href="<?= base_url() ?>" target="_blank"><?= lang('ionize_website') ?></a></li>
				<li><a href="<?= base_url().Settings::get_lang('current').'/'.config_item('admin_url') ?>/user/logout"><?= lang('ionize_logout') ?></a></li>
				<li>
					<?php foreach(Settings::get('displayed_admin_languages') as $lang) :?>
						<a href="<?= base_url().$lang ?>/<?= config_item('admin_url')?>"><img src="<?= theme_url() ?>images/world_flags/flag_<?= $lang ?>.gif" alt="<?= $lang ?>" /></a>
					<?php endforeach ;?>
				</li>
			</ul>
		</div>
	</div>
</div>


<div id="desktopNav" class="desktopNav">

	<div class="toolMenu left">

		<ul>
			<li><a class="navlink" href="dashboard" title="<?= lang('ionize_title_welcome')?>"><?= lang('ionize_menu_dashboard')?></a></li>
			<li><a class="returnFalse" href=""><?= lang('ionize_menu_content') ?></a>	
				<ul>
					<?php if($this->connect->is('super-admins')) :?>
						<li><a class="navlink" href="menu" title="<?= lang('ionize_title_menu') ?>"><?=lang('ionize_menu_menu')?></a></li>
					<?php endif ;?>
					<li><a class="navlink" href="page/create/0" title="<?= lang('ionize_title_new_page') ?>"><?= lang('ionize_menu_page') ?></a></li>
					<li><a class="navlink" href="article/list_articles" title="<?= lang('ionize_title_articles') ?>"><?= lang('ionize_menu_articles') ?></a></li>
					<li><a class="navlink" href="translation" title="<?= lang('ionize_title_translation') ?>"><?= lang('ionize_menu_translation') ?></a></li>
					<li class="divider"><a id="mediamanagerlink" href="media/get_media_manager" title="<?= lang('ionize_menu_media_manager') ?>"><?= lang('ionize_menu_media_manager') ?></a></li>
					<?php if ($this->connect->is('super-admins')) :?>
						<li class="divider"><a class="navlink" href="element_definition/index" title="<?= lang('ionize_menu_content_elements') ?>"><?= lang('ionize_menu_content_elements') ?></a></li>
					<?php endif ;?>
					<?php if ($this->connect->is('super-admins') ) :?>
						<li><a class="navlink" href="extend_field/index" title="<?= lang('ionize_menu_extend_fields') ?>"><?= lang('ionize_menu_extend_fields') ?></a></li>
					<?php endif ;?>
				</ul>
			</li>
			<?php if($this->connect->is('editors')) :?>
			<li><a class="returnFalse" href=""><?= lang('ionize_menu_modules') ?></a>
				<ul>
					<!-- Module Admin controllers links -->
					<?php foreach($modules as $uri => $module) :?>
						<?php if($this->connect->is($module['access_group'])) :?>
							<li><a class="navlink" id="<?= $uri ?>ModuleLink" href="module/<?= $uri ?>/<?= $uri ?>/index" title="<?= $module['name'] ?>"><?= $module['name'] ?></a></li>
						<?php endif ;?>								
					<?php endforeach ;?>
					<?php if($this->connect->is('admins')) :?>
						<li class="divider"><a class="navlink" href="modules" title="<?= lang('ionize_title_modules') ?>"><?=lang('ionize_menu_modules_admin')?></a></li>
					<?php endif ;?>
				</ul>
			</li>
			<?php endif ;?>
			<li><a class="returnFalse" href=""><?= lang('ionize_menu_tools') ?></a>
				<ul>
					<li><a href="https://www.google.com/analytics/reporting/login" target="_blank">Google Analytics</a></li>
					<li><a class="navlink" href="system_check"><?=lang('ionize_menu_system_check')?></a></li>
				</ul>
			</li>
		
			<li><a class="returnFalse" href=""><?=lang('ionize_menu_settings')?></a>
				<ul>
					<li><a class="navlink" href="setting/ionize" title="<?= lang('ionize_menu_ionize_settings') ?>"><?=lang('ionize_menu_ionize_settings')?></a></li>
					<li><a class="navlink" href="lang" title="<?= lang('ionize_menu_languages') ?>"><?=lang('ionize_menu_languages')?></a></li>
					<?php if($this->connect->is('admins')) :?>
						<li><a class="navlink" href="users" title="<?= lang('ionize_menu_users') ?>"><?=lang('ionize_menu_users')?></a></li>
					<?php endif ;?>
					<?php if($this->connect->is('super-admins')) :?>
						<li><a class="navlink" href="setting/themes" title="<?= lang('ionize_title_theme') ?>"><?=lang('ionize_menu_theme')?></a></li>
					<?php endif ;?>
					<li class="divider"><a class="navlink" href="setting" title="<?= lang('ionize_menu_site_settings') ?>"><?=lang('ionize_menu_site_settings')?></a></li>
					<?php if($this->connect->is('super-admins')) :?>
						<li><a class="navlink" href="setting/technical" title="<?= lang('ionize_menu_site_settings_technical') ?>"><?=lang('ionize_menu_site_settings_technical')?></a></li>
					<?php endif ;?>
				</ul>
			</li>
			<li><a class="returnFalse" href=""><?= lang('ionize_menu_help') ?></a>
				<ul>
					<?php if (is_dir(realpath(APPPATH.'../user-guide'))) :?>
						<li><a id="docLink" href="../user-guide/index.html" target="_blank"><?= lang('ionize_menu_documentation') ?></a></li>								
					<?php endif; ?>
					<li<?php if (is_dir(realpath(APPPATH.'../user-guide'))) :?> class="divider"<?php endif; ?>><a id="aboutLink" href="<?= theme_url() ?>views/about.html"><?= lang('ionize_menu_about') ?></a></li>
				</ul>
			</li>
		</ul>
	</div>
	

	<div id="desktopNavToolbar_spinner" class="spinner"></div>		


	<!--
	<div class="toolbox">
		<div id="spinnerWrapper"><div id="spinner"></div></div>		
	</div>
	-->
	
</div><!-- /desktopNavbar -->


</div>

<script type="text/javascript">

	$$('.navlink').each(function(item)
	{
		item.addEvent('click', function(event)
		{
			event.preventDefault();
			
			MUI.Content.update({
				url: admin_url + ION.cleanUrl(this.getProperty('href')),
				element: 'mainPanel',
				title: this.getProperty('title')
			});
		});
	});
	
	$('mediamanagerlink').addEvent('click', function(event)
	{
		event.preventDefault();
		
		MUI.Content.update({
			url: admin_url + ION.cleanUrl(this.getProperty('href')),
			element: 'mainPanel',
			title: this.getProperty('title'),
			padding: {top: 0, right: 0, bottom: 0, left: 0}
		});
	});


	$('aboutLink').addEvent('click', function(event)
	{
		event.preventDefault();

		new MUI.Modal({
			id: 'about',
			title: 'MUI',
			content: {url: admin_url + 'desktop/get/about'},
			type: 'modal2',
			width: 360,
			height: 210,
			padding: {top: 70, right: 12, bottom: 10, left: 22},
			scrollbars: false
		});
	});
	
</script>

