/** MediaManager
 *	Opens the choosen media / file manager and get the transmitted file name
 *
 *	Options :
 *
 *		baseUrl:			URL to the website
 *		parent:				type of the parent. 'article', 'page', etc. Used to update the database table.                 
 *		idParent:			ID of the parent element      
 *		button:				DOM opener button name
 */

var IonizeMediaManager = new Class(
{
	Implements: Options,

    options: {
		parent:			false,
		idParent:		false,
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
		
		this.baseUrl =		this.options.baseUrl;

		this.adminUrl =		this.options.adminUrl;
		
		this.themeUrl =		theme_url;

		this.standalone =   options.standalone;
		this.idParent =		options.idParent;
		this.parent =		options.parent;
		this.filemanager =  null;

		this.container = $(options.container);

		// Filemanager opening buttons
		var self = this;
		$$(options.fileButton).each(function(item)
		{
			item.addEvent('click', function(e)
			{
				e.stop();
				self.toggleFileManager();
			});
		});
		
		// Check if a fileManager is already open. If yes, change the callback ref.
		// Needed in case of page / article change with the filemanager open
/*
		if ($('filemanagerWindow'))
		{
			this.filemanager = $('filemanagerWindow').retrieve('filemanager');
			this.initParentTarget();
		}
*/
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
	 *
	 * @param parent
	 * @param id_parent
	 */
	initParent: function(parent, id_parent)
	{
		this.parent = parent;
		this.idParent = id_parent;

		if (this.filemanager) this.setFilemanagerTargetInfo();
	},


	/**
	 * Adds Target info to the Filemanager window
	 */
	setFilemanagerTargetInfo: function()
	{
		var text = Lang.get('ionize_label_filemanager_target') + ' : ' + this.parent + ' ' + this.idParent;

		this.filemanager.setTargetInfo(text);
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
			id_parent: this.idParent,
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
	 * calls 'loadMediaList' with the correct media type returned by the XHR call
	 *
	 * @param responseJSON
	 */
	successAddMedia: function(responseJSON)
	{
		ION.notification(responseJSON.message_type, responseJSON.message);

		// Media list reload
		this.loadMediaList();
	},


	/**
	 * Loads a media list through XHR regarding its type
	 * called after a media list loading through 'loadMediaList'
	 *
	 */
	loadMediaList: function()
	{
		// Only loaded if a parent exists
		if (this.idParent)
		{
			new Request.JSON(
			{
				url : this.adminUrl + 'media/get_media_list',
				method: 'post',
				data: {
					parent: this.parent,
					id_parent: this.idParent
				},
				'onFailure': this.failure.bind(this),
				'onComplete': this.completeLoadMediaList.bind(this)
			}).send();
		}
	},

	
	/**
	 * Initiliazes the media list regarding to its type
	 * called after a media list loading through 'loadMediaList'
	 *
	 * @param responseJSON  JSON response object.
	 *
	 */
	completeLoadMediaList: function(responseJSON)
	{
		// Hides the spinner
		MUI.hideSpinner();

		this.container = $(this.options.container);
		this.container.empty();

		if (responseJSON && responseJSON.content)
		{
			// Feed the container with responseJSON content
			this.container.set('html', responseJSON.content);

			var self = this;

			// Init the sortable
			var sortableMedia = new Sortables(this.container, {
				revert: true,
				handle: '.drag',
				clone: true,
				// constrain: true,
				// container: container,
				opacity: 0.5,
				onComplete: function()
				{
					var serialized = this.serialize(0, function(element)
					{
						// Get the ID list by replacing 'type_' by '' for each item
						// Example : Each picture item is named 'picture_ID' where 'ID' is the media ID
						if (element.id != '')
						{
							return element.getProperty('data-id');
						}
					});
					// Items sorting
					self.sortItemList(serialized);
				}
			});

			// Store the first ordering after picture list load
			this.container.store('sortableOrder', sortableMedia.serialize(0, function (element)
			{
				return element.getProperty('data-id');
			}));

			// Edit icon
			var items = this.container.getElements('div.media');

			items.each(function(item)
			{
				var edit_icon = item.getElement('.edit');
				var id = item.getProperty('data-id');
				var filename = item.getProperty('data-filename');

				if (edit_icon)
				{
					edit_icon.addEvent('click', function()
					{
						ION.formWindow(
							'media' + id,
							'mediaForm' + id,
							filename,           // Window title
							'media/edit/' + id,
							{width:600,height:430,resize:false}
						);
					});
				}
			});
		}

		// Add the media number to the tab
		ION.updateTabNumber('mediaTab', this.container.getProperty('id'));
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
		var sortableOrder = this.container.retrieve('sortableOrder');

		// Remove "undefined" from serialized. Undefined comes from the clone, which isn't removed before serialize.
		var serie = new Array();
		serialized.each(function(item)
		{
			if (typeOf(item) != 'null')
				serie.push(item);
		});

		// If current <> new ordering : Save it ! 
		if (sortableOrder.toString() != serie.toString() ) 
		{
			// Store the new ordering
			this.container.store('sortableOrder', serie);

			// Save the new ordering
			var myAjax = new Request.JSON(
			{
				url: this.adminUrl + 'media/save_ordering/' + this.parent + '/' + this.idParent,
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
			url: this.adminUrl + 'media/detach_media/' + this.parent + '/' + this.idParent + '/' + id,
			method: 'post',
			onSuccess: function()
			{
				this.loadMediaList();
			}.bind(this),
			onFailure: this.failure.bind(this)
		}).send();
	},

	detachAllMedia: function()
	{
		new Request.JSON(
		{
			url: this.adminUrl + 'media/detach_all_media/' + this.parent + '/' + this.idParent,
			method: 'post',
			onSuccess: function()
			{
				this.loadMediaList();
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
					this.loadMediaList();
				}
			}.bind(this)
		}).send();
	},



	/**
	 * Opens fileManager
	 *
	 * @param options
	 */
	toggleFileManager:function()
	{
		// If no parent exists : don't show the filemanager but an error message
		if (! this.idParent || this.idParent == '')
		{
			ION.notification('error', Lang.get('ionize_message_please_save_first'));
		}
		else
		{
			// Exit if another fileManager is already running
			if ($('filemanagerWindow'))
			{
				var inst = $('filemanagerWindow').retrieve('instance');

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

