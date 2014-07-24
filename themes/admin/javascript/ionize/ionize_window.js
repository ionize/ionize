/**
 * Ionize Window
 *
 */
ION.Window = new Class({

	Implements: [Events, Options],

	w: null,			// Mocha UI Window
 	buttons:[],			// Array of bottom buttons
	divButtons: null,

	options: {
		type:'window',
		y: 40,
		width: 500,
		height:300,
		maximizable: true,
		contentBgColor: '#fff',
		barTitle: '',
		title: null,
		buttons:[],
		onDraw: function(){}

		/*
		 * User's options :
		 *
		 barTitle: '',              // Window top bar title
		 title: {
		    text: '',               // Main Window Title
		    class: ''               // Title class (left icon)
		 }
		 subtitle: [                // Array of subtitle's key / value (displayed under the main title
		    {
				key: '',            // Optional
				value: ''           // Works with key
				html: ''            // Replaces key/value by plain HTML
			 }
			 ...
		 ]
		 form: {
		    id: '',                 // Form ID
		    class: '',               // Form CSS class
		    action: '',             // URL to the save controller
		    reload: function(),     // Function executed after Form save,
		    post: {					// Data to merge with the form data
		    	key:val
		    }
		 }

		 */
	},

	/*
	 * Events
	 *

	 onDraw: function(ION.Window){}

	 */

	/**
	 *
	 * @param options
	 */
	initialize: function()
	{
		var options = arguments[0] ? arguments[0] : {};
		this.setOptions(options);

		if (options.id && $(options.id))
		{
			var self = $(options.id).retrieve('ion_window');

			var w = self.getWindow();
			w.focus.delay(10, w);
			if (MUI.options.standardEffects) w.el.windowEl.shake();

			return self;
		}
		else
		{
			var t = this.options.type;
			delete options.type;

			if (t == 'form')
				this.w = this._createFormWindow(options);
			else
				this.w = this._createWindow(options);

			// Once we have this.w, we can affect 'this' to all buttons
			var self = this;
			this.buttons.each(function(button){
				button.w = self;
			});

			this.fireEvent('onDraw', this);

			return this;
		}
	},

	getContainer: function()
	{
		return this.w.el.content;
	},

	getWindow: function()
	{
		return this.w;
	},

	getWrapper: function()
	{
		return this.w.el.contentWrapper;
	},

	getForm: function()
	{
		if (typeOf(this.form) != 'null')
			return this.form;

		return null;
	},

	getTitle: function()
	{
		return this.title;
	},

	getSaveButton: function()
	{
		return this.saveButton;
	},

	getSaveReloadButton: function()
	{
		return this.saveReloadButton;
	},

	close: function()
	{
		this.w.close();
	},

	resize: function(x,y)
	{
		this.w.resize({width:x, height:y, top:70, centered:true});
	},

	_createWindow: function(opt)
	{
		var self = this,
			options = {
				container: document.body,
				content:{}
			}
		;

		opt.contentTitle = (opt.title && opt.title.text) ? opt.title.text : null;
		opt.contentTitleClass = (opt.title && opt.title.class) ? ' ' + opt.title.class : '';
		opt.title = typeOf(opt.barTitle) != 'null' && opt.barTitle != '' ? opt.barTitle : (opt.contentTitle != null ? opt.contentTitle : '');
		opt.cssClass = opt.cssClass != '' ? opt.cssClass : '';

		Object.append(options, opt);

		// Prepare the buttons
		Object.each(opt.buttons, function(bOptions)
		{
			if ( ! bOptions['class']) bOptions['class'] = 'green';

			var button = new ION.Button(bOptions);

			if ( ! button.getElement().hasClass('right') && ! button.getElement().hasClass('left'))
				button.getElement().addClass('right');

			self.buttons.push(button);
		});

		if (options.contentTitle)
		{
			var ode = null;

			if (opt.onDrawEnd)
			{
				ode = opt.onDrawEnd;
			}

			options['onDrawEnd'] = function(w)
			{
				self.title = new ION.WindowTitle({
					title: options.contentTitle,
					subtitle: opt.subtitle,
					'class': options.contentTitleClass,
					container: w.el.content
				});

				// Set before call of the potential Form Window onDrawEnd
				if (typeOf(self.divButtons) == 'null')
					self.divButtons = new Element('div', {'class':'buttons'}).inject(w.el.content);

				// Buttons set as options
				self.buttons.each(function(button)
				{
					button.getElement().inject(self.divButtons);
				});

				if ( ode != null)
					ode(w);
			}
		}

		var w = new MUI.Window(options);
		w.el.windowEl.store('ion_window', this);

		return w;
	},


	/**
	 *
	 * @param opt
	 * @returns {MUI.Window}
	 * @private
	 */
	_createFormWindow: function(opt)
	{
		var self = this;

		this.form = null;

		if (opt.form)
		{
			if (typeOf(opt.form.id) == 'null') opt.form.id = ION.generateHash(16);
			if (typeOf(opt.form['class']) == 'null') opt.form['class'] = '';

			var options =
			{
				onDrawEnd: function(w)
				{
					if (typeOf(self.divButtons) == 'null')
						self.divButtons = new Element('div', {'class':'buttons'}).inject(w.el.content);

					self.form = new Element('form', {
						id: opt.form.id,
						'class': opt.form['class'],
						action: opt.form.action,
						method: 'post'
					}).inject(w.el.content);

					self.saveButton = new Element('button', {
						'class':'button right yes',
						id: 'save' + opt.form.id,
						text: Lang.get('ionize_button_save_close')
					}).inject(self.divButtons);

					// Form data to send with the form, whatever is sent btw.
					if (opt.form.post)
					{
						Object.each(opt.form.post, function(value, idx)
						{
							if (typeOf(value) == 'object')
							{
								Object.each(value, function(val, key)
								{
									new Element('input', {
										'type':'hidden',
										name: idx + '[' + key + ']', value:val
									}).inject(self.form);
								});
							}
							else
							{
								new Element('input', {'type':'hidden', name:idx, value:value}).inject(self.form);
							}
						});
					}

					if (opt.form.reload)
					{
						self.saveReloadButton = new Element('button', {
							'class':'button blue right ml10',
							text: Lang.get('ionize_button_save')
						}).inject(self.divButtons);

						self.saveReloadButton.addEvent('click', function()
						{
							ION.JSON(
								opt.form.action,
								self.form,
								{
									onSuccess:function(json)
									{
										w.close();
										opt.form.reload(json);
									}
								}
							);
						});
					}

					var cancelButton = new Element('button', {
						'class':'button right red',
						id: 'cancel' + opt.form.id,
						text: Lang.get('ionize_button_cancel')
					}).inject(self.divButtons);

					cancelButton.addEvent('click', function(){ w.close(); });

					ION.setFormSubmit(
						self.form,         // Form Object
						self.saveButton.id,     // Save button ID
						self.form.action,  // Save URL
						null,              // Confirmation Object (null in this case, no conf. to open one window)
						opt.form           // Options, to pass onSuccess() method
					);
				}
			}
		}
		else
		{
			options = {
				onDrawEnd: function(w)
				{
					new Element('span', {
						'class':'lite',
						text: 'ERROR : Please set the "form" options action, at least.'
					}).inject(w.el.content);
				}
			};
		}

		Object.append(opt, options);

		var w = this._createWindow(opt);

		return w;
	}
});

ION.WindowTitle = new Class({

	Implements: [Events, Options],

	options: {
		'class' : '',
		title : '',
		subtitle: [
		/*
			{
				key: '',            // Optional
				value: ''           // Works with key
				html: ''            // Replaces key/value by plain HTML. In this case, key/values are not used if set.
			}
			...
		*/
		],
		build: true                 // Build or not the title. If false, the title will need to be returned
									// with getDomElement()
	},

	initialize: function(options)
	{
		this.setOptions(options);

		this.container = typeOf(this.options.container) == 'string' ? $(this.options.container) : this.options.container;

		this.subTitleElement = null;

		this.buildTitle();

		return this;
	},

	buildTitle: function()
	{
		this.domElement = new Element('div');

		this.h2 = new Element('h2', {
			'class': 'main ' + this.options.class,
			'html' : this.options.title
		}).inject(this.domElement);

		// Subtitle is one array of objects
		if (this.options.subtitle)
			this.setSubtitle(this.options.subtitle);

		if (this.options.build == true)
			this.domElement.inject(this.container);
	},

	addClass: function(cl)
	{
		this.h2.addClass(cl);
		return this;
	},

	removeClass: function(cl)
	{
		this.h2.removeClass(cl);
		return this;
	},

	setTitle: function(title)
	{
		if (this.h2) this.h2.set('html', title);
	},

	getDomElement: function()
	{
		return this.domElement;
	},

	setSubtitle: function(subtitle)
	{
		this.getSubtitleDomElement().empty();

		var p = new Element('p').inject(this.subTitleElement);

		if (typeOf(subtitle) == 'string')
			var span = new Element('span', {'class': 'lite', 'html': subtitle  }).inject(p);
		else
		{
			Object.each(subtitle, function(sub, idx)
			{
				if (idx == 'html')
				{
					sub = {value:sub}
				}
				else
				{
					if (idx > 0)
						new Element('span', {'html': ' | '}).inject(p);

					if (typeOf(sub.html) != 'null')
					{
						sub = {value:sub.html}
					}
					else if (sub.key && typeOf(sub.key) == 'string')
					{
						var span = new Element('span', {'class': 'lite', 'html': sub.key  }).inject(p);

						if (sub.value)
							span.set('html', span.get('html') + ' : ' );
					}
				}
				new Element('span', {'html': sub.value}).inject(p);
			});
		}
	},

	removeSubtitle: function()
	{
		if (this.subTitleElement != null)
		{
			this.subTitleElement.destroy();
			this.subTitleElement = null;
		}
	},

	getSubtitleDomElement: function()
	{
		if (this.subTitleElement == null)
		{
			this.buildSubtitleElement();
		}

		return this.subTitleElement;
	},

	buildSubtitleElement: function()
	{
		this.subTitleElement = new Element('div', {
			'class': 'main subtitle'
		}).inject(this.domElement, 'bottom');
	}
});


ION.append({


	/**
	 * Ionize notification window
	 * Launch a notification window creation
	 *
	 * @param	type    string 	    type of notification. Can be : error, notice, success
	 * @param	message string	    Notification message
	 */
	notification: function(type, message)
	{
		new MUI.Window({
			loadMethod: 'html',
			closeAfter: 2500,
			type: 'notification',
			cssClass: 'notification ',
			content: '<div class="'+ type +'">' + message + '</div>',
			width: 350,
			height: 50,
			y: 1,
			padding:  { top: 15, right: 12, bottom: 10, left: 12 },
			shadowBlur: 5,
			bodyBgColor: [250, 250, 250]
		});
	},


	/**
	 * Ionize Add Confirmation modal window
	 *
	 * @param	id          string		Window ID
	 * @param	button      string		Button or any element ID on wich add the link
	 * @param	callback    string		URL or JS function called in case of user confirmation
	 * @param	msg         string		Element name to update after the request success
	 * @param	options     object		options
	 *
	 */
	addConfirmation: function(id, button, callback, msg, options)
	{
		$(button).addEvent('click', function(e)
		{
			e.stop();
			ION.confirmation(id, callback, msg, options);
		});	
	},

	/**
	 * Ionize Confirmation modal window
	 * Opens a windows with yes / no buttons
	 *
	 * @param	id          string		Window ID
	 * @param	callback    string		URL or Callback JS function to call if yes answer
	 * @param	msg         string		Message
	 * @param	wOptions    object		Window extended options
	 *
	 */
	confirmation: function(id, callback, msg)
	{
		// Get the buttons container
		var wButtons = ION._getConfirmationButtons(id, callback);
		var wOptions = arguments[3];

		// Window question message
		var wMsg = (Lang.get(msg)) ? Lang.get(msg) : msg ;

		// Message HTML Element & window content container		
		var wMessage = new Element('div', {'class':'message'}).set('html', wMsg);		// Message
		var wContent = new Element('div').adopt(wMessage, wButtons);					// Windows content final container

		// Window options
		var options = {
			id: 'w' + id,
			content: wContent,
			title: Lang.get('ionize_modal_confirmation_title'),
			cssClass:'confirmation',
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }			
		}

		// Extends the window options
		if (typeOf(wOptions) != 'null') {options =  Object.append(options, wOptions);}
		
		// Open the confirmation modal window
		new MUI.Modal(options);
	},

	/**
	 * Modal windows
	 *
	 */
	error: function(msg, wOptions)
	{
		var options = ION._getModalOptions('error', msg);
		new MUI.Modal(options);		
	},

	alert: function(msg, wOptions)
	{
		var options = ION._getModalOptions('alert', msg);
		new MUI.Modal(options);		
	},

	information: function(msg, wOptions)
	{
		var options = ION._getModalOptions('information', msg);
		new MUI.Modal(options);		
	},

	/**
	 * Ionize generic form window
	 * Use to load a window which contains a form 
	 *
	 * @param	string		Window ID.
	 * @param	string		Window Form ID
	 * @param	string		Lang translation key or string as title of the window
	 * @param	string		URL called in case of form validation
	 * @param	object		Window extended options
	 *
	 */
	formWindow: function(id, form, title, wUrl, wOptions, data)
	{
		// Cleans URLs
		wUrl = ION.cleanUrl(wUrl);

		// Window options
		var options  = 
		{
			id: 'w' + id,
			title: (typeOf(Lang.get(title)) == 'null') ? title : Lang.get(title),
			container: document.body,
			content: {
				url: admin_url + wUrl,
				method:'post',
				data: data,
				onLoaded: function(element, content)
				{
					if ($(form))
					{
						// Get the form action URL and adds 'true' so the transport is set to XHR
						var formUrl = $(form).getProperty('action');

						// Set the form submit button action and send the DOM Element to update with the according URL
						ION.setFormSubmit(
							form,               // Form Object
							('bSave' + id),     // Save button ID
							formUrl,            // Save URL
							null,               // Confirmation Object (null in this case, no conf. to open one window)
							wOptions            // Options, to pass onSuccess() method
						);
					}

					// Add the cancel event if cancel button exists
					// All buttons name starts with 'b'
					if (bCancel = $('bCancel' + id))
					{
						bCancel.addEvent('click', function(e)
						{
							e.stop();
							MUI.get('w' + id).close();
						});
					}
					var instance = $('w' + id).retrieve('instance');
					this.fireEvent('loaded', instance);

					// Window resize
					if (typeOf(wOptions) != 'null' && wOptions.resize == true)
					{
						ION.windowResize(id);
					}
				}
			},
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff'			
		};

		// Extends the window options
		if (typeOf(wOptions) != 'null') { options =  Object.append(options, wOptions);}

		// Window creation
		return new MUI.Window(options);
	},


	/**
	 * Opens a data window, without buttons
	 * Usefull for editing a list
	 *
	 */
	dataWindow: function(id, title, wUrl, wOptions, data)
	{
		// Cleans URLs
		wUrl = ION.cleanUrl(wUrl);

		var options  = 
		{
			id: 'w' + id,
			title: (typeOf(Lang.get(title)) == 'null') ? title : Lang.get(title),
			container: document.body,
			// evalResponse: true,
			content: {
				url: admin_url + wUrl,
				data: data,
				method: 'post',
				onLoaded: function(element, content)
				{
					// Window resize
					if (typeOf(wOptions) != 'null' && wOptions.resize == true)
					{
						ION.windowResize(id);
					}
					var instance = $('w' + id).retrieve('instance');

					this.fireEvent('loaded', instance);
				}
			},
			width: 310,
			height: 130,
			y: 80,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff'
		};
		
		// Memorize and restore Size & Position
		if (wOptions && wOptions.memorizeSize == true)
		{
			if (Cookie.read('w' + id))
			{
				var cookie = new Hash.Cookie('w' + id, {duration: 365});
				
				wOptions =  Object.append(wOptions, 
				{
					'width': cookie.get('width'),
					'height': cookie.get('height'),
					'y': cookie.get('top'),
					'x': cookie.get('left')
				});
			}
	
			wOptions.onResize = function()
			{
				var cookie = new Hash.Cookie(this.windowEl.id, {duration: 365});
				cookie.erase();
				cookie.extend(this.windowEl.getCoordinates());
			}
		}

		// Extends the window options
		if (typeOf(wOptions) != 'null') { options = Object.append(options, wOptions);}
		
		// Window creation
		return new MUI.Window(options);
	},
	

	_getModalOptions: function(type, msg)
	{
		// Window message
		var wMsg = (Lang.get(msg)) ? Lang.get(msg) : msg ;
	
		var btnOk = new Element('button', {'class':'button yes right mr35'}).set('text', Lang.get('ionize_button_ok'));

		var button = new Element('div', {'class':'buttons'}).adopt(btnOk);


		// Message HTML Element & window content container		
		var wMessage = new Element('div', {'class':'message'}).set('html', wMsg);
		var wContent = new Element('div').adopt(wMessage, button);

		// Window options
		var id = new Date().getTime();
		var options = {
			id: 'w' + id,
			content: wContent,
			title: Lang.get('ionize_modal_' + type + '_title'),
			cssClass: type,
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }			
		}

		// Event on btn No : Simply close the window
		btnOk.addEvent('click', function() 
		{
			MUI.get('w' + id).close();
		}.bind(this));
		
		return options;
	},
	
	/**
	 * Returns the buttons yes/no HTMLDOMElement
	 *
	 * @param	string		Window ID (to link with the close button)
	 * @param	string		URL or Callback JS function to call if yes answer
	 * @param	string		Element to update after url completion
	 * @param	string		URL of the update element
	 *
	 */
	_getConfirmationButtons:  function(id, callback)
	{
		// Btn Yes / No creation
		var btnYes = new Element('button', {'class':'button yes right'}).set('text', Lang.get('ionize_button_confirm'));
		var btnNo = new Element('button', {'class':'button no right'}).set('text', Lang.get('ionize_button_cancel'));
	
		// Event on btn No : Simply close the window
		btnNo.addEvent('click', function() 
		{
			MUI.get('w' + id).close();
		}.bind(this));

		// Event on btn Yes
		btnYes.addEvent('click', function()
		{
			/*
			 * Check if callback is an  URL or a JS callback function
			 * No RegExp check on URL because some URL can be passed without "http://"
			 * if fact you wish to use a regexp : var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
			 * An URL is supposing containing "/"
			 * Case URL : 		Form sending
			 * Case Callback : 	Execution of callback function
			 *
			 */
			// URL case
			if ( (callback + '').indexOf('/') > -1 )
			{
				if (typeOf(callback) != 'function')
				{
					// Send the standard form object
					ION.sendForm(callback);
				}
				else
				{
					callback();
				}
			}
			// Callback case
			else
			{
				callback();
			}

			// Close the modal window
			MUI.get('w' + id).close();
			
		}.bind(this));
	
		// Buttons container
		return new Element('div', {'class':'buttons'}).adopt(btnYes, btnNo)
	},
	
	/**
	 * Resizes one window based on its content
	 *
	 * @param	String		Windows ID, without the Mocha prefix (w)
	 *
	 */
	windowResize: function(id, size, resize, centered )
	{
		var mps = $('mainPanel').getSize();
		var window = $('w' + id).retrieve('instance');
		var windowEl = $('w' + id).getElement('.mochaContent');

		// resize = (typeOf(resize) == 'null') ? 'both' : resize;
		// var resizeHeight = !!(resize == 'both' || resize == 'height');
		// var resizeWidth = !!(resize == 'both' || resize == 'width');
		var centered = !! (typeOf(centered) == 'null');

		// windows content size
		var cs = false;
		var gotSize = windowEl.getSize();

		if (typeOf(size) == 'object')
		{
			cs = {};
			if (typeOf(size.width) != 'null') { cs['x'] = size.width; } else { cs['x'] = gotSize.x };
			if (typeOf(size.height) != 'null') { cs['y'] = size.height; } else { cs['y'] = gotSize.y };
		}
		else
		{
			cs = gotSize;
		}

		if ((cs.y + 80) < mps.y)
		{
			window.resize({height: cs.y + 10, width: cs.x, centered:centered, top:70 });
		}
		else
		{
			window.resize({height: mps.y - 30, width: cs.x, centered:centered, top:70 });
		}
	
	},
	
	
	/**
	 * Closes one window
	 *
	 * @param	Mixed	Window DOM Element or String of Complete window ID
	 *
	 */
	closeWindow: function(id)
	{
		if (typeOf(id) == 'string')
			id = $(id);
	
		if (typeOf(id) == 'element')
		{
			MUI.get(id).close();
		}
	}
});