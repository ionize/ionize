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
 * This Upload handler will allow the MTFM to continue to function, without multiple-upload-at-once
 * function and without progress bars.  But otherwise, it should work.
 */
FileManager.implement({

	options: {
		resizeImages: true,
		upload: true,
		uploadAuthData: {}             // deprecated; use FileManager.propagateData instead!
	},

	hooks: {
		show: {
			upload: function() {
				this.startUpload();
			}
		},

		cleanup: {
			upload: function() {
				this.hideUpload();
			}
		}
	},

	onDialogOpenWhenUpload: function() {

	},

	onDialogCloseWhenUpload: function() {

	},

	// Writing to file input values is not permitted, we replace the field to blank it.
	make_file_input: function(form_el)
	{
		var fileinput = (new Element('input')).set({
			type: 'file',
			name: 'Filedata',
			id: 'filemanager_upload_Filedata'
		});
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

	hideUpload: function()
	{
		if (!this.options.upload || !this.upload) return;

		if (this.upload.uploadButton.label)
		{
			this.upload.uploadButton.label.fade(0).get('tween').chain(function() {
				this.element.dispose().destroy();
			});
			this.upload.uploadButton.label = null;
		}
		if (this.upload.uploadButton)
		{
			this.upload.uploadButton.fade(0).get('tween').chain(function() {
				this.element.dispose().destroy();
			});
			this.upload.uploadButton = null;
		}
		if (this.upload.form)
		{
			this.upload.inputs = null;

			this.upload.form.dispose().destroy();
			this.upload.form = null;
		}
		this.menu.setStyle('height', '');

		if (this.upload.resizer)
		{
			this.upload.resizer.dispose().destroy();
			this.upload.resizer = null;
		}

		// discard old iframe, if it exists:
		if (this.upload.dummyframe)
		{
			// remove from the menu (dispose) and trash it (destroy)
			this.upload.dummyframe.dispose().destroy();
			this.upload.dummyframe = null;
		}
	},

	startUpload: function()
	{
		if (!this.options.upload) {
			return;
		}

		var self = this;

		this.upload = {
			inputs: {},
			resizer: null,
			dummyframe: null,
			dummyframe_active: false,     // prevent premature firing of the load event (hello, MSIE!) to cause us serious trouble in there

			form: (new Element('form'))
				//.set('action', tx_cfg.url)
				.set('method', 'post')
				.set('enctype', 'multipart/form-data')
				.set('encoding', 'multipart/form-data')		// IE7
				.set('target', 'dummyframe')
				.setStyles({
					'float': 'left',
					'padding-left': '3px',
					'display': 'block'
			}),

			uploadButton: this.addMenuButton('upload').inject(this.menu, 'bottom').addEvents({
				click:  function(e) {
					e.stop();
					self.browserLoader.fade(1);
					self.upload.form.action = tx_cfg.url;

					// Update curent dir path to form hidden field
					self.upload.inputs['directory'].setProperty('value', self.CurrentDir.path);

					self.upload.dummyframe_active = true; // NOW you may fire when ready...

					self.upload.form.submit();
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			}),

			lastFileUploaded: null,  // name of the last successfully uploaded file; will be preselected in the list view
			error_count: 0
		};

		var tx_cfg = this.options.mkServerRequestURL(this, 'upload', Object.merge({},
						this.options.propagateData,
						(this.options.uploadAuthData || {}), {
							directory: (this.CurrentDir ? this.CurrentDir.path : null),
							filter: this.options.filter,
							resize: this.options.resizeImages,
							reportContentType: 'text/plain'        // Safer for iframes: the default 'application/json' mime type would cause FF3.X to pop up a save/view dialog!
						}));

		// Create hidden input for each form data
		Object.each(tx_cfg.data, function(v, k){
			var input = new Element('input').set({type: 'hidden', name: k, value: v, id: 'filemanager_upload_' + k });
			self.upload.form.adopt(input);
			self.upload.inputs[k] = input;
		});

		if (this.options.resizeImages)
		{
			this.upload.resizer = new Element('div', {'class': 'checkbox'});
			var check = (function()
			{
				this.toggleClass('checkboxChecked');

				// Update the resize hidden field
				self.upload.inputs['resize'].setProperty('value', (this.hasClass('checkboxChecked')) ? 1 : 0);
			}).bind(this.upload.resizer);
			check();
			this.upload.uploadButton.label = new Element('label', { 'class': 'filemanager-resize' }).adopt(
				this.upload.resizer,
				new Element('span', {text: this.language.resizeImages})
			).addEvent('click', check).inject(this.menu);
		}

		this.make_file_input(self.upload.form);

		self.upload.form.inject(this.menu, 'top');
		//this.menu.setStyle('height', '60px');

		// discard old iframe, if it exists:
		if (this.upload.dummyframe)
		{
			// remove from the menu (dispose) and trash it (destroy)
			this.upload.dummyframe.dispose().destroy();
			this.upload.dummyframe = null;
		}

		this.upload.dummyframe = (new IFrame).set({src: 'about:blank', name: 'dummyframe'}).setStyles({display: 'none'});
		this.menu.adopt(this.upload.dummyframe);

		this.upload.dummyframe.addEvent('load', function()
		{
			var iframe = this;
			self.diag.log('NoFlash upload response: ', this, ', iframe: ', self.upload.dummyframe, ', ready:', (1 * self.upload.dummyframe_active));

			// make sure we don't act on premature firing of the event in MSIE browsers:
			if (!self.upload.dummyframe_active)
				return;

			self.browserLoader.fade(0);

			var response = null;
			Function.attempt(function() {
					response = iframe.contentDocument.documentElement.textContent;
				},
				function() {
					response = iframe.contentWindow.document.innerText;
				},
				function() {
					response = iframe.contentDocument.innerText;
				},
				function() {
					// Maybe this.contentDocument.documentElement.innerText isn't where we need to look?
					//debugger;
					response = "{status: 0, error: \"noFlashUpload: document innerText grab FAIL: Can't find response.\"}";
				}
			);

			var j = JSON.decode(response);

			if (j && !j.status)
			{
				self.showError('' + j.error);
				self.load(self.CurrentDir.path);
			}
			else if (j)
			{
				self.load(self.CurrentDir.path, j.name);
			}
			else
			{
				// IE9 fires the load event on init! :-(
				if (self.CurrentDir)
				{
//					self.showError('bugger! No or faulty JSON response! ' + response);
					self.load(self.CurrentDir.path);
				}
			}

			self.make_file_input(self.upload.form);
		});
	}
});

