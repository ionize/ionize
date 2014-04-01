/* 

 Ionize main menu intialization

*/

var Ionize = (Ionize || {});

Ionize.initializeDesktop = function(){

	ION.Authority.initialize(
	{
		'onComplete':function()
		{
			MUI.create({
				'control':'MUI.Desktop',
				'id':'desktop',
				'taskbar':true,
				'content':[
					{name:'header', url: admin_url + 'desktop/get_header'},
					{name:'taskbar'},
					{name:'content',
					columns:[
						{
							id: 'sideColumn', placement: 'left', width: 280, resizeLimit: [222, 600],sortable: false,
							panels:[
							{
								id: 'structurePanel',
								title: '',
								cssClass: 'panelAlt',
								isCollapsed: (false == ION.Authority.can('access', 'admin/tree')),
								content: [
									{url: admin_url + 'tree'},
									{
										name: 'toolbox',
										position: 'header',
										cssClass: 'left',
										divider: false,
										url: admin_url + 'desktop/get/toolboxes/structure_toolbox',
										onLoaded:function(){
										}
									}
								]
							}
							/*,
							{
								title: 'Tasks',
								id: 'splitPanel_tasks',
								cssClass: 'panelAlt',
								header: true,
								isCollapsed: false,
								content: 'coucou'

							}
							*/
							]
						},
						{id: 'mainColumn',	placement: 'main', resizeLimit: [100, 300],sortable: false,
							panels:[
							{
								id: 'mainPanel',
								padding: {top: 0, right: 0, bottom: 0, left: 0},
								title: Lang.get('ionize_title_welcome'),
								content: [
									{url: admin_url + 'dashboard'},
									{
										name: 'toolbox',
										position: 'header',
										url: admin_url + 'desktop/get/toolboxes/empty_toolbox'
									}
								],
								collapsible: false,
								onLoaded: function(el)
								{
									//	mediaManager.toggleFileManager({standalone:true});
								},
								onDrawEnd: function()
								{
									$('mainPanel').addClass('bg-gray');
								}
								// ,onResize: Ionize.updateResizeElements
							}]
						}
					]}
				]
			});
		}
	});


};



// Initialize MochaUI when the DOM is ready
window.addEvent('load', function()
{
	MUI.initialize({path:{root:theme_url + 'javascript/mochaui/'}});
	MUI.register('MUI.Windows', MUI.Windows);

	Ionize.initializeDesktop();

	// User singleton
	ION.User = new ION.UserClass();

//	document.addEvent('click', function(){$$('.btn-group').removeClass('open')});

});

