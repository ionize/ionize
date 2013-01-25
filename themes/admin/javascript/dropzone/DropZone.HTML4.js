/*
---

name: DropZone.HTML4

description: A DropZone module. Handles uploading using the HTML4 method

license: MIT-style license

authors:
  - Mateusz Cyrankiewicz
  - Juan Lago

requires: [DropZone]

provides: [DropZone.HTML4]

...
*/

DropZone.HTML4 = new Class({

	Extends: DropZone,

	initialize: function (options)
	{
		this.setOptions(options);
		
		this.method = 'HTML4';
		
		this.activate();
	},
	
	activate: function ()
	{
		// Setup some options
		this.options.multiple = false;
		
		this.iframe = new IFrame({
			id: 'dropZoneUploadIframe',
			name: 'dropZoneUploadIframe',
			styles: {
				display: 'none'
			}
		});
		
		this.parent();
		
		this.iframe.addEvent('load', function ()
		{
			//var icdb = this.iframe.contentWindow.document.body;
			var response = this.iframe.contentWindow.document.body.innerHTML;
			
			if (response != '') {
								
				this.isUploading = false;

				this.upload();
				
				try
				{
					// substring to avoid problems in Chrome, which adds a <pre> object to text
					response = JSON.decode(response.substring(response.indexOf("{"), response.lastIndexOf("}") + 1), true);
					
				} catch(e){
					//
				}
				
				var file = this.fileList[response.key];
				
				var item;
				if (this.uiList && response)
				{
// HERE !!!
console.log('#dropzone_item_' + file.uniqueid + '_' + file.id);
					var item = this.uiList.getElement('#dropzone_item_' + file.uniqueid + '_' + file.id);
				}
				
				if (this._checkResponse(response))
				{
					file.uploaded = true;

					// Complete file information from server side
					file.size = response.size;
					
					this._itemComplete(item, file, response);
										
				} else {
					
					this._itemError(item, file, response);
				}
			}
		}.bind(this)).inject(this.hiddenContainer);

		// this._buildBase();

		// Trigger for html file input
		this._activateHTMLButton();
	},
	
	upload: function ()
	{
		if (!this.isUploading)
		{
			this._getForms().each(function (el, id)
			{
				var file = this.fileList[id];
				
				if (file != undefined && !this.isUploading)
				{
					if (file.checked && !file.uploading)
					{
						file.uploading = true;
						var perc = file.progress = 50;
						
						if (this.uiList)
						{
							var item = this.uiList.getElement('#dropzone_item_' + this.fileList[id].uniqueid + '_' + file.id);
						}
						this._itemProgress(item, perc);
						
						this.isUploading = true;
						this.nCurrentUploads++;
						var submit = el.submit();
					}
				}
			}.bind(this));
			
			this.parent();
		}
	},

	_getInputFileName: function (el)
	{
		var pieces = el.get('value').split(/(\\|\/)/g);

		return pieces[pieces.length - 1];
	},

	cancel: function (id, item)
	{
		this.parent(id, item);
	},
	
	
	/* Private methods */
	
	_newInput: function ()
	{
		// create form
		var formcontainer = new Element('form',
		{
			id: 'tbxFile_' + this._countInputs(),
			name: 'frmFile_' + this._countInputs(),
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
		this.parent(formcontainer);
		
		// add interaction to input
		this.lastInput.addEvent('change', function (e)
		{
			e.stop();
			
			this.addFiles([{
				name: this._getInputFileName(this.lastInput),
				type: null,
				size: null
			}]);

		}.bind(this));
	}
});