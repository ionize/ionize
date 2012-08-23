

ION.append({
	
	tinyMceSettings: function(id, mode, options)
	{
		var options = (typeOf(options) != 'null') ? options : {};

		var width = (typeOf(options.width) != 'null') ? options.width : '100%';
		var height = (typeOf(options.height) != 'null') ? options.height : 180;
	
		switch (mode)
		{
			case 'small':

				var settings =  
				{
					mode : 'exact',
					elements : id,
					theme : 'advanced',
					skin: 'ionizeMce',
					language : Lang.get('current'),
					entity_encoding : 'raw',
					height: height,
					width: width,
					dialog_type : 'modal',
					inlinepopups_skin: 'ionizeMce',
//					extended_valid_elements  : "ion:*, a[href</<ion:*]",
//					extended_valid_elements : "iframe[align<bottom?left?middle?right?top|class|frameborder|height|id|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style|title|width]",
					verify_html : false,
					relative_urls : false,
					convert_urls : false,
					auto_cleanup_word : false,
					plugins : 'inlinepopups,advimage,advlink,spellchecker,nonbreaking',
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_resizing : true,
					theme_advanced_resizing_use_cookie : false,
					theme_advanced_path_location : 'bottom',
					theme_advanced_buttons1 : 'bold,italic,|,bullist,numlist,|,link,unlink,image,|,nonbreaking',
					theme_advanced_buttons2 : '',
					theme_advanced_buttons3 : '',
					content_css : tinyCSS,
					file_browser_callback: 'ION.openTinyFilemanager'
				};
				return settings;
				break;
			
			default:
				// Removed plugin preelementfix
				var settings = {
					mode : 'exact',
					elements : id,
					theme : 'advanced',
					skin: 'ionizeMce',
					language : Lang.get('current'),
					entity_encoding : 'raw',
					height:'450',
					width:'100%',
					dialog_type : 'modal',
					inlinepopups_skin: 'ionizeMce',
//					extended_valid_elements  : "ion:*, a[href</<ion:*]",
//					extended_valid_elements : "iframe[align<bottom?left?middle?right?top|class|frameborder|height|id|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style|title|width]",
					verify_html : false,
					relative_urls : false,
					convert_urls : false,
					auto_cleanup_word : false,
					plugins : 'pdw, inlinepopups,codemirror,safari,nonbreaking,media,preview,directionality,paste,fullscreen,template,table,advimage,advlink,spellchecker',
					flash_menu : 'false',
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_resizing : true,
					theme_advanced_resizing_use_cookie : false,
					theme_advanced_path_location : 'bottom',
					theme_advanced_blockformats : tinyBlockFormats,
					theme_advanced_disable : 'help',
					theme_advanced_buttons1 : tinyButtons1,
					theme_advanced_buttons2 : tinyButtons2,
					theme_advanced_buttons3 : tinyButtons3,
					content_css : tinyCSS,
		            // PDW Toggle Toolbars settings
		            pdw_toggle_on : 1,
		            pdw_toggle_toolbars : '2,3',
					file_browser_callback: 'ION.openTinyFilemanager',
					formats : {
						alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
						aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
						alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
						alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'justify'}
					}				
				};
				
				// If users templates, add them to the init object
				if (getTinyTemplates != false)
				{
					settings.template_templates = getTinyTemplates(site_theme_url + 'assets/templates/');
				}

				return settings;
				break;
		}
	},
	
	

	/**
	 * Init TinyMCE on tabbed selectors
	 * @param	string	CSS tabs selectors
	 *					Each tab much have the "rel" attribute set.
	 *					Example : rel="fr"
	 * @param	string	textareas selector. Must have the same "rel" attr. as the atb
	 *					Example : #categoryTabContent .tinyCategory
	 * @param	string	Editor mode. Can be 'small' or empty (normal editor)
	 *
	 *
	 */
	initTinyEditors: function(tab_selector, container_selector, mode, options)
	{
		var textareas = $$(container_selector);
		var mode = mode;
		
		$$(tab_selector).each(function(tab)
		{
			// Current tab language or identifier
			var tab_rel = tab.getProperty('rel');
		
			textareas.each(function(target)
			{
				// Current area language or identifier (related to tab)
				var target_rel = target.getProperty('rel');
				
				if (tab_rel == target_rel)
				{
					// Tab click : Init TinyMCE
					tab.addEvent('click', function(e)
					{
						var ed = tinyMCE.editors[target.id];
		
						if (typeOf(ed) != 'object')
						{
							tinyMCE.init(ION.tinyMceSettings(target.id, mode, options));
						}
					});
					// Remove tiny editor of object with this ID.
					(tinyMCE.editors).each(function(tiny)
					{
						if (typeOf(tiny) != 'null')
						{
							if (tiny.id == target.id)
							{
								tinyMCE.remove(tiny);
							}
						}
					});
					
					// Init Tiny on the visible tab
					if (tab.hasClass('selected'))
					{
						var ed = tinyMCE.editors[target.id];

						if (typeOf(ed) == 'null')
						{
							setTimeout(function() {
								tinyMCE.init(ION.tinyMceSettings(target.id, mode, options));
							}, 50);
						}
					}
				}
			});

			// Debug : List of active tiny object in memory
			
			(tinyMCE.editors).each(function(tiny)
			{
				if (typeOf(tiny) == 'object')
				{
//					console.log(tiny.id + ' in memory.');
				}
			});
			
		});
	},



	openTinyFilemanager:function(field, url, type, win)
	{
		// Get the tokken, get the options...
		var xhr = new Request.JSON(
		{
			url: admin_url + 'media/get_tokken',
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				// Opens the filemanager if the tokken can be retrieved (auth checked by get_tokken() )
				if (responseJSON && responseJSON.tokken != '')
				{
					var fmOptions = {
						url: admin_url + 'media/filemanager',
						URLpath4assets: theme_url + 'javascript/mootools-filemanager/Assets',
//						assetBasePath: theme_url + 'javascript/mootools-filemanager/Assets',
						standalone: false,
						language: Lang.get('current'),
						selectable: true,
						hideOnClick: true,
						propagateData: {'uploadTokken': responseJSON.tokken},
						parentContainer: 'filemanagerWindow_contentWrapper',
						mkServerRequestURL: function(fm_obj, request_code, post_data)
						{
							return {
								url: fm_obj.options.url + '/' + request_code,
								data: post_data
							};
						},
						onComplete: function(path)
						{
							if (!win.document) return;
							win.document.getElementById(field).value = path;
							if (win.ImageDialog) win.ImageDialog.showPreviewImage(path, 1);
							MUI.get('filemanagerWindow').close();
						}
					};

					// Close existing instance of fileManager
					var instance = MUI.get('filemanagerWindow');
					if (instance)
					{
						instance.close();
					}
					
					// Init FM
					var filemanager = new FileManager(fmOptions);
					
					// MUI Window creation
					var options = ION.getFilemanagerWindowOptions();
					
					options.content = filemanager.show();
								
					options.onResizeOnDrag = function() {	filemanager.fitSizes(); }
					
					// Set the MUI Window on the top of Tiny's modals
					// tinyMCE modals are stored at 300000, Dialogs at 400000
					MUI.Windows.indexLevel = 350000;
					
					var w = new MUI.Window(options);
					w.filemanager = filemanager;
				}
				else
				{
					ION.notification('error', Lang.get('ionize_session_expired'));
					return false;
				}
			}
		}).send();
	}
});

