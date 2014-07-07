/*
---

name: DropZone.HTML5

description: A DropZone module. Handles uploading using the HTML5 method

license: MIT-style license

authors:
  - Mateusz Cyrankiewicz
  - Juan Lago

requires: [DropZone]

provides: [DropZone.HTML5]

...
*/

DropZone.HTML5 = new Class({

	Extends: DropZone,

	initialize: function (options)
	{
		this.setOptions(options);
		this.method = 'HTML5';
		this.activate();
	},
	
	bound: {},

	/**
	 *
	 */
	activate: function ()
	{
		this.parent();
		
		// If drop area is specified, 
		// and in HTML5 mode,
		// activate dropping
		
		if(this.uiDropArea)
		{
			// Extend new events
			Object.append(Element.NativeEvents, {
				dragenter: 2,
				dragleave: 2,
				dragover: 2,
				drop: 2
			});
			
			this.uiDropArea.addEvents(
			{
				'dragenter': function (e)
				{
					e.stop();
					this.uiDropArea.addClass('hover');
					
				}.bind(this),

				'dragleave': function (e)
				{
					e.stop();
					
					if (e.target && e.target === this.uiDropArea) {
						this.uiDropArea.removeClass('hover');
					}
					
				}.bind(this),

				'dragover': function (e)
				{
					e.stop();
					e.preventDefault();
					
				}.bind(this),

				'drop': function (e)
				{
					e.stop();
					if(e.event.dataTransfer)
						this.addFiles(e.event.dataTransfer.files);

					this.uiDropArea.removeClass('hover');
					
				}.bind(this)
			});
			
			// prevent defaults on window
			this.bound = {
				stopEvent: this._stopEvent.bind(this)
			};
		
			$(document.body).addEvents({
				'dragenter': this.bound.stopEvent,
				'dragleave': this.bound.stopEvent,
				'dragover': this.bound.stopEvent,
				'drop': this.bound.stopEvent
			});
		}
		
		// Activate trigger for html file input
		this._activateHTMLButton();
		
	},


	/**
	 *
	 */
	upload: function()
	{
		this.fileList.each(function(file, i)
		{
			if (file.checked && ! file.uploading && this.nCurrentUploads < this.options.max_queue)
			{
				// Upload only checked and new files
				file.uploading = true;
				this.nCurrentUploads++;
				
				this._html5Send(file, 0, false);
			}
		}, this);
		
		this.parent();
	},


	/**
	 *
	 * @param file
	 * @param start
	 * @param resume
	 * @private
	 */
	_html5Send: function (file, start, resume)
	{
		var item;
		// if (this.uiList) item = this.uiList.getElement('#dropzone_item_' + (file.uniqueid));
		// now getting the item globally in case it was moved somewhere else in onItemAdded event
		// this way it can always remain controlled
		item = $('dropzone_item_' + file.uniqueid + '_' + file.id);
		
		var end = this.options.block_size,
			chunk,
			is_blob = true;

		var header_file_name = file.name;

		var total = start + end;
		if (total > file.size) end = total - file.size;

		// Get slice method : Standard browser first
		if (file.file.slice)
		{
			chunk = file.file.slice(start, total);
			header_file_name = unescape(encodeURIComponent(file.name));
		}
		// Mozilla based
		else if (file.file.mozSlice)
		{
			chunk = file.file.mozSlice(start, total);
			header_file_name = unescape(encodeURIComponent(file.name));
		}
		// Chrome 20- and webkit based // Safari slices the file badly
		else if (file.file.webkitSlice && !Browser.safari)
		{
			chunk = file.file.webkitSlice(start, total);
		}
		// Safari 5-
		else
		{
			// send as form data instead of Blob
			chunk = new FormData();
			chunk.append('file', file.file);
			is_blob = false;
			header_file_name = unescape(encodeURIComponent(file.name));
		}
		
		// Set headers
		var headers = {
			'Cache-Control': 'no-cache',
			'X-Requested-With': 'XMLHttpRequest',
			'X-File-Name': header_file_name,
			'X-File-Size': file.size,
			'X-File-Id': file.id
		};

		if(resume) headers['X-File-Resume'] = resume;

		// Add file additional vars to the headers ( -> avoid using query string)
		Object.each(file.vars, function(value, index)
		{
			// Some servers don't support underscores in X-Headers
			index = index.replace(/_/g, '-');
			headers['X-' + String.capitalize(index)] = value;
		});

		// Send request
		var xhr = new Request.Blob({
			url: this.url,
			headers: headers,
			onProgress: function(e)
			{
				if( ! is_blob)
				{
					// track xhr progress only if data isn't actually sent as a chunk (eg. in Safari)
					var perc = e.loaded / e.total * 100;
					this.fileList[file.id].progress = perc;
					this._itemProgress(item, perc);
				}
			}.bind(this),
			onSuccess: function (response)
			{
				try {
					response = JSON.decode(response, true);
				} catch(e){
					response = '';
				}
				
				if(typeof this.fileList[file.id] != 'undefined' && !this.fileList[file.id].cancelled)
				{
					if (this._noResponseError(response))
					{
						// || total >= file.size // sometimes the size is measured wrong and fires too early?
						if (response.finish == true)
						{
							// job done!
							this._itemComplete(item, file, response);
							if (this.nCurrentUploads != 0 && this.nCurrentUploads < this.options.max_queue && file.checked) this.upload();
						}
						else
						{
							// in progress..
							if(file.checked)
							{
								var perc = (total / file.size) * 100;

								// it's used to calculate global progress
								this.fileList[file.id].progress = perc;
								
								this._itemProgress(item, perc);

								// Set the filename as set by the backend : 'name' comes from Filemanager, 'file_name' comes from MY_Upload()
								file.name = typeOf(response.name) != 'null' ? response.name : response.file_name;

								// Recursive upload
								this._html5Send(file, start + response.size.toInt(), true);
							}
						}
					}
					else
					{
						// response error!
						this._itemError(item, file, response);
					}
				}
				else
				{
					// item doesn't exist anymore, probably cancelled
				}

			}.bind(this),
			onFailure: function()
			{
				this._itemError(item, file);
			}.bind(this),
			onException: function(e, key, value)
			{
				console.log('DropZone.HTML5 ERROR : ' + e.message);
				console.log('DropZone.HTML5 ERROR : ' + key + ' : ' + value);
			}.bind(this)
		});

		xhr.send(chunk);
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
		this.parent();

		if(this.hiddenContainer) this.hiddenContainer.empty();
	},

	/* Private methods */

	/**
	 *
	 * @private
	 */
	_newInput: function(){
		
		this.parent();
		
		// add interaction to input
		this.lastInput.addEvent('change', function (e)
		{
			e.stop();

			this.addFiles(this.lastInput.files);

		}.bind(this));
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
		this.parent(item, file, response);
				
/*
// Previuoulsy
		if(this.nCurrentUploads == 0)
			this._queueComplete();
		else if (this.nCurrentUploads != 0 && this.nCurrentUploads < this.options.max_queue)
			this.upload();
*/

		if (this.nCurrentUploads != 0 && this.nCurrentUploads < this.options.max_queue)
			this.upload();
	}
	
});