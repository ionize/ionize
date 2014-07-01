
ION.append({
	
	tinyMceSettings: function(id, mode, options)
	{
		var options = (typeOf(options) != 'null') ? options : {};

		var width = (typeOf(options.width) != 'null') ? options.width : '100%';
		var height = (typeOf(options.height) != 'null') ? options.height : 180;

		var spell_langs = new Array();
		var idx = ([Cookie.read('articleTab'), false].pick());
		if (typeOf(idx) == 'null')
			idx = 0;
			Settings.setting.languages.each(function(target)
			{
				var pref = '';
				if (idx == target.ordering - 1)
					pref = '+';
				spell_langs.push(pref + target.name + '=' + target.lang);
			});
		var spellchecker_languages = spell_langs.join();

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
					convert_urls : true,    // was false
					relative_urls: true,    // was false
					document_base_url: ION.baseUrl,
					auto_cleanup_word : false,
					gecko_spellcheck: true,
					valid_elements : "*[*]",
					extended_valid_elements : "*[*]",
					plugins : 'save,inlinepopups,advimage,advlink,nonbreaking,,media,preview,directionality,paste,fullscreen,template,table,advimage,advlink,spellchecker',
					spellchecker_languages: spellchecker_languages,
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_resizing : true,
					theme_advanced_resizing_use_cookie : false,
					theme_advanced_path_location : 'bottom',
					theme_advanced_blockformats : tinyBlockFormats,
					theme_advanced_buttons1 : smallTinyButtons1,
					theme_advanced_buttons2 : '',
					theme_advanced_buttons3 : '',
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
						// Prevent CMD+Left Browser go back in history
						ed.onKeyDown.add(function(ed, e) {
							if (e.metaKey && e.keyCode =='37')
								e.preventDefault();
						});
					}
				};

				if (smallTinyButtons2 != '') settings.theme_advanced_buttons2 = smallTinyButtons2;
				if (smallTinyButtons3 != '') settings.theme_advanced_buttons3 = smallTinyButtons3;

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
					verify_html : false,
					convert_urls : true,    // was false
					relative_urls: true,    // was false
					document_base_url: ION.baseUrl,
					auto_cleanup_word : false,
					gecko_spellcheck: true,
					valid_elements : "*[*]",
					extended_valid_elements : "*[*]",
					plugins : 'pdw,save,inlinepopups,codemirror,safari,nonbreaking,media,preview,directionality,paste,fullscreen,template,table,advimage,advlink,spellchecker',
					spellchecker_languages: spellchecker_languages,
					flash_menu : 'false',
					theme_advanced_toolbar_location : 'top',
					theme_advanced_toolbar_align : 'left',
					theme_advanced_resizing : true,
					theme_advanced_resizing_use_cookie : false,
					theme_advanced_path_location : 'bottom',
					theme_advanced_blockformats : tinyBlockFormats,
					theme_advanced_disable : 'help',
					theme_advanced_buttons1 : tinyButtons1,
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
						// Prevent CMD+Left Browser go back in history
						ed.onKeyDown.add(function(ed, e) {
							if (e.metaKey && e.keyCode =='37')
								e.preventDefault();
						});

						/*
						// Replace Media Shortcode by URLs before displaying
						ed.onBeforeSetContent.add(function(ed, o)
						{
							o.content = ION.tinyFromShortcodeToMediaUrl(o.content);
						});

						// Replace Shortcode as its inserted into editor (which uses the exec command)
						ed.onExecCommand.add(function(a, cmd) {
							if (cmd ==='mceInsertContent'){
								tinyMCE.activeEditor.setContent(
									ION.tinyFromShortcodeToMediaUrl(tinyMCE.activeEditor.getContent())
								);
							}
						});

						// Replace the media URL back to Shortcode on save
						ed.onPostProcess.add(function(a, o) {
							if (o.get)
								o.content = ION.tinyFromMediaUrlToShortcode(o.content);
						});
						*/

						ION.tinyOnSetup(ed);
					},
					formats : {
						alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
						aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
						alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
						alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'justify'}
					}
				};

				settings.theme_advanced_buttons2 = tinyButtons2;
				settings.theme_advanced_buttons3 = tinyButtons3;


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
	 *
	 * @param tab_selector          Each tab much have the "rel" attribute set. Example : rel="fr"
	 * @param container_selector    textareas selector. Must have the same "rel" attr. as the atb.
	 *                              Example : #categoryTabContent .tinyCategory
	 * @param mode                  Editor mode. Can be 'small' or empty (normal editor)
	 * @param options
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
				t_path = path.replace(/^\//g, '');
				win.document.getElementById(field).value = t_path;
				var preview_path = ION.baseUrl + t_path;
				if (win.ImageDialog) win.ImageDialog.showPreviewImage(preview_path, 1);
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
		var winOptions = ION.getFilemanagerWindowOptions();
		winOptions.content = filemanager.show();
		winOptions.onResizeOnDrag = function() {	filemanager.fitSizes(); };

		// Set the MUI Window on the top of Tiny's modals
		// tinyMCE modals are stored at 300000, Dialogs at 400000
		MUI.Windows.indexLevel = 350000;

		var w = new MUI.Window(winOptions);
		w.filemanager = filemanager;
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
	tinySmallOnSetup:function(ed){},



	tinyFromMediaUrlToShortcode : function(co) {

		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ? tinymce.DOM.decode(n[1]) : '';
		};

		return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
			var cls = getAttr(im, 'class');

			if ( cls.indexOf('modxGallery') != -1 )
				return '<p>[['+tinymce.trim(getAttr(im, 'title'))+']]</p>';

			return a;
		});
	},

	tinyFromShortcodeToMediaUrl: function(co)
	{
		return co.replace(/\{\{media:(\d*)\}\}/g, function(a,b)
		{
			// console.log('a : ' + a);
			// console.log('b : ' + b);

			// Here : Get the media src by Ajax request

			var image = 'files/pictures/IMG_8438.jpg';

			return image;
		});
	}


});

