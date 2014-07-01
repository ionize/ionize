/*
---

name: DropZone

description: Crossbrowser file uploader with HTML5 chunk upload support, flexible UI and nice modability. Uploads are based on Mooupload by Juan Lago

version: 0.9.1

license: MIT-style license

authors:
  - Mateusz Cyrankiewicz
  - Juan Lago
  - Michel-Ange Kuntz


requires: [Core/Class, Core/Object, Core/Element.Event, Core/Fx.Elements, Core/Fx.Tween]

provides: [DropZone]

...
*/

var DropZone = new Class({

	Implements: [Options, Events],

	options:
	{
		// UI Elements
		/*
		The class accomodates to use what's available:
		- eg. if ui_list is defined, uploaded items will be output into it, otherwise this functionality will be disabled
		- ui_button OR ui_drop_area is required to select files
		- drop area only works in HTML5 mode
		- drop area and ui_list can be the same element
		- drop area and ui_button can be the same element
		*/
		ui_button: null,
		ui_list: null,
		ui_drop_area: null,

		// Translated terms
		/*
		lang:{
			start_upload: Lang.get('ionize_button_start_upload'),
			select_files: Lang.get('ionize_label_select_files_to_upload')
		},
		*/

		// Form & File input prefix
		ui_form_prefix: 'dropzone_form_',
		ui_file_input_prefix: 'file_',

		// Settings
		url: null,
		accept: '*/*',
		method: null, // for debugging, values: 'HTML5', 'HTML4', 'Flash' or null for automatic selection
		multiple: true,
		autostart: true,
		max_queue: 5,
		min_file_size: 1,
		max_file_size: 0,
		block_size: 502000, // Juan doesn't recommend less than 101400 and more than 502000

		// additional to be sent to backend
		vars: {},
		gravity_center: null // an element after which hidden DropZone elements are output

		// Events
		/*
			onReset: function (method) {},
			onSelectError: function (error, filename, filesize) {},
			onAddFiles: function () {},
			onUploadStart: function (){}, // start of queue
			onUploadComplete: function (num_uploaded){}, // end of queue
			onUploadProgress: function (perc) {}, // on progress of queue
			onItemAdded: function (element, file, imageData) {}, // listener should add HTML for the item (get params like file.name, file.size), imageData is sent only for images
			onItemCancel: function (element, file) {},
			onItemComplete: function (item, file, response) {},
			onItemError: function (item, file, response) {},
			onItemProgress: function (item, perc) {}
		*/
	},
	
	// Vars
    method: null,
	flashObj: null,
	flashloaded: false,
	uiButton: null,
	uiList: null,
	uiDropArea: null,
	hiddenContainer: null,
	ui_upload_button: null,

	// Init
	initialize: function (options)
	{
		/*
		* Check what's available
		* and initiate based on that
		* note: swap bits here to make Flash preferred to HTML5
		*/
		if (options.method != '' && typeof options.method != 'undefined')
			this.method = (options.method).toUpperCase();

		// Lang keys set here
		this.options.lang = {
			start_upload: Lang.get('ionize_button_start_upload'),
			select_files: Lang.get('ionize_label_select_files_to_upload')
		};


		// Check HTML5 support & if module is available
		if ( ! this.method && window.File && window.FileList && window.Blob && typeof DropZone['HTML5'] != 'undefined')
		{
			this.method = 'HTML5';

			// Unfortunally Opera 11.11 has an incomplete Blob support
			if (Browser.opera && Browser.version <= 11.11) this.method = null;
		}

		// Check flash support & if module is available
		if ( ! this.method && typeof DropZone['Flash'] != 'undefined') this.method = Browser.Plugins.Flash && Browser.Plugins.Flash.version >= 9 ? 'Flash' : null;
		
		// If not Flash or HTML5, go for HTML4 if module is available
		if ( ! this.method && typeof DropZone['HTML4'] != 'undefined') this.method = 'HTML4';

		// Activate proper method (self-extend)
		if(typeof DropZone[this.method] != 'undefined')
		{
			return new DropZone[this.method](options);
		}
	},
	
	activate: function()
	{
		// set UI elements
		this.uiButton = $(this.options.ui_button);
		this.uiList = $(this.options.ui_list);
		this.uiDropArea = $(this.options.ui_drop_area);

		this.uiListUploadButton = $(this.options.ui_upload_button);
		
		// just any of elements, to keep injected invisible elements next to
		this.gravityCenter = this.options.gravity_center;
		if(!this.gravityCenter) this.gravityCenter = this.uiButton || this.uiList || this.uiDropArea;
		if(!this.gravityCenter) return;
		
		// container for invisible things
		this.hiddenContainer = new Element('div', {'class': 'dropzone_hidden_wrap'}).inject(this.gravityCenter, 'after');
		
		// setup things fresh
		this.reset();
	},


	/**
	 * Sets one optional var
	 * This var will be sent by POST (HTML4 mode)
	 * and through HTTP headers (HTML5 mode)
	 * @param key
	 * @param value
	 */
	setVar:function(key, value)
	{
		this.options.vars[key] = value;
	},


	/**
	 * Unsets one optional var
	 * @param key
	 */
	unsetVar:function(key)
	{
		delete this.options.vars[key];
	},


	/**
	 * Adds files before upload
	 * @param files
	 * @return {Boolean}
	 */
	addFiles: function(files)
	{
		// Clone the vars options object, to avoid byref value
		var vars = Object.clone(this.options.vars);

		// Add Files to fileList, Call _addNewItem()
		for (var i = 0, f; f = files[i]; i++)
		{
			var fname = f.name || f.fileName;
			var fsize = f.size || f.fileSize;

			if (fsize != undefined)
			{
				if (fsize < this.options.min_file_size) {
					this.fireEvent('onSelectError', ['minfilesize', fname, fsize]);
					return false;
				}

				if (this.options.max_file_size > 0 && fsize > this.options.max_file_size) {
					this.fireEvent('onSelectError', ['maxfilesize', fname, fsize]);
					return false;
				}
			}
			
			var id = this.fileList.length;

			this.fileList[id] = {
				file: f,
				id: id,
				uniqueid: String.uniqueID(),
				checked: true,
				name: fname,
				path: f.path || fname,
				type: (f.type || f.extension || this._getFileExtension(fname)).toLowerCase(),
				size: fsize,
				uploaded: false,
				uploading: false,
				progress: 0,
				error: false,
				vars: vars
			};
			
			if (this.uiList) this._addNewItem(this.fileList[this.fileList.length - 1]);
		}

		// fire!
		this.fireEvent('onAddFiles', [this.fileList.length]);

		if (this.options.autostart) this.upload();
	},


	/**
	 * Starts Upload
	 */
	upload: function()
	{
		if( ! this.isUploading)
		{
			this.isUploading = true;
			this.fireEvent('onUploadStart');
			
			this._updateQueueProgress();
		}
	},
	

	/**
	 * Cancels a specified item
	 * @param id
	 * @param item
	 */
	cancel: function(id, item)
	{
		if(this.fileList[id]){
			
			this.fileList[id].checked = false;
			this.fileList[id].cancelled = true;
			
			if(this.fileList[id].error) {
				this.nErrors--;
			} else if(this.fileList[id].uploading) {
				this.nCurrentUploads--;
			}
		}
		
		this.nCancelled++;
		
		// if(this.nCurrentUploads <= 0 ) this._queueComplete();
		
		this.fireEvent('onItemCancel', [item]);
	},
	

	/**
	 * kill at will
	 */
	kill: function()
	{
		// cancel all
		this.fileList.each(function(f, i){
			
			this.cancel(f.id);
		}, this);
	},


	/**
	 *
	 */
	reset: function()
	{
		// Add vars to URL (query string)
		// this.url = this.options.url + ((!this.options.url.match('\\?')) ? '?' : '&') + Object.toQueryString(this.options.vars);

		// Nop, var must be added to POST data to allow controllers which does not permit query string data
		// to process upload
		this.url = this.options.url;
		this.fileList = new Array();
		this.lastInput = undefined; // stores new, currently unused hidden input field

		this.nCurrentUploads = 0;
		this.nUploaded = 0;
		this.nErrors = 0;
		this.nCancelled = 0;

		this.queuePercent = 0;
		this.isUploading = false;
		this.setVar('file_input_prefix', this.options.ui_file_input_prefix);

		// Not done here : HTML4 mode adds one input after each file select
		this._newInput();

		this.fireEvent('reset', [this.method]);
	},


	/* Private methods */
		
	/**
	 * Activate button used by HTML4 & HTML5 uploads
	 *
	 */
	_activateHTMLButton: function()
	{
		if( ! this.uiButton) return;

		this.uiButton.removeClass('disabled');

		this.uiButton.addEvent('click', function (e)
		{
			e.stop();

			// Click trigger for input[type=file] only works in FF 4.x, IE and Chrome
			if( this.options.multiple || ( ! this.options.multiple && ! this.isUploading)) this.lastInput.click();

		}.bind(this));
	},


	/**
	 * Creates hidden input elements to handle file uploads nicely
	 * @param formcontainer
	 * @private
	 */
	_newInput: function(formcontainer)
	{
		if(!formcontainer) formcontainer = this.hiddenContainer;
		
		// Input File
		this.lastInput = new Element('input',
		{
			id: this.options.ui_file_input_prefix + this._countInputs(),
			name: this.options.ui_file_input_prefix + this._countInputs(),
			type: 'file',
			size: 1,
			styles: {
				position: 'absolute',
				top: 0,
				left: 0
			},
			multiple: this.options.multiple,
			accept: this.options.accept
		}).inject(formcontainer);


		// Old version of firefox and opera don't support click trigger for input files fields
		// Internet "Exploiter" do not allow trigger a form submit if the input file field was not clicked directly by the user
		if (this.method != 'Flash' && (Browser.name=='firefox2' || Browser.name=='firefox3' || Browser.name=='opera' || Browser.name=='ie')) {
			this._positionInput();
		} else {
			this.lastInput.setStyle('visibility', 'hidden');
		}

		return this.lastInput;
	},

	/**
	 *
	 * @private
	 */
	_positionInput: function()
	{
		// if(!this.uiButton && true) return;
		
		// Get addFile attributes
		// var btn = this.uiButton,
		//	btncoords = btn.getCoordinates(btn.getOffsetParent());

		// Only solution for IE9, so it trigger correctly the file input click
		// Tried :
		// - Inject of the existing label before the file input : failed :
		//      this.uiButton.setProperty('for', this.lastInput.id);
		//      this.uiButton.inject(this.lastInput, 'after');

		if (this.uiButton)
		{
			this.uiButton.dispose();
			this.uiButton = null;
		}

		// Must be a label
		// See : http://jsfiddle.net/djibouti33/uP7A9/
		// http://stackoverflow.com/questions/10667856/form-submit-ie-access-denied-same-domain
		var label = new Element('label',
		{
			'text': this.options.lang.select_files,
			'for': this.lastInput.id,
			'class':'left button'
		}).inject(this.lastInput, 'before');

		new Element('i', {'class':'icon-upload'}).inject(label, 'top');

		this.lastInput.setStyles({
			position: 'absolute',
			left: '-9999em',
			opacity: 0.0001,
			'-moz-opacity': 0
		});
	},


	/**
	 *
	 * @private
	 */
	_updateQueueProgress: function()
	{
		var perc = 0,
			n_checked = 0;
		
		this.fileList.each(function(f)
		{
			if (f.checked) {
				perc += f.progress;
				n_checked++;
			}
		});
		
		if(n_checked == 0) return;
		
		this.queuePercent = perc / n_checked;
		
		this.fireEvent('onUploadProgress', [this.queuePercent, this.nUploaded + this.nCurrentUploads, this.fileList.length-this.nCancelled]);
	},


	/**
	 *
	 * @private
	 */
	_queueComplete: function()
	{
		this.isUploading = false;

		this.fireEvent('uploadComplete', [this.nUploaded, this.nErrors]);

		this.reset();
		// previously :
		// if(this.nErrors==0) this.reset();
	},


	/**
	 *
	 * @param item
	 * @param perc
	 * @private
	 */
	_itemProgress: function(item, perc)
	{
		this.fireEvent('itemProgress', [item, perc]);
		
		this._updateQueueProgress();
	},


	/**
	 *
	 * @param item
	 * @param file
	 * @param response
	 * @private
	 */
	_itemComplete: function(item, file, response)
	{
		if(file.cancelled) return;
		
		this.nCurrentUploads--;
		this.nUploaded++;
				
		this.fileList[file.id].uploaded = true;
		this.fileList[file.id].progress = 100;

		// Store the progression : used when onComplete is fired before onItemAdded has finished (large images)
		item.store('progress', 100);

		this._updateQueueProgress();
		
		this.fireEvent('onItemComplete', [item, file, response]);
		
		if(this.nCurrentUploads <= 0 && this.nUploaded + this.nErrors + this.nCancelled == this.fileList.length) this._queueComplete();
	},


	/**
	 *
	 * @param item
	 * @param file
	 * @param response
	 * @private
	 */
	_itemError: function(item, file, response)
	{
		this.nCurrentUploads--;
		this.nErrors++;
		
		if(typeof file.id != 'undefined' && typeof this.fileList[file.id] != 'undefined'){
			this.fileList[file.id].uploaded = true;
			this.fileList[file.id].error = true;
		}
		
		this.fireEvent('onItemError', [item, file, response]);
		
		if(this.nCurrentUploads <= 0) this._queueComplete();
	},


	/**
	 *
	 * @param file
	 * @private
	 */
	_addNewItem: function(file)
	{
		// create a basic wrapper for the thumb
		var item = new Element('div', {
			'class': 'dropzone_item',
			'id': 'dropzone_item_' + file.uniqueid + '_' + file.id
		}).inject(this.uiList);

		// check file type, and get thumb if it's an image
		
		// Get the URL object (unavailable in Safari 5-)
		window.URL = window.URL || window.webkitURL;
		
		if (file.type.match('image') && window.URL)  // typeof FileReader !== 'undefined' &&
		{
			// measure size of the blob image
			var img = new Element('img', {'style': 'visibility: hidden; position: absolute;'});
			img.addEvent('load', function(e)
			{
				// e.target.result for large images crashes Chrome?
				this.fireEvent('itemAdded', [item, file, img.src, img.getSize()]);
				// Clean up after yourself.
				window.URL.revokeObjectURL(img.src);
				img.destroy();
			}.bind(this));
			
			// if image is corrupted
			img.addEvent('error', function(e)
			{
				// e.target.result for large images crashes Chrome?
				this.fireEvent('itemAdded', [item, file]);
				// Clean up after yourself.
				window.URL.revokeObjectURL(img.src);
				img.destroy();
			}.bind(this));

			img.src = window.URL.createObjectURL(file.file);
			this.gravityCenter.adopt(img);
		}
		else
		{
			this.fireEvent('itemAdded', [item, file]);
		}

		// Adds a button to start the upload
		if (this.options.autostart == false && ! this.uiListUploadButton)
		{
			this.uiListUploadButton = new Element('a',{
				'class':'button filemanager-start-upload',
				'text': this.options.lang.start_upload}
			).addEvent('click', function()
			{
				this.upload();
			}.bind(this)).inject(this.uiList);
		}
		else if(this.options.autostart == false && this.uiListUploadButton)
		{
			this.uiListUploadButton.addEvent('click', function()
			{
				this.upload();
			}.bind(this))
		}

		return item;
	},


	/**
	 *
	 * @return {*}
	 * @private
	 */
	_getInputs: function()
	{
		return this.hiddenContainer.getElements('input[type=file]');
	},


	/**
	 *
	 * @return {*}
	 * @private
	 */
	_getForms: function()
	{
		return this.hiddenContainer.getElements('form');
	},


	/**
	 *
	 * @return {*}
	 * @private
	 */
	_countInputs: function()
	{
		var containers = this._getInputs();
		return containers.length;
	},

	_countItems:function()
	{
		var items = this.uiList.getElements('.item');
		return items.length;
	},


	/**
	 *
	 * @param filename
	 * @return {*}
	 * @private
	 */
	_getFileExtension: function(filename)
	{
		return filename.split('.').pop();
	},




	/**
	 * Change handling response to what you use in backend here..
	 * @param response
	 * @return {Boolean}
	 * @private
	 */
	_noResponseError: function(response)
	{
		return (response.error == 0);
	},


	/**
	 *
	 * @param e
	 * @private
	 */
	_stopEvent: function(e)
	{
		e.stop();
	}

});