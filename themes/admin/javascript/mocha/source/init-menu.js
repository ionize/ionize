initializeMenu = function(){

	// Default padding
	var default_padding= { top: 12, right: 15, bottom: 8, left: 15 };


	// Dashboard link...
	if ($('dashboardLink')){ 
		$('dashboardLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_welcome'),
				url : admin_url + 'dashboard'		
			});
		});
	}

	$('logoAnchor').addEvent('click', function(e){
		$('dashboardLink').fireEvent('click',e);
	});

	// Content : Manage Menu ...
	if ($('menuLink')){ 
		$('menuLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_menu'),
				url : admin_url + 'menu'		
			});
		});
	}

	// Content : New Page...
	if ($('newPageLink')){ 
		$('newPageLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_new_page'),
				url : admin_url + 'page/create/0'		
			});
		});
	}

	// Content : Articles
	if ($('articlesLink')){ 
		$('articlesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_articles'),
				url : admin_url + 'article/list_articles'		
			});
		});
	}

	// Translations
	if ($('translationLink')){ 
		$('translationLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_translation'),
				url : admin_url + 'translation/'
			});
		});
	}

	// Content : Media manager
	if ($('mediaManagerLink')){ 
		$('mediaManagerLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_media_manager'),
				url : admin_url + 'media/get_media_manager',
				padding: {top:0, left:0, right:0}
			});
		});
	}


	// Content : Elements Types
	if ($('elementsLink')){ 
		$('elementsLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_content_elements'),
				url : admin_url + 'element_definition/index'
			});
		});
	}



	// Content : Extended fields
	if ($('extendfieldsLink')){ 
		$('extendfieldsLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_extend_fields'),
				url : admin_url + 'extend_field/index'
			});
		});
	}


	// Modules : List
	if ($('modulesLink')){ 
		$('modulesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_modules'),
				url : admin_url + 'modules/'
			});
		});
	}
	
	// Themes
	if ($('themesLink')){ 
		$('themesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_title_theme'),
				url : admin_url + 'setting/themes/'
			});
		});
	}
	
	// Settings : Ionize
	if ($('ionizeSettingLink')){ 
		$('ionizeSettingLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_ionize_settings'),
				url : admin_url + 'setting/ionize'
			});
		});
	}

	// Settings : Global Website settings
	if ($('settingLink')){ 
		$('settingLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_site_settings_global'),
				url : admin_url + 'setting'
			});
		});
	}

	// Settings : Technical settings
	if ($('technicalSettingLink')){ 
		$('technicalSettingLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_site_settings_technical'),
				url : admin_url + 'setting/technical'
			});
		});
	}

	// Settings : Languages...
	if ($('languagesLink')){ 
		$('languagesLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_languages'),
				url : admin_url + 'lang'
			});
		});
	}

	// Settings : Users...
	if ($('usersLink')){ 
		$('usersLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				title: Lang.get('ionize_menu_users'),
				url : admin_url + 'users'
			});
		});
	}

	// Documentation link
	/*
	if ($('docLink')){ 
		$('docLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.updateContent({
				element: $('mainPanel'),
				loadMethod: 'iframe',
				title: Lang.get('ionize_title_documentation'),
				url : base_url + '../user_guide/index.html',
				padding: {top:0, left:0, right:0}
			});
		});
	}
	*/


	// About
	MUI.aboutWindow = function() {
		new MUI.Modal({
			id: 'about',
			title: 'MUI',			
			contentURL: admin_url + 'desktop/get/about',
			type: 'modal2',
			width: 360,
			height: 210,
			y:200,
			padding: { top: 70, right: 12, bottom: 10, left: 22 },
			scrollbars: false
		});
	}
	if ($('aboutLink')) {
		$('aboutLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.aboutWindow();
		});
	}


	// Deactivate menu header links
	$$('a.returnFalse').each(function(el){
		el.addEvent('click', function(e){
			new Event(e).stop();
		});
	});

	MUI.myChain.callChain();

}
