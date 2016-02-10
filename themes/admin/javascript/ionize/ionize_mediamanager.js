/** MediaManager
 *	Opens the choosen media / file manager and get the transmitted file name
 *
 *	Options :
 *
 *		baseUrl:			URL to the website
 *		parent:				type of the parent. 'article', 'page', etc. Used to update the database table.                 
 *		id_parent:			ID of the parent element
 *		button:				DOM opener button name
 */

var IonizeMediaManager = new Class(
{
	Implements: Options,

	options: {
		parent:			false,
		id_parent:		false,
		thumbSize:		120,
	    resizeOnUpload: false,
	    uploadAutostart: false,
	    uploadMode:     '',
	    standalone:     false
    },

	/**
	 *
	 * @param options
	 */
	initialize: function(options)
	{
		this.setOptions(options);

		this.baseUrl =		ION.baseUrl;
		this.adminUrl =		ION.adminUrl;
		this.themeUrl =		ION.themeUrl;

		this.id_parent =	null;
		this.parent =		null;
		this.id_extend =	null;
		this.lang =	        null;
		this.filemanager =  null;

		if (options)
			this.init(options);

		return this;
	},


	/**
	 *
	 * @param options
	 */
	init: function(options)
	{
		this.parent = options.parent;
		this.id_parent = options.id_parent;
		this.id_extend = options.id_extend;
		this.lang = options.lang;

		this.container = (typeOf(options.container) == 'string') ? $(options.container) :
			(typeOf(options.container) == 'element' ? options.container : null);

		if (options.tab) this.tab = options.tab;
		if (options.extend_label) this.extend_label = options.extend_label;

		if (options.container != null)
		{
			this.buildContainer();

			this.catchInstance();
		}
	},


	getOptions: function()
	{
		return {
			container: this.container,
			tab: this.tab,
			parent: this.parent,
			id_parent: this.id_parent,
			id_extend: this.id_extend,
			extend_label: this.extend_label,
			lang: this.lang
		}
	},


	catchInstance: function()
	{
		var self = this;

		var elFilemanagerWindow = $('filemanagerWindow');
		if (elFilemanagerWindow) {
			// Window
			var inst = elFilemanagerWindow.retrieve('instance');

			// FM instance
			this.filemanager = inst.filemanager;

			// Set the onComplete target : This class !
			this.filemanager.removeEvents('complete');
			this.filemanager.setOptions({'onComplete': self.addMedia.bind(self)});

			this.setFilemanagerTargetInfo();
		}
	},


	getExistingInstance: function()
	{
		var self = this;

		var elFilemanagerWindow = $('filemanagerWindow');
		if (elFilemanagerWindow)
		{
			// Window
			var inst = elFilemanagerWindow.retrieve('instance');

			// FM instance
			this.filemanager = inst.filemanager;

			// Set the onComplete target : This class !
			this.filemanager.removeEvents('complete');
			this.filemanager.setOptions({'onComplete': self.addMedia.bind(self)});

			this.setFilemanagerTargetInfo();

			// Re-open window if minimized or shake if triing to open another FM
			if (inst.isMinimized)
			{
				inst.restore();
			}
			else
			{
				inst.focus();
				$('filemanagerWindow').shake();
			}

			return true;
		}

		return false;
	},


	buildContainer: function()
	{
		var self = this;

		// Button bar
		var p = new Element('p', {'class':'h30'}).inject(this.container);

		// Media List Container
		this.mediaContainer = new Element('div').inject(this.container);

		// Button: Add Media
		new ION.Button({
			'title' : Lang.get('ionize_label_add_media'),
			'class': 'light right',
			'icon' : 'icon-pictures',
			container: p,
			onClick: function(inst, button)
			{
//				self.toggleFileManager();
				self.open();
			},
			onLoaded: function(button)
			{
				button.store('options', self.getOptions());
			}
		});

		// Button : Add Video URL
		new ION.Button({
			'title' : Lang.get('ionize_label_add_video'),
			'class': 'light right',
			'icon' : 'icon-video',
			container: p,
			onClick: function()
			{
				new ION.Window(
				{
					id: 'addExternalMedia',
					type: 'form',
					width:600, height:150,
					form: {
						method: 'post',
						action: self.adminUrl + 'media/add_external_media',
						post: {
							'parent': self.parent,
							'id_parent': self.id_parent,
							'type': 'video'
						},
						onSuccess: function()
						{
							ION.notification('success', Lang.get('ionize_message_operation_ok'));
							self.loadList();
						}
					},
					title: {
						text: Lang.get('ionize_label_add_video'),
						'class': 'video'
					},
					subtitle: Lang.get('ionize_message_paste_video_url'),
					onDraw: function(w)
					{
						var form = w.getForm();

						new Element('textarea', {name:'path', 'class':'inputtext autogrow left ml40 w80p'}).inject(form);

						ION.initFormAutoGrow(form);
					}
				});
			}
		});

		// Button : Reload
		new ION.Button({
			'title' : Lang.get('ionize_label_reload_media_list'),
			'class': 'light left',
			'icon' : 'icon-refresh',
			container: p,
			onClick: function()
			{
				self.loadList();
			}
		});

		// Unlink All
		new ION.Button({
			'title' : Lang.get('ionize_label_detach_all'),
			'class': 'light left',
			'icon' : 'icon-unlink',
			container: p,
			onClick: function()
			{
				self.detachAllMedia();
			}
		});
	},


	/**
	 * Change the FM callback to act for standard parents : Pages, articles, etc.
	 *
	 */
	initParentTarget: function()
	{
		var self = this;
		this.filemanager.removeEvents('complete');
		this.filemanager.setOptions(
		{
			'onComplete': self.addMedia.bind(self)
		});
	},


	/**
	 * Opens fileManager
	 *
	 */
	open:function()
	{
		// No parent
		if ( ! this.id_parent || this.id_parent == '')
		{
			ION.notification('error', Lang.get('ionize_message_please_save_first'));
		}
		else
		{
			// Exit here : no instance needed
			if (this.getExistingInstance())
			{
				this.setFilemanagerTargetInfo();
				return;
			}

			// Create one instance (FM + Window)
			this.createInstance();

			this.setFilemanagerTargetInfo();
		}
	},


	createInstance: function()
	{
		var self = this;

		// Correct windows levels : Get the current highest level.
		MUI.Windows._getWithHighestZIndex();
		var zidx = (MUI.Windows.highestZindex).toInt();
		MUI.Windows.indexLevel = zidx + 100;

		this.filemanager = new Filemanager(
		{
			url: this.adminUrl + 'media/filemanager',
			assetsUrl: this.themeUrl + 'javascript/filemanager/assets',
			standalone: false,
			createFolders: true,
			destroy: ION.Authority.can('delete', 'admin/filemanager'),
			rename: ION.Authority.can('rename', 'admin/filemanager'),
			upload: ION.Authority.can('upload', 'admin/filemanager'),
			move_or_copy: ION.Authority.can('move', 'admin/filemanager'),
			resizeOnUpload: self.options.resizeOnUpload,
			uploadAutostart: self.options.uploadAutostart,
			uploadMode: self.options.uploadMode,
			language: Lang.current,
			selectable: true,
			hideOnSelect: false,
			'onComplete': self.addMedia.bind(self),
			parentContainer: 'filemanagerWindow_contentWrapper',
			mkServerRequestURL: function(fm_obj, request_code, post_data)
			{
				return {
					url: fm_obj.options.url + '/' + request_code,
					data: post_data
				};
			}
		});

		// MUI Window creation
		var winOptions = ION.getFilemanagerWindowOptions();
		winOptions.content = this.filemanager.show();
		winOptions.onResizeOnDrag = function(){
			this.filemanager.fitSizes();
		};

		self.fileManagerWindow = new MUI.Window(winOptions);
		self.fileManagerWindow.filemanager = this.filemanager;
	},


	/**
	 * Adds Target info to the Filemanager window
	 */
	setFilemanagerTargetInfo: function()
	{
		if (this.filemanager)
		{
			var text = Lang.get('ionize_label_filemanager_target') + ' : ' + this.parent + ' : ' + this.id_parent;
			this.filemanager.setTargetInfo(text);
		}
	},


	/**
	 * Adds one medium to the current parent
	 * Called by callback by the file / image manager
	 *
	 * @param file_url       Complete URL to the media. Slashes ('/') were replaced by ~ to permit CI management
	 * @param file
	 */
	addMedia:function(file_url, file)
	{
		var data = {
			path: file_url,
			parent: this.parent,
			id_parent: this.id_parent,
			id_extend: this.id_extend       // can be null
		};

		var url = this.adminUrl + 'media/add_media';

		new Request.JSON(
		{
			'url': url,
			'method': 'post',
			'data': data,
			'onSuccess': this.successAddMedia.bind(this),
			'onFailure': this.failure.bind(this)
		}).send();
	},


	/**
	 * called after 'addMedia()' success
	 * calls 'loadList' with the correct media type returned by the XHR call
	 *
	 * @param responseJSON
	 */
	successAddMedia: function(responseJSON)
	{
		ION.notification(responseJSON.message_type, responseJSON.message);

		// Media list reload
		this.loadList();
	},


	loadList: function()
	{
		var self = this;

		new Request.JSON(
			{
				url : this.adminUrl + 'media/get_media_list',
				data: this.getOptions(),
				'method': 'post',
				'onFailure': this.failure.bind(this),
				'onComplete': function(responseJSON)
				{
					self.completeLoadList(responseJSON);
				}
			}).send();
	},


	completeLoadList: function(json)
	{
		var self = this,
			parent = self.parent,
			id_parent = self.id_parent,
			id_extend = self.id_extend,
			lang = self.lang
		;

		// Hides the spinner
		MUI.hideSpinner();

		this.mediaContainer.empty();

		this.updateNumber(Object.getLength(json.items));

		if (Object.getLength(json.items) > 0)
		{
			var h = ION.getHash(8);

			// Display Media List
			Array.each(json.items, function(media)
			{
				var id = media.id_media,
					div = new Element('div', {'class':'picture drag', 'data-id':media.id_media, id:media.id_media}).inject(self.mediaContainer),
					background = media.type == 'picture' ? 'url(' + ION.adminUrl + 'media/get_thumb/' + media.id_media + '/'  + self.options.thumbSize + '/' + h + ')' : 'url(' + ION.themeUrl + 'javascript/filemanager/assets/images/icons/large/' + media.extension + '.png)',
					thumb = new Element('div', {'class':'thumb', style:'width:'+self.options.thumbSize+'px;height:'+self.options.thumbSize+'px;background-image:'+background}).inject(div),
					icons = new Element('p', {'class':'icons'}).inject(div),
					title = media.file_name.length > 25 ? media.file_name.substr(25) + '...' : media.file_name
					;

				if (media.type != 'picture')
					new Element('span', {'class':'title lite', html:title}).inject(thumb);

			//	if(ION.Authority.can('unlink', 'admin/' + self.parent + '/media'))
			//	{
					var unlink = new Element('a', {'class':'icon unlink right help', title:Lang.get('ionize_label_detach_media')}).inject(icons);
					unlink.addEvent('click', function()
					{
						self.detachMedia(id, parent, id_parent, id_extend, lang);
					});

			//	}

			//	if(ION.Authority.can('edit', 'admin/' + self.parent + '/media'))
			//	{
					var edit = new Element('a', {'class':'icon edit left mr5', title:Lang.get('ionize_label_edit')}).inject(icons);
					edit.addEvent('click', function()
					{
						ION.formWindow(
							'media' + id,
							'mediaForm' + id,
							title,
							ION.adminUrl + 'media/edit/' + id,
							{width:700,height:500,resize:false}
						);
					});
			//	}

				if (media.type == 'picture')
				{
					var refresh = new Element('a', {'class':'icon refresh left mr5 help', title:Lang.get('ionize_label_init_thumb')}).inject(icons);
					refresh.addEvent('click', function()
					{
						self.initThumbs(id);
					});

				}

				new Element('a', {'class':'icon info left help', title:media.path}).inject(icons);

			});

			// Init the sortable
			var sortableMedia = new Sortables(
				this.mediaContainer,
				{
					revert: true,
					handle: '.drag',
					clone: true,
					opacity: 0.5,
					onComplete: function()
					{
						var serialized = this.serialize(0, function(element)
						{
							if (element.getProperty('id'))
								return element.getProperty('data-id');
						});

						self.sortItemList(serialized);
					}
				}
			);

			// Store the first ordering after picture list load
			this.mediaContainer.store('sortableOrder', sortableMedia.serialize(0, function(element)
			{
				return element.getProperty('data-id');
			}));
		}
	},


	/**
	 * Items list ordering
	 * called on items sorting complete
	 * calls the XHR server ordering method
	 *
	 * @param serialized    new order as a string. coma separated
	 *
	 */
	sortItemList: function(serialized)
	{
		var sortableOrder = this.mediaContainer.retrieve('sortableOrder');

		// Remove "undefined" from serialized, which can comes from the clone.
		var serie = new Array();
		serialized.each(function(item)
		{
			if (typeOf(item) != 'null')	serie.push(item);
		});

		// If current <> new ordering : Save it ! 
		if (sortableOrder.toString() != serie.toString() ) 
		{
			// Store the new ordering
			this.mediaContainer.store('sortableOrder', serie);

			// Save the new ordering
			new Request.JSON(
			{
				url: this.adminUrl + 'media/save_ordering/' + this.parent + '/' + this.id_parent,
				method: 'post',
				data: 'order=' + serie,
				onSuccess: function(responseJSON)
				{
					MUI.hideSpinner();

					ION.notification(responseJSON.message_type, responseJSON.message);
				}
			}).post();
		}
	},


	/**
	 * On request fail
	 *
	 * @param xhr
	 */
	failure: function(xhr)
	{
		ION.notification('error', xhr.responseText );

		// Hide the spinner
		MUI.hideSpinner();
	},


	/**
	 * Unlink one media from his parent
	 *
	 * @param id    Media ID
	 *
	 */
	detachMedia: function(id)
	{
		// Show the spinner
		MUI.showSpinner();

		new Request.JSON(
		{
			url: this.adminUrl + 'media/detach_media/' + this.parent + '/' + this.id_parent + '/' + id,
			method: 'post',
			onSuccess: function()
			{
				this.loadList();
			}.bind(this),
			onFailure: this.failure.bind(this)
		}).send();
	},


	detachAllMedia: function()
	{
		var self = this;

		new Request.JSON(
		{
			url: this.adminUrl + 'media/detach_all_media/' + this.parent + '/' + this.id_parent,
			method: 'post',
			onSuccess: function()
			{
				self.loadList();
			}.bind(this),
			onFailure: this.failure.bind(this)
		}).send();
	},


	/**
	 * Init thumbnails for one picture
	 * to be called on pictures list
	 *
	 * @param id_picture
	 */
	initThumbs:function(id_picture) 
	{
		// Show the spinner
		MUI.showSpinner();

		var myAjax = new Request.JSON(
		{
			url: this.adminUrl + 'media/init_thumbs/' + id_picture,
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				ION.notification(responseJSON.message_type, responseJSON.message );
				
				if (responseJSON.message_type == 'success')
				{
					this.loadList();
				}
			}.bind(this)
		}).send();
	},

	updateNumber: function(nb)
	{
		var tab = $(this.tab);

		if (typeOf(tab) != 'null')
		{
			var td = tab.getElement('.tab-detail');
			if (td) td.destroy();

			if (nb > 0)
				tab.adopt(new Element('span', {'class':'tab-detail'}).set('html',nb));
		}
	},


	/**
	 * Opens fileManager
	 *
	 * @param	{Object}	options
	 */
	toggleFileManager:function()
	{
		// If no parent exists : don't show the filemanager but an error message
		if (! this.id_parent || this.id_parent == '')
		{
			ION.notification('error', Lang.get('ionize_message_please_save_first'));
		}
		else
		{
			// Exit if another fileManager is already running
			var elFilemanagerWindow = $('filemanagerWindow');
			if (elFilemanagerWindow)
			{
				var inst = elFilemanagerWindow.retrieve('instance');

				// Re-open window if minimized or shake if triing to open another FM
				if (inst.isMinimized)
				{
					inst.restore();
				}
				else
				{
					inst.focus();
					elFilemanagerWindow.shake();
				}

				return;
			}

			// Referer to ionizeMediaManager
			var self = this;

			// Correct windows levels : Get the current highest level.
			MUI.Windows._getWithHighestZIndex();							// stores the highest level in MUI.highestZindex
			var zidx = (MUI.Windows.highestZindex).toInt();

			MUI.Windows.indexLevel = zidx + 100;						// Mocha window z-index

			this.filemanager = new Filemanager({
				url: admin_url + 'media/filemanager',
				assetsUrl: theme_url + 'javascript/filemanager/assets',
				standalone: false,
				createFolders: true,
				destroy: ION.Authority.can('delete', 'admin/filemanager'),
				rename: ION.Authority.can('rename', 'admin/filemanager'),
				upload: ION.Authority.can('upload', 'admin/filemanager'),
				move_or_copy: ION.Authority.can('move', 'admin/filemanager'),
				resizeOnUpload: self.options.resizeOnUpload,
				uploadAutostart: self.options.uploadAutostart,
				uploadMode: self.options.uploadMode,
				language: Lang.current,
				selectable: true,
				hideOnSelect: false,
				'onComplete': self.addMedia.bind(self),
				parentContainer: 'filemanagerWindow_contentWrapper',
				mkServerRequestURL: function(fm_obj, request_code, post_data)
				{
					return {
						url: fm_obj.options.url + '/' + request_code,
						data: post_data
					};
				}
			});

			// MUI Window creation
			var winOptions = ION.getFilemanagerWindowOptions();
			winOptions.content = this.filemanager.show();
			winOptions.onResizeOnDrag = function(){
				self.filemanager.fitSizes();
			};

			self.window = new MUI.Window(winOptions);
			self.window.filemanager = this.filemanager;

			this.setFilemanagerTargetInfo();
		}
	}
});

