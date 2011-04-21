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

/*
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
*/



Ionize.initializeDesktop = function(){

	MUI.create({
		'control':'MUI.Desktop',
		'id':'desktop',
		'taskbar':true,
		'content':[
			{name:'header', url: admin_url + 'desktop/get_header'},
			{name:'taskbar'},
			{name:'content',columns:[
				{id: 'sideColumn', placement: 'left', width: 280, resizeLimit: [222, 600],
					panels:[
						{
							id: 'structurePanel',
							title: '',
							content: [
								{url: admin_url + 'core/get_structure'},
								{
									name: 'toolbox',
									position: 'header',
									cssClass: 'left',
									divider: false,
									url: admin_url + 'desktop/get/toolboxes/structure_toolbox'
								}
							]
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
						]
	//					,onResize: Ionize.updateResizeElements
					}]
				}
			]}
		]
	});
};

// Initialize MochaUI when the DOM is ready
window.addEvent('load', function()
{
	MUI.initialize({path:{root:theme_url + 'javascript/mochaui-ede4ba2/'}});
//	MUI.register('Ionize', Ionize);
//	MUI.register('MUI.Windows', MUI.Windows);
	Ionize.initializeDesktop();
});

