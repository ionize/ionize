/**
 * Ionize Uploader Class
 * @type {Class}
 *
 * requires: [DropZone, Core/Class, Core/Object, Core/Element.Event, Core/Fx.Elements, Core/Fx.Tween]
 *
 * provides: [ION.Uploader]
 *
 * authors:
 * - Michel-Ange Kuntz
 *
 *
 * options {
 * 		url:		URL of the upload controller
 * 		method:		Upload method. Can be 'html4', 'html5', 'auto'
 * 		autostart:	Autstart the upload after file selection
 * 		dropare:	Drop area into which drag and drop files
 *		assetsUrl:	URL of the type assets
 * 		onReset: 	function. Executed when the dropZone object resets (at init)
 * 		post:		Array of keys:values to post with the uploaded item
 * 					[
 * 						{key: value},
 * 						{key2: value2},
 * 					]
 * }
 *
 * Usage :
 *
 * this.uploader = new ION.Uploader({
 *      'container' : id,
 *		'notifyContainer' : id,
 *		'url' : string,
 *		'method': ['auto', 'html5', 'html4'],
 *		'autostart': [true, false],
 *		'droparea': [null, id],
 *		onItemComplete: function(item, file, response)
 *		{
 *	        ...
 *		}
 *	});
 *
 *  // Optional vars
 *  Object.each(this.options.uploadPost, function(val, key)
 *  {
 *	    self.uploader.dropZone.setVar(key, val);
 *  })
 *
 */
var ION = (ION || {});

ION.Uploader = new Class({

	Implements: [Options, Events],

	options: {
		url: null,
		method: 'auto',				// 'auto', 'html5', 'html4'
		autostart: false,
		previewListMode: 'auto',    // 'auto', list', 'card'
		droparea: null,

		uploadPost: null			// Object of {key:value}, added as POST data

		// Events
		/*
		 onItemComplete: function (item, file, response) {},
		 onComplete: function(){}
		 */
	},

	// Vars
	container: null,

	initialize: function(options)
	{
		var self = this;

		if ( ! DropZone)
		{
			console.log('Uploader : DropZone class missing. Cannot initialize');
			return;
		}
		if (typeOf(options.url) == 'null')
		{
			console.log('Uploader : DropZone upload URL missing. Cannot initialize');
			return;
		}

		// Options
		this.setOptions(options);

		if (this.options.method == 'auto') this.options.method = '';

		// Assets URL
		var scripts = $$('script');
		scripts.each(function(script)
		{
			var url = script.src.split('/'),
				name = url.pop();

			if (name == 'ionize_uploader.js')
			{
				url.pop();
				url = url.join('/');
				self.assetsUrl = url + '/filemanager/assets'
			}
		});

		// Assets URL (bis)
		this.assetsUrl = this.assetsUrl.replace(/(\/|\\)*$/, '/');

		// Container
		if (typeOf(this.options.container) != 'null')
			this.container = $(this.options.container);

		if (typeOf(this.options.notifyContainer) != 'null')
			this.notifyContainer = $(this.options.notifyContainer);
		else
			this.notifyContainer = this.container;

		// Init DOM elements & DropZone
		this.initDomElements();
		this.initDropZone();

		return this;
	},

	initDomElements: function()
	{
		// Select Button
		this.uploadSelectButton = new Element('label', {
			'class': 'button left light',
			'text': Lang.get('ionize_label_select_files_to_upload')
		}).inject(this.container);

		new Element('i', {'class':'icon-upload'}).inject(this.uploadSelectButton, 'top');

		// Start Upload Button
		this.startUploadButton = false;
		if (this.options.autostart != true)
		{
			this.startUploadButton = new Element('a', {
				'class': 'button left green ml5',
				'text': Lang.get('ionize_button_start_upload')
			}).inject(this.container);
		}

		// Upload Zone
		this.uploadZone = new Element('div', {'class': 'uploader'}).inject(this.container, 'top');
		this.uploadZoneList = new Element('div', {'class': 'list'}).inject(this.uploadZone);

		// DropArea
		if( typeOf(this.options.droparea ) == 'null')
			this.uiDropArea = document.body
		else
			this.uiDropArea = $(this.options.droparea);

	},

	initDropZone: function()
	{
		var self = this;

		this.dropZone = new DropZone(
		{
			method: this.options.method,
			autostart: this.options.autostart,
			ui_button: this.uploadSelectButton,
			ui_list: this.uploadZoneList,
			ui_drop_area: this.uiDropArea,
			ui_upload_button: this.startUploadButton,
			url: this.options.url,
			lang:{
				start_upload: Lang.get('ionize_button_start_upload'),
				select_files: Lang.get('ionize_label_select_files_to_upload')
			},

			onReset:function(method)
			{
				this.url = self.options.url;

				self.fireEvent('reset', [this.method]);

				// Useful to send the uploaded file directory
				// Overwritten by the controller if more security is needed
				// this.setVar('directory', '_test/toto');
			},

			onItemAdded: function(item, file, imagedata)
			{
				var img_loaded = false;

				item.addClass('list-item').adopt(
					new Element('a', {'class': 'icon delete', 'style':'z-index:2'}).addEvent('click', function(e)
					{
						e.stop();
						self.dropZone.cancel(file.id, item);
					}),
					new Element('div', {'class': 'progress'}).adopt(
						new Element('div', {'class': 'progress-bar'})
					)
				);

				if(self.options.previewListMode != 'list' && file.type && file.type.match('image') && imagedata)
				{
					item.addClass('image');

					var img = new Element('img', {'src': imagedata}).inject(item, 'top').setStyle('opacity', .7);
					var dim = img.getSize();
					if (dim.y < dim.x)
						img.setStyles({'height':'100px', 'width':'auto'});
					else
						img.setStyles({'height':'auto', 'width':'100px'});

					img_loaded = true;
				}
				else
				{

					// Firefox does no provide the full path to the file
					if ( self.options.previewListMode != 'list' && ! Browser.firefox && ['image','jpg','jpeg','png','gif','bmp'].contains((file.type).toLowerCase()))
					{
						var img = Asset.image(file.path,
						{
							onLoad: function()
							{
								var display_image = true;

								if (Browser.name=='ie' && this.fileSize > 10000000)
								{
									display_image = false;
									this.destroy();
								}

								if (display_image)
								{
									var domImg = new Element('img', {'src': 'file://' + file.path});
									domImg.setStyles({'opacity':0}).inject(document.body);
									var dim = domImg.getSize();

									if (dim.y < dim.x)
										domImg.setStyles({'height':'100px', 'width':'auto'});
									else
										domImg.setStyles({'height':'auto', 'width':'100px'});

									domImg.inject(item, 'top').setStyles({'opacity':.7});

									// item.getElement('p').destroy();
									item.setStyles({'background-image': 'none'});

									img_loaded = true;
								}
							},
							onError: function()	{
								img_loaded = false;
							}
						});
					}

					// Image could not be loaded, or until the image is loaded
					if (img_loaded == false)
					{
						var ext = (file.name).split('.').pop();
						item.setStyles({
							'background-image': 'url(' + self.assetsUrl + 'images/icons/' + ext + '.png' + ')'
						});
					}
				}

				// Not an image
				var text = file.name;
				/*
				 if (text.length > 15)
				 {
				 text = text.slice(0,4) + ' … ' + text.slice(-11);
				 }
				 */
				var p = new Element('p').inject(item, 'top');
				p.adopt(new Element('span', {'text': text}));

				if (item.retrieve('progress'))
					this.fireEvent('itemProgress', [item, item.retrieve('progress')]);
			},

			// TODO
			onItemProgress: function(item, perc)
			{
				if( ! item.getElement('.progress')) return;

				item.getElement('.progress').fade('show');

				var parentSize = item.getElement('.progress').getDimensions();

				// var dim = 80;
				item.getElement('.progress-bar').tween('width', Math.round(parentSize.x * perc/100));
				item.getElement('.progress-bar').innerHTML = Math.floor(perc) + '%';
			},

			onItemComplete: function(item, file, response)
			{
				// Fires ION.Uploader event !
				self.fireEvent('itemComplete', [item, file, response]);

				// Get out if no progress bar
				if( ! item.getElement('.progress')) return;

				item.getElement('.progress').fade('show');
				// Dim cannot be calculated on large file : upload starts before the DOM element is rendered.
				// var dim = 80;
				var parentSize = item.getElement('.progress').getDimensions();
				item.getElement('.progress-bar').tween('width', parentSize.x);
				item.getElement('.progress-bar').innerHTML = '100 %';

				// var img = item.getElement('img');
				// if (img) item.getElement('img').setStyle('opacity', 1);

				// Remove item from DOM
				item.fade(0).get('tween').chain(function() {
					this.element.destroy();
				});
			},

			onItemCancel: function(item)
			{
				// Remove item from DOM
				item.fade(0).get('tween').chain(function() {
					this.element.destroy();
				});
			},

			onItemError: function(item, file, response)
			{
				if (typeOf(ION.Notify) != 'null')
				{
					new ION.Notify(
						self.notifyContainer,
						{type:'error'}
					).show(response.orig_name + ' : ' + response.error);
				}

				// Remove item from DOM
				item.fade(0).get('tween').chain(function() {
					this.element.destroy();
				});
			},

			onUploadStart: function()
			{
				// Remove all Notify
/*
				if (typeOf(ION.Notify) != 'null')
					new ION.Notify(self.notifyContainer,{}).removeAll();
*/
			},

			onUploadProgress: function(perc, nb_uploaded_so_far){},

			onUploadComplete: function(num_uploaded, num_error)
			{
				// Fires ION.Uploader event !
				self.fireEvent('onComplete');

				var elements = self.uploadZoneList.getChildren('.dropzone_item');

				Array.each(elements, function(item){
					var parentSize = item.getDimensions();
					self.uploadZoneList.getChildren('.progress-bar').tween('width', parentSize.x);
					self.uploadZoneList.getChildren('.progress-bar').innerHTML = '100 %';

					item.fade(0).get('tween').chain(function() {
						this.element.destroy();
					});
				});
			}
		});

		// Add optional POST vars to DropZone
		if (typeOf(this.options.uploadPost) != 'null')
		{
			Object.each(this.options.uploadPost, function(val, key)
			{
				self.dropZone.setVar(key, val);
			});
		}
	}
});
