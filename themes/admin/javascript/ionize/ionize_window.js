
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
			padding:  { top: 15, right: 12, bottom: 5, left: 12 },
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
	confirmation: function(id, callback, msg, wOptions)
	{

		// Get the buttons container
		wButtons = ION._getConfirmationButtons(id, callback);

		// Window question message
		var wMsg = (Lang.get(msg)) ? Lang.get(msg) : msg ;

		// Message HTML Element & window content container		
		var wMessage = new Element('div', {'class':'message'}).set('text', wMsg);		// Message
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
					// Get the form action URL and adds 'true' so the transport is set to XHR
					var formUrl = $(form).getProperty('action');

					// Set the form submit button action and send the DOM Element to update with the according URL
					ION.setFormSubmit(form, ('bSave' + id), formUrl);

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
					
					// Event on save button
					/*
					if (bSave = $('bSave' + id))
					{
						bSave.addEvent('click', function(e)
						{
							e.stop();
							
							// closeFunc is needed for IE8
							var closeFunc = function()
							{
								MUI.get('w' + id).close();
							}
							closeFunc.delay(50);
						});
					}
					*/
					
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
		new MUI.Window(options);
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
//			evalResponse: true,
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