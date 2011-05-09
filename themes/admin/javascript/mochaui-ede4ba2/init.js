/* 

 Ionize main menu intialization

*/

var Ionize = (Ionize || {});

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
						],
						collapsible: false
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
	Ionize.initializeDesktop();
	MUI.register('MUI.Windows', MUI.Windows);
});

