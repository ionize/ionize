/**
 * Ionize Extend Media Manager
 *
 */

ION.ExtendMediaManager = new Class({

	Implements: [Events, Options],

	options: {
		thumbSize:		    120,
		resizeOnUpload:     false,
		uploadAutostart:    false,
		uploadMode:         '',
		standalone:         false
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

		this.addMediaUrl =  this.adminUrl + 'media/add_media_to_extend';

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

		this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		if (options.tab) this.tab = options.tab;
		if (options.extend_label) this.extend_label = options.extend_label;

		this.buildContainer();

		if (this.filemanager) this.setFilemanagerTargetInfo();
	},

	buildContainer: function()
	{
		var self = this;

		// Button bar
		var p = new Element('p', {'class':'h30'}).inject(this.container);

		// Create Button
		this.btnAddMedia = new Element('a', {
			'class':'button light',
			text: Lang.get('ionize_label_add_media')
		}).adopt(new Element('i', {'class':'icon-pictures'})).inject(p);

		this.btnAddMedia.addEvent('click', function()
		{
			self.open();
		});

		// Media List Container
		this.mediaContainer = new Element('div', {
			'class':''
		}).inject(this.container);


		// Add video URL Button
		/*
		 * @todo :
		 * 1. Build the window
		 * 2. Call extendMediaManager.addMedia()
		 *      with add of the type, which is "external"
		 * 3. Modify the controller Media->add_media_to_extend()
		 *      to check for type='external' and do the insert
		 */
		/*
		 var addVideo = new Element('a', {
		 'class':'button light',
		 'data-id': field.id_extend_field,
		 text: Lang.get('ionize_label_add_video')
		 }).adopt(new Element('i', {'class':'icon-video'})).inject(p);

		 addVideo.addEvent('click', function()
		 {
		 ION.dataWindow(
		 'addExternalMedia',
		 'ionize_label_add_video',
		 'media/add_external_media_window',
		 {width:600, height:150},
		 {
		 parent: self.parent,
		 id_parent: self.id_parent
		 }
		 )
		 });
		 */

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

	getExistingInstance: function()
	{
		var self = this;

		if ($('filemanagerWindow'))
		{
			// Window
			var inst = $('filemanagerWindow').retrieve('instance');

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


	/**
	 * Adds Target info to the Filemanager window
	 */
	setFilemanagerTargetInfo: function()
	{
		if (this.filemanager)
		{
			var text = Lang.get('ionize_label_filemanager_target') + ' : ' + this.parent + ' ' + this.id_parent;

			if (this.id_extend != null)
				text = text + ' - Extend : ' + this.extend_label;

			if (this.lang != null)
				text = text + ' - Lang : ' + this.lang;

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
			id_extend: this.id_extend,
			lang: this.lang
		};

		// Extend Field
		new Request.JSON(
		{
			'url': this.addMediaUrl,
			'method': 'post',
			'data': data,
			'onSuccess': this.successAddMedia.bind(this),
			'onFailure': this.failure.bind(this)
		}).send();
	},

	/**
	 * called after 'addMedia()' success
	 * calls 'loadList'
	 *
	 * @param responseJSON
	 */
	successAddMedia: function(responseJSON)
	{
		ION.notification(responseJSON.message_type, responseJSON.message);

		this.loadList();
	},

	/**
	 * Loads a media list through XHR regarding its type
	 * called after a media list loading through 'loadList'
	 *
	 * @param options  Object {
	 *                     parent:
	 *                     id_parent:
	 *                     id_extend:
	 *                     lang:
	 *                 }
	 */
	loadList: function()
	{
		var self = this;

		new Request.JSON(
		{
			url : this.adminUrl + 'media/get_extend_media_list',
			data: this.getOptions(),
			'method': 'post',
			'onFailure': this.failure.bind(this),
			'onComplete': function(responseJSON)
			{
				self.completeLoadList(responseJSON);
			}
		}).send();
	},

	/**
	 * Initiliazes the media list regarding to its type
	 * called after a media list loading through 'loadList'
	 *
	 * @param responseJSON  JSON response object.
	 *                      responseJSON.type : media type. Can be 'picture', 'video', 'music', 'file'
	 */
	completeLoadList: function(responseJSON)
	{
		var self = this;

		// Hides the spinner
		MUI.hideSpinner();

		this.mediaContainer.empty();

		if (responseJSON && responseJSON.content)
		{
			// Feed the mediaContainer with responseJSON content
			this.mediaContainer.set('html', responseJSON.content);

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

						self.sortItemList(responseJSON.type, serialized);
					}
				}
			);

			// Store the first ordering after picture list load
			this.mediaContainer.store('sortableOrder', sortableMedia.serialize(0, function(element)
			{
				return element.getProperty('data-id');
			}));

			// Events on items
			var medias = this.mediaContainer.getElements('div.drag');

			medias.each(function(media)
			{
				// Set it to init the values
				var parent = self.parent,
					id_parent = self.id_parent,
					id_extend = self.id_extend,
					lang = self.lang
				;

				// Unlink
				var unlink = media.getElement('a.unlink');
				if (unlink)
				{
					unlink.addEvent('click', function()
					{
						self.detachMedia(this.getProperty('data-id'), parent, id_parent, id_extend, lang);
					});
				}

				// Edit
				var edit = media.getElement('a.edit');
				if (edit)
				{
					edit.addEvent('click', function()
					{
						var id = this.getProperty('data-id');
						ION.formWindow(
							'media' + id,
							'mediaForm' + id,
							this.getProperty('data-title'),
							ION.adminUrl + 'media/edit/' + id,
							{width:520,height:430,resize:false}
						);
					});
				}

				// Refresh thumb
				var refresh = media.getElement('a.refresh');
				if (refresh)
				{
					refresh.addEvent('click', function()
					{
						self.initThumbs(this.getProperty('data-id'));
					});
				}
			});
		}
		// Update the tab'info (number of media)
		// if (tab) ION.updateTabNumber(tab, this.container);
	},

	/**
	 * Items list ordering
	 * called on items sorting complete
	 * calls the XHR server ordering method
	 *
	 * @param type          Media type. Can be 'picture', 'video', 'music', 'file'
	 * @param serialized    new order as a string. coma separated
	 */
	sortItemList: function(type, serialized)
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
			serie = serie.join(',');

			var data = {
				parent: this.parent,
				id_parent: this.id_parent,
				id_extend: this.id_extend,
				lang: this.lang,
				order: serie
			};

			// Save the new ordering
			new Request.JSON(
			{
				url: this.adminUrl + 'media/save_extend_ordering',
				method: 'post',
				data: data,
				onSuccess: function(responseJSON)
				{
					MUI.hideSpinner();

					ION.notification(responseJSON.message_type, responseJSON.message);
				}
			}).post();
		}
	},

	/*
	 * Keep for future release
	 *
	getTab: function()
	{
		var selector = '.' + this.tab + '[data-id=' + this.id_extend + ']';

		if(this.lang != null)
			selector = selector + '[data-lang=' + this.lang + ']';

		var tab = $$(selector);

		if (tab.length > 0)
			return tab[0];

		return null;
	},
	*/

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
	 * @param type  Media type
	 * @param id    Media ID
	 */
	detachMedia: function(id_media, parent, id_parent, id_extend, lang)
	{
		MUI.showSpinner();

		var data = {
			id_media: id_media,
			parent: parent,
			id_parent: id_parent,
			id_extend: id_extend,
			lang: lang
		};

		new Request.JSON(
		{
			url: this.adminUrl + 'media/detach_extend_media',
			method: 'post',
			data: data,
			onSuccess: function()
			{
				this.loadList();
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
		MUI.showSpinner();

		new Request.JSON(
		{
			url: this.adminUrl + 'media/init_thumbs/' + id_picture,
			method: 'post',
			onSuccess: function(responseJSON)
			{
				ION.notification(responseJSON.message_type, responseJSON.message );

				if (responseJSON.message_type == 'success')
				{
					this.loadList();
				}
			}.bind(this)
		}).send();
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
			if (this.getExistingInstance()) return;

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
	}
});

