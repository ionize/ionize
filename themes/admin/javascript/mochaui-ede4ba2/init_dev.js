/* 

 In this file we setup our Windows, Columns and Panels,
 and then initialize MochaUI.

 At the bottom of Core.js you can see how to setup lazy loading for your
 own plugins.

 */

/*

 INITIALIZE COLUMNS AND PANELS

 Creating a Column and Panel Layout:

 - If you are not using panels then these columns are not required.
 - If you do use panels, the main column is required. The side columns are optional.

 Columns
 - Create your columns from left to right.
 - One column should not have it's width set. This column will have a fluid width.

 Panels
 - After creating Columns, create your panels from top to bottom, left to right.
 - One panel in each column should not have it's height set. This panel will have a fluid height.
 - New Panels are inserted at the bottom of their column.

 -------------------------------------------------------------------- */
var Ionize = (Ionize || {});

Ionize.updateMainPanel = function(event, item)
{
	MUI.Content.update({
		url: item.url,
		element: 'mainPanel',
		title: item.title
	});
}


Ionize.aboutWindow = function()
{
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
}




Ionize.initializeDesktop = function(){

	MUI.create({
		'control':'MUI.Desktop',
		'id':'desktop',
		'taskbar':true,
		'content':[

//			{name:'bar', cssClass:'desktopBar', control:'MUI.Dock', 'docked':[
				
				{name:'header', url: admin_url + 'desktop/get/desktop/desktop_header'},
/*
				{name:'nav', control:'MUI.Dock', cssClass:'desktopNav', docked:[
					{name: 'menu', position: 'header', control: 'MUI.Menu', divider:false,
						items:[
							{text:'Dashboard',registered:'Ionize.updateMainPanel', url: admin_url + 'dashboard'},
							{text:Lang.get('ionize_menu_content'),items:[
								{text:Lang.get('ionize_menu_menu'),registered:'Ionize.updateMainPanel', url: admin_url + 'menu', title: Lang.get('ionize_title_menu')}, // Menus
								{text:Lang.get('ionize_menu_page'),registered:'Ionize.updateMainPanel', url: admin_url + 'page/create/0', title: Lang.get('ionize_title_new_page')}, // Create Page
								{text:Lang.get('ionize_menu_articles'),registered:'Ionize.updateMainPanel', url: admin_url + 'article/list_articles', title: Lang.get('ionize_title_articles')}, // List articles
								{text:Lang.get('ionize_menu_translation'),registered:'Ionize.updateMainPanel', url: admin_url + 'translation', title: Lang.get('ionize_title_translation')}, // Translations
								{type:'divider'},
								{text:Lang.get('ionize_menu_media_manager'),registered:'Ionize.updateMainPanel', url: admin_url + 'media/get_media_manager', title: Lang.get('ionize_menu_media_manager')},  // Media Manager
								{type:'divider'},
								{text:Lang.get('ionize_menu_content_elements'),registered:'Ionize.updateMainPanel', url: admin_url + 'element_definition/index', title: Lang.get('ionize_menu_content_elements')}, // Content Elements
								{text:Lang.get('ionize_menu_extend_fields'),registered:'Ionize.updateMainPanel', url: admin_url + 'extend_field/index', title: Lang.get('ionize_menu_extend_fields')} // Extends
							]},
							{text:Lang.get('ionize_menu_modules'),items:[
								{type:'divider'},
								{text:Lang.get('ionize_menu_modules_admin'),id:'modulesLink',registered:'Ionize.updateMainPanel', url: admin_url + 'modules', title: Lang.get('ionize_title_modules')} // Modules Admin 
							]},
							{text:Lang.get('ionize_menu_tools'),items:[
								{text:'Google Analytics',id:'googleAnalyticsLink',url:'https://www.google.com/analytics/reporting/login', target:'_blank'} // Google Analytics
							]},
							{text:Lang.get('ionize_menu_settings'),items:[
								{text:Lang.get('ionize_menu_ionize_settings'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting/ionize', title: Lang.get('ionize_menu_ionize_settings')}, // Menus
								{text:Lang.get('ionize_menu_languages'),registered:'Ionize.updateMainPanel', url: admin_url + 'lang', title: Lang.get('ionize_menu_languages')}, // Langs
								{text:Lang.get('ionize_menu_users'),registered:'Ionize.updateMainPanel', url: admin_url + 'users', title: Lang.get('ionize_menu_users')}, // Users
								{text:Lang.get('ionize_menu_theme'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting/themes', title: Lang.get('ionize_title_theme')}, // Themes
								{text:Lang.get('ionize_menu_site_settings'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting', title: Lang.get('ionize_menu_site_settings_global')}, // Site Settings
								{text:Lang.get('ionize_menu_technical_settings'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting/technical', title: Lang.get('ionize_menu_technical_settings')} // Technical Settings
							]},
							{text:Lang.get('ionize_menu_help'),items:[
								{text:Lang.get('ionize_menu_about'),registered:'Ionize.aboutWindow'} // About
							]}
						]
					},
					{control:'MUI.Spinner',divider:false}
				]},
*/
			
//			]},

/*
			{name:'header', url: admin_url + 'core/get/desktop/desktop_titlebar'},
			{name:'nav', control:'MUI.Dock',cssClass:'desktopNav', docked:[
				{name: 'menu', position: 'header', control: 'MUI.Menu', divider:false,
					items:[
						{text:'Dashboard',registered:'Ionize.updateMainPanel', url: admin_url + 'dashboard'},
						{text:Lang.get('ionize_menu_content'),items:[
							{text:Lang.get('ionize_menu_menu'),registered:'Ionize.updateMainPanel', url: admin_url + 'menu', title: Lang.get('ionize_title_menu')}, // Menus
							{text:Lang.get('ionize_menu_page'),registered:'Ionize.updateMainPanel', url: admin_url + 'page/create/0', title: Lang.get('ionize_title_new_page')}, // Create Page
							{text:Lang.get('ionize_menu_articles'),registered:'Ionize.updateMainPanel', url: admin_url + 'article/list_articles', title: Lang.get('ionize_title_articles')}, // List articles
							{text:Lang.get('ionize_menu_translation'),registered:'Ionize.updateMainPanel', url: admin_url + 'translation', title: Lang.get('ionize_title_translation')}, // Translations
							{type:'divider'},
							{text:Lang.get('ionize_menu_media_manager'),registered:'Ionize.updateMainPanel', url: admin_url + 'media/get_media_manager', title: Lang.get('ionize_menu_media_manager')},  // Media Manager
							{type:'divider'},
							{text:Lang.get('ionize_menu_content_elements'),registered:'Ionize.updateMainPanel', url: admin_url + 'element_definition/index', title: Lang.get('ionize_menu_content_elements')}, // Content Elements
							{text:Lang.get('ionize_menu_extend_fields'),registered:'Ionize.updateMainPanel', url: admin_url + 'extend_field/index', title: Lang.get('ionize_menu_extend_fields')} // Extends
						]},
						{text:Lang.get('ionize_menu_modules'),items:[
							{type:'divider'},
							{text:Lang.get('ionize_menu_modules_admin'),id:'modulesLink',registered:'Ionize.updateMainPanel', url: admin_url + 'modules', title: Lang.get('ionize_title_modules')} // Modules Admin 
						]},
						{text:Lang.get('ionize_menu_tools'),items:[
							{text:'Google Analytics',id:'googleAnalyticsLink',url:'https://www.google.com/analytics/reporting/login', target:'_blank'} // Google Analytics
						]},
						{text:Lang.get('ionize_menu_settings'),items:[
							{text:Lang.get('ionize_menu_ionize_settings'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting/ionize', title: Lang.get('ionize_menu_ionize_settings')}, // Menus
							{text:Lang.get('ionize_menu_languages'),registered:'Ionize.updateMainPanel', url: admin_url + 'lang', title: Lang.get('ionize_menu_languages')}, // Langs
							{text:Lang.get('ionize_menu_users'),registered:'Ionize.updateMainPanel', url: admin_url + 'users', title: Lang.get('ionize_menu_users')}, // Users
							{text:Lang.get('ionize_menu_theme'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting/themes', title: Lang.get('ionize_title_theme')}, // Themes
							{text:Lang.get('ionize_menu_site_settings'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting', title: Lang.get('ionize_menu_site_settings_global')}, // Site Settings
							{text:Lang.get('ionize_menu_technical_settings'),registered:'Ionize.updateMainPanel', url: admin_url + 'setting/technical', title: Lang.get('ionize_menu_technical_settings')} // Technical Settings
						]},
						{text:Lang.get('ionize_menu_help'),items:[
							{text:Lang.get('ionize_menu_about'),registered:'Ionize.aboutWindow'} // About
						]}
					]
				},
				{control:'MUI.Spinner',divider:false}
			]},
*/
			{name:'taskbar'},
			{name:'content',columns:[
				{id: 'sideColumn', placement: 'left', width: 280, resizeLimit: [222, 600],
					panels:[
						{
							id: 'structurePanel',
							title: '',
							content: {
								url: admin_url + 'core/get_structure'
							}
						}
					]
				},
				{id: 'mainColumn',	placement: 'main', resizeLimit: [100, 300],
					panels:[
					{
						id: 'mainPanel',
						title: Lang.get('ionize_title_welcome'),
						content: [
							{url: admin_url + 'dashboard'},
							{
								name: 'toolbox',
								position: 'header',
								url: admin_url + 'desktop/get/toolboxes/empty_toolbox'
							}
						],
						onResize: Ionize.updateResizeElements
					}]
				}
			]}
		]
	});
};
/*
Ionize.initialize = function(){

//	new MUI.Require({js:['scripts/demo-shared.js'],
//		'onload':function(){
			// Initialize MochaUI options
			MUI.initialize({path:{root:theme_url + 'javascript/mochaui-ede4ba2/'}});
//			MUI.load(['Parametrics','famfamfam','CoolClock']);
			MUI.register('Ionize', Ionize);
			MUI.register('MUI.Windows', MUI.Windows);
			Ionize.initializeDesktop();
//		}
//	});
};
*/
// Initialize MochaUI when the DOM is ready
window.addEvent('load', function()
{
	MUI.initialize({path:{root:theme_url + 'javascript/mochaui-ede4ba2/'}});
	MUI.register('Ionize', Ionize);
	MUI.register('MUI.Windows', MUI.Windows);
	Ionize.initializeDesktop();
	initializeContent();
/*
	MUI.myChain = new Chain();
	MUI.myChain.chain(
		function(){
//			MUI.initialize({path:{root:theme_url + 'javascript/mochaui-ede4ba2/'}});
			Ionize.initializeDesktop();
		},
		function(){
			initializeContent();
		}
	).callChain();
*/
});

// Initialize MochaUI when the DOM is ready
// window.addEvent('load', Ionize.initialize); //using load instead of domready for IE8
