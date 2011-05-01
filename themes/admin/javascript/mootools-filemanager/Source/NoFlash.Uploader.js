/*
---

description: Implements Upload functionality into the FileManager without using Flash

authors: James Sleeman (@sleemanj)

license: MIT-style license.

requires: [Core/*]

provides: Filemanager.NoFlashUploader

...
*/

/*
 * While the flash uploader is preferable, sometimes it is not possible to use it due to
 * server restrictions (eg, mod_security), or perhaps users refuse to use flash.
 *
 * This Upload handler will allow the MFM to continue to function, without multiple-upload-at-once
 * function and without progress bars.  But otherwise, it should work.
 */
FileManager.implement({

	options: {
		resizeImages: true,
		upload: true,

		// Is there a useful reason to have this different from propagateData... dubious :-/
		uploadAuthData: {}
	},

	hooks: {
		show: {
			upload: function() {
				this.startUpload();
			}
		},

		cleanup: {
			upload: function(){
				if (!this.options.upload  || !this.upload) {
					return;
				}

				try {
					if (this.upload.uploader) {
						this.upload.uploader.set('opacity', 0).dispose();
					}
				}
				catch(e) { }
			}
		}
	},

	_dummyframe: null,

	onDialogOpenWhenUpload: function(){

	},

	onDialogCloseWhenUpload: function(){

	},

	// Writing to file input values is not permitted, we replace the field to blank it.
	make_file_input: function(form_el)
	{
		var fileinput = (new Element('input')).set({type: 'file', 'name': 'Filedata'}).setStyles({width: 120});
		if (form_el.getElement('input[type=file]'))
		{
			fileinput.replaces(form_el.getElement('input[type=file]'));
		}
		else
		{
			form_el.adopt(fileinput);
		}
		return form_el;
	},


	startUpload: function()
	{
		if (!this.options.upload) {
			return;
		}

		// discard old iframe, if it exists:
		if (this._dummyframe)
		{
			// remove from the menu (dispose) and trash it (destroy)
			this._dummyframe.dispose().destroy();
			this._dummyframe = null;
		}

		var self = this;

		var f = (new Element('form'))
			//.set('action', tx_cfg.url)
			.set('method', 'post')
			.set('enctype', 'multipart/form-data')
			.set('target', 'dummyframe')
			.setStyles({ 'float': 'left', 'padding-left': '3px', 'display': 'block'});

		var uploadButton = this.addMenuButton('upload').addEvents({
			click:  function(e) {
				e.stop();
				self.browserLoader.set('opacity', 1);
				f.action = tx_cfg.url;
				
				// Update curent dir path to form hidden field
				self.uploadFormInputs['directory'].setProperty('value', self.CurrentDir.path);

				f.submit();
			},
			mouseenter: function(){
				this.addClass('hover');
			},
			mouseleave: function(){
				this.removeClass('hover');
				this.blur();
			},
			mousedown: function(){
				this.focus();
			}
		});

		this.menu.adopt(uploadButton);

		var tx_cfg = this.options.mkServerRequestURL(this, 'upload', Object.merge(
			this.options.propagateData,
			{
				directory: '',
				filter: this.options.filter,
				resize: this.options.resizeImages,     // TODO: must be updated when button is clicked
				reportContentType: 'text/plain'        // Safer for iframes: the default 'application/json' mime type would cause FF3.X to pop up a save/view dialog!
			}
		));
		
		// Stores form hidden inputs
		this.uploadFormInputs = new Hash();

		// Create hidden input for each form data
		Object.each(tx_cfg.data, function(v, k){
			input = new Element('input').set({type: 'hidden', name: k, value: v, id: 'filemanager_upload_' + k });
			f.adopt(input);
			self.uploadFormInputs[k] = input;
		});

		if (this.options.resizeImages)
		{
			var resizer = new Element('div', {'class': 'checkbox'});
			var check = (function()
			{
				this.toggleClass('checkboxChecked');

				// Update the resize hidden field
				self.uploadFormInputs['resize'].setProperty('value', (this.hasClass('checkboxChecked')) ? 1 : 0);
			}).bind(resizer);

			check();
			uploadButton.label = new Element('label').adopt(
				resizer, new Element('span', {text: this.language.resizeImages})
			).addEvent('click', check).inject(this.menu);
		}

		this.make_file_input(f);

		f.inject(this.menu, 'top');
		this.menu.setStyle('height', '60px');

		this._dummyframe = (new IFrame).set({src: 'about:blank', name: 'dummyframe'}).setStyles({display: 'none'});
		this.menu.adopt(this._dummyframe);

		this._dummyframe.addEvent('load', function()
		{
			self.browserLoader.fade(0);

			try
			{
				var response;
				try
				{
					response = this.contentDocument.documentElement.textContent;
				}
				catch(e) {}

				if (!response)
				{
					try
					{
						response = this.contentWindow.document.innerText;
					}
					catch(e) {}
				}

				if (!response)
				{
					try
					{
						response = this.contentDocument.innerText;
					}
					catch(e) {}
				}

				if (!response)
				{
					throw "Can't find response.";
				}

				response = JSON.decode(response);

				if (response && !response.status)
				{
					mfm.showError('' + response.error);
				}
				else if (response)
				{
					mfm.onShow = true; // why exactly do we need to set this, what purpose does the default of NOT preselecting the thing we asked to preselect have?

					// Why the Hell a regex replace on Directory???
					//self.load(mfm.Directory.replace(/\/$/, ''), response.name ? response.name : null);
					self.load(mfm.Directory, (response.name ? response.name : null));
				}
				else
				{
					this.showError('bugger! No JSON response!');
				}
			}
			catch(e)
			{
				// Maybe this.contentDocument.documentElement.innerText isn't where we need to look?
				//debugger;
				self.diag.log('noFlashUpload: document innerText grab FAIL:', e, this, tx_cfg);
				self.load(self.Directory);
			}

			self.make_file_input(f);
		});
	}
});

