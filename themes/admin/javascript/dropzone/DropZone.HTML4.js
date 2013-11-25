/*
---

name: DropZone.HTML4

description: A DropZone module. Handles uploading using the HTML4 method

license: MIT-style license

authors:
  - Mateusz Cyrankiewicz
  - Juan Lago
  - Michel-Ange Kuntz

requires: [DropZone]

provides: [DropZone.HTML4]

...
*/

DropZone.HTML4 = new Class({

	Extends: DropZone,

	initialize: function(options)
	{
		this.setOptions(options);
		this.method = 'HTML4';
		this.activate();
	},

	bound: {},

	activate: function()
	{
		// Setup some options
		this.options.multiple = false;

		// Upload iFrame
		this.iframe = new IFrame({
			id: 'dropZoneUploadIframe',
			name: 'dropZoneUploadIframe',
			styles: {
				display: 'none'
			}
		});

		// Init the fileList (never reset by reset() )
		this.fileList = new Array();
		this.lastInput = undefined;
		this.nCurrentUploads = 0;
		this.nUploaded = 0;
		this.setVar('file_input_prefix', this.options.ui_file_input_prefix);

		// Creates uiButton, uiList, hiddenContainer
		this.parent();

		// Load Event on iFrame
		this.iframe.addEvent('load', function()
		{
			var response = this.iframe.contentWindow.document.body.innerHTML;

			if (response != '')
			{
				this.isUploading = false;

				// Will process the next form
				this.upload();

				// substring to avoid problems in Chrome, which adds a <pre> object to text
				try	{
					response = JSON.decode(response.substring(response.indexOf("{"), response.lastIndexOf("}") + 1), true);
				}
				catch(e){}

				var file = this.fileList[response.key];

				if (file)
				{
					var item = null;

					if (this.uiList && response)
						item = this.uiList.getElement('#dropzone_item_' + file.uniqueid + '_' + file.id);

					if (file && item)
					{
						if (this._noResponseError(response))
						{
							file.uploaded = true;

							// Complete file information from server side
							file.size = response.size;

							this._itemComplete(item, file, response);
						}
						else
						{
							this._itemError(item, file, response);
						}
					}
				}
			}
		}.bind(this)).inject(this.hiddenContainer);

		// Trigger for html file input
		this._activateHTMLButton();

		// First Form Input
		this._newInput();
	},

	addFiles: function(files)
	{
		this.parent(files);

		this._newInput();
	},

	/**
	 * Starts the upload
	 *
	 */
	upload: function()
	{
		if (!this.isUploading)
		{
			// Each form = one file to upload
			forms = this._getForms();

			// the last form (no selected file) must not be uploaded, only if no autostart
			if (this.options.autostart == false)
				forms.pop();

			var delay_value = 0;
			forms.each(function(formElement, id)
			{
				var file = this.fileList[id];

				if (file != undefined && !this.isUploading)
				{
					if (file.checked && ! file.uploading)
					{
						this.fileList[id].uploading = true;

						// Fake progression...
						var perc = file.progress = 50;

						if (this.uiList)
						{
							var item = this.uiList.getElement('#dropzone_item_' + this.fileList[id].uniqueid + '_' + file.id);
							this._itemProgress(item, perc);
						}

						this.isUploading = true;
						this.nCurrentUploads++;

						// Add file additional vars to form as inputs before submit
						Object.each(file.vars, function(value, index) {
							formElement.adopt(new Element('input', {'type': 'hidden', 'name':index, 'value':value}));
						});

						(function() {
							formElement.submit();
						}).delay(100);
					}
				}
			}.bind(this));

			this.parent();
		}
	},


	/**
	 *
	 * @param id
	 * @param item
	 */
	cancel: function(id, item)
	{
		this.parent(id, item);
	},


	/**
	 *
	 */
	kill: function()
	{
		this.parent();

		// remove events
		if(this.uiDropArea) $(document.body).removeEvents(
			{
				'dragenter': this.bound.stopEvent,
				'dragleave': this.bound.stopEvent,
				'dragover': this.bound.stopEvent,
				'drop': this.bound.stopEvent
			});
	},

	reset: function()
	{
		this.url = this.options.url;
		this.fileList = new Array();

		this.nErrors = 0;
		this.nUploaded = 0;
		this.nCurrentUploads = 0;
		this.nCancelled = 0;

		this.queuePercent = 0;
		this.isUploading = false;

		this.fireEvent('reset', [this.method]);
	},


	/**
	 *
	 * @param el
	 * @return {*}
	 * @private
	 */
	_getInputFileName: function(el)
	{
		var pieces = el.get('value').split(/(\\|\/)/g);

		return pieces[pieces.length - 1];
	},


	/* Private methods */

	/**
	 *
	 * @private
	 */
	_newInput: function()
	{
		// Remove all existing forms labels
		var forms = this._getForms();
		forms.each(function(form) {
			var label = form.getElement('label');
			if (label) label.destroy();
		});

		// create form
		var formcontainer = new Element('form',
		{
			id: this.options.ui_form_prefix + this._countInputs(),
			name: this.options.ui_form_prefix + this._countInputs(),
			enctype: 'multipart/form-data',
			encoding: 'multipart/form-data',
			method: 'post',
			action: this.url,
			target: this.iframe.get('name')
			
		}).inject(this.hiddenContainer);

		if (this.options.max_file_size > 0)
		{
			new Element('input', {
				name: 'MAX_FILE_SIZE',
				type: 'hidden',
				value: this.options.max_file_size
			}).inject(formcontainer);
		}
		
		// call parent
		var lastInput = this.parent(formcontainer);
		
		// add interaction to input
		lastInput.addEvent('change', function (e)
		{
			e.stop();

			this.addFiles([{
				name: this._getInputFileName(lastInput),
				path: lastInput.value,
				type: null,
				size: null
			}]);

		}.bind(this));
	}
});