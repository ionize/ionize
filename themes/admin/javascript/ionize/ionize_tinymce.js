
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
					language : Lang.current,
					entity_encoding : 'raw',
					height: height,
					width: width,
					dialog_type : 'modal',
					inlinepopups_skin: 'ionizeMce',
					verify_html : false,
					relative_urls : false,
					convert_urls : false,
					auto_cleanup_word : false,
					gecko_spellcheck: true,
					plugins : 'save,inlinepopups,advimage,advlink,spellchecker,nonbreaking,,media,preview,directionality,paste,fullscreen,template,table,advimage,advlink,spellchecker',
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_resizing : true,
					theme_advanced_resizing_use_cookie : false,
					theme_advanced_path_location : 'bottom',
					theme_advanced_blockformats : tinyBlockFormats,
					theme_advanced_buttons1 : smallTinyButtons1,
					theme_advanced_buttons2 : smallTinyButtons2,
					theme_advanced_buttons3 : smallTinyButtons3,
					/*
					theme_advanced_buttons1 : 'bold,italic,|,bullist,numlist,|,link,unlink,image,|,nonbreaking',
					theme_advanced_buttons2 : '',
					theme_advanced_buttons3 : '',
					*/
					content_css : tinyCSS,
					file_browser_callback: 'ION.openTinyFilemanager',
					save_onsavecallback:function(ed)
					{
						var submit = ed.formElement.retrieve('submit');
						if (typeOf(submit) != 'null')
							submit.fireEvent('click');
						return false;
					},
					setup : function(ed) {
						// Register mceIonizeHrefBrowser, called by advlink plugin (modified plugin)
						ed.addCommand('mceIonizeHrefBrowser', function(ui, v) {
							ION.openIonizeHrefBrowser(ed, ui, v);
						});
						ed.addCommand('mceIonizeHrefName', function(ui, v) {
							ION.getIonizeHrefName(ed, ui, v);
						});
						ed.onKeyUp.add(function(ed, e) {
							ION.setUnsavedData();
						});
						ION.tinySmallOnSetup(ed);
					}
				};
				return settings;
				break;
			
			default:

				var settings = {
					mode : 'exact',
					elements : id,
					theme : 'advanced',
					skin: 'ionizeMce',
					language : Lang.current,
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
					gecko_spellcheck: true,
					plugins : 'pdw,save,inlinepopups,codemirror,safari,nonbreaking,media,preview,directionality,paste,fullscreen,template,table,advimage,advlink,spellchecker',
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
					save_onsavecallback:function(ed)
					{
						var submit = ed.formElement.retrieve('submit');
						if (typeOf(submit) != 'null')
							submit.fireEvent('click');
						return false;
					},
					// Could be nice to do it through one dedicated callback, but seems not possible
					// ionize_hrefbrowser_callback: 'ION.openIonizeHrefBrowserCallback',
					setup : function(ed) {
						// Register mceIonizeHrefBrowser, called by advlink plugin (modified plugin)
						ed.addCommand('mceIonizeHrefBrowser', function(ui, v) {
							ION.openIonizeHrefBrowser(ed, ui, v);
						});
						ed.addCommand('mceIonizeHrefName', function(ui, v) {
							ION.getIonizeHrefName(ed, ui, v);
						});
						ed.onKeyUp.add(function(ed, e) {
							ION.setUnsavedData();
						});
						ION.tinyOnSetup(ed);
					},
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

		if (typeOf(tab_selector) == 'null')
		{
			textareas.each(function(target)
			{
				// First remove tiny editor of object with this ID.
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
				var ed = tinyMCE.editors[target.id];
				if (typeOf(ed) != 'object')
				{
					tinyMCE.init(ION.tinyMceSettings(target.id, mode, options));
				}
			});
		}
		else
		{
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
				/*
				(tinyMCE.editors).each(function(tiny)
				{
					if (typeOf(tiny) == 'object')
					{
						console.log(tiny.id + ' in memory.');
					}
				});
				*/
			});
		}
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
						assetsUrl: theme_url + 'javascript/filemanager/assets',
						standalone: false,
						createFolders: true,
						destroy: true,
						rename: true,
						upload: true,
						move_or_copy: true,
						resizeOnUpload: Settings.get('resize_on_upload'),
						uploadAutostart: Settings.get('upload_autostart'),
						uploadMode: Settings.get('upload_mode'),
						language: Lang.current,
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
					var filemanager = new Filemanager(fmOptions);
					
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
	},


	/**
	 * Opens the Ionize Href browser
	 * @param ed    TinyMCEPopup editor object
	 * @param ui    bool
	 * @param v     value send to this method when called by advlink.getIonizeHrefBrowser()
	 *              see : advlink.js
	 *              {
	 *                  func: function(e){...}
	 *              }
	 */
	openIonizeHrefBrowser: function(ed, ui, v)
	{
		// Set the MUI Window on the top of Tiny's modals
		// tinyMCE modals are stored at 300000, Dialogs at 400000
		MUI.Windows.indexLevel = 350000;

		// 1. Open the browser window
		var ionWindow = ION.dataWindow(
			'ionizeHrefBrowser',
			Lang.get('ionize_title_tree_browser'),
			admin_url + 'tree/browser',
			{
				width: 400,
				height: 300,
				// The tree fires the onSelect event on its parent window, if any.
				// Get the element reference
				onSelect: function(rel)
				{
					var element = 'page';
					if (rel.indexOf('.') > 0) element = 'article';

					v.func('{{' + element + ':' + rel + '}}');
					this.close();
				}
			}
		);
	},

	getIonizeHrefName: function(ed, ui, v)
	{
		var mceHref = v.href;

		mceHref = mceHref.replace('{{', '');
		mceHref = mceHref.replace('}}', '');

		var entity = mceHref.split(':');
		var type = entity[0];
		var rel = entity[1];

		ION.JSON(
			admin_url + 'tree/get_entity',
			{
				'type':type,
				'rel':rel
			},
			{
				onSuccess:function(responseJSON, responseText)
				{
					if(typeOf(responseJSON.page) != 'null')
					{
						var breadcrumb = responseJSON.page.title;

						if (typeOf(responseJSON.article) != 'null')
							breadcrumb = breadcrumb + ' > ' + responseJSON.article.title;

						v.func(breadcrumb);
					}
					else
					{
						v.func('<span class="error">' + Lang.get('ionize_message_internal_link_not_found') + '</span>');
					}
				}
			}
		);
	},


	/**
	 * Called at tinyMCE setup
	 * Can be overidden by user's theme JS if located in :
	 * /themes/my_theme/assets/javascript/tinyMCE.js
	 *
	 * @param ed
	 *
	 */
	tinyOnSetup:function(ed){},


	/**
	 * Called at tinyMCE small editors setup
	 * Can be overidden by user's theme JS if located in :
	 * /themes/my_theme/assets/javascript/tinyMCE.js
	 *
	 * @param ed
	 *
	 */
	tinySmallOnSetup:function(ed){}

});

