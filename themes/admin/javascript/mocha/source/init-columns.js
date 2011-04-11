/** 
 * Ionize UI Init 
 *
 */

initializeColumns = function() {

	/* 
	 * Main UI options	
	 *
	 */
	
	// Windows Corner radius
	windowOptions = MUI.Windows.windowOptions;
	windowOptions.cornerRadius = 0;
	
	// Windows Shadows
	// windowOptions.shadowBlur = 5;	
	MUI.Window.implement({ options: windowOptions });
	
	// Hide desktop header
//	$('desktopHeader').hide();


	/*
	 * Create Columns
	 *	 
	 */	 
	new MUI.Column({
		id: 'sideColumn',
		placement: 'left',
		sortable: false,
		width: 280,
		resizeLimit: [222, 600]
	});

	new MUI.Column({
		id: 'mainColumn',
		placement: 'main',	
		sortable: false,
		resizeLimit: [100, 500],
		evalScripts: true
	});

	// Add Site structure panel to side column
	new MUI.Panel({
		id: 'structurePanel',
//		title: Lang.get('ionize_title_structure'),
		title: '',
		loadMethod: 'xhr',
		contentURL: admin_url + 'core/get_structure',
		column: 'sideColumn',
		panelBackground:'#f2f2f2',
		padding: { top: 15, right: 0, bottom: 8, left: 15 },
		headerToolbox: true,
		headerToolboxURL: admin_url + 'desktop/get/toolboxes/structure_toolbox',
		headerToolboxOnload: function(){

			// ToggleHeader Button
			$('toggleHeaderButton').addEvent('click', function(e)
			{
				e.stop();
				var cn = 'desktopHeader';
				var el = $(cn);
				var opened = 'true';
				
				if (Cookie.read(cn))
				{
					opened = (Cookie.read(cn));
				}
				if (opened == 'false')
				{
					Cookie.write(cn, 'true');
					el.show();
				}
				else
				{
					Cookie.write(cn, 'false');
					el.hide();
				}

//				$('desktopHeader').toggle();
				window.fireEvent('resize');
			});
			
			// Init desktopHeader status from cookie
			var dh = $('desktopHeader');
			var opened = (Cookie.read('desktopHeader'));
			if (opened == 'false') {dh.hide();}
			else {dh.show();} 
			window.fireEvent('resize');
		}
	});

	// Add Info panel to side column
/*
	new MUI.Panel({
		id: 'infoPanel',
		title: 'Debug',
		loadMethod: 'xhr',
		contentURL: base_url + 'admin/core/get_info',
		column: 'sideColumn',
		panelBackground: '#fff',
			padding: { top: 15, right: 15, bottom: 8, left: 15 },
		onContentLoaded: function(c) 
		{
//			log = new Log('debug');
		}		
		
	});
*/

	// Add panels to main column	
	new MUI.Panel({
		id: 'mainPanel',
		title: Lang.get('ionize_title_welcome'),
		loadMethod: 'xhr',
		contentURL: admin_url + 'dashboard',
		padding: { top: 15, right: 15, bottom: 8, left: 15 },
		addClass: 'pad-maincolumn',
		column: 'mainColumn',
		collapsible: false,
		panelBackground: '#fff',
		headerToolbox: true,
		headerToolboxURL: admin_url + 'desktop/get/toolboxes/empty_toolbox'
	});


	MUI.myChain.callChain();
}
