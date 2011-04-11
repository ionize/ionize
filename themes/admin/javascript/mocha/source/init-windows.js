var initializeWindows = function(){


	MUI.hideSpinner = function()
	{
		if ($('spinner')) $('spinner').hide();
	}

	MUI.showSpinner = function()
	{
		if ($('spinner')) $('spinner').show();
	}

	/**
	 * Ionize notification window
	 * Launch a notification window creation
	 *
	 * @param	string 	type of notification. Can be : error, notice, success
	 * @param	string	Notification message
	 */
	MUI.notification = function(type, message)
	{
		new MUI.Window({
			loadMethod: 'html',
			closeAfter: 2500,
			type: 'notification',
			addClass: 'notification ',
			content: '<div class="'+ type +'">' + message + '</div>',
			width: 350,
			height: 50,
			y: 1,
			padding:  { top: 15, right: 12, bottom: 10, left: 12 },
			shadowBlur: 5,
			bodyBgColor: [250, 250, 250],
			contentBgColor: '#e5e5e5'
		});
	}
	
	/**
	 * Ionize Add Confirmation modal window
	 *
	 * @param	string		Window ID
	 * @param	string		Button or any element ID on wich add the link
	 * @param	string		URL or JS function called in case of user confirmation
	 * @param	string		Element name to update after the request success
	 *
	 */
	MUI.addConfirmation = function(id, button, callback, msg, options)
	{
		$(button).addEvent('click', function(e)
		{
			var e = new Event(e).stop();
			MUI.confirmation(id, callback, msg, options);
		});	
	}

	/**
	 * Ionize Confirmation modal window
	 * Opens a windows with yes / no buttons
	 *
	 * @param	string		Window ID
	 * @param	string		URL or Callback JS function to call if yes answer
	 * @param	string		Message
	 * @param	object		Window extended options
	 *
	 */
	MUI.confirmation = function(id, callback, msg, wOptions)
	{
		// Get the buttons container
		wButtons = MUI._getConfirmationButtons(id, callback);

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
			addClass:'confirmation',
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }			
		}

		// Extends the window options
		if (wOptions) {$extend(options, wOptions);}
		
		// Open the confirmation modal window
		new MUI.Modal(options);
	}

	/**
	 * Modal windows
	 *
	 */
	MUI.error = function(msg, wOptions)
	{
		var options = MUI._getModalOptions('error', msg);
		new MUI.Modal(options);		
	}

	MUI.alert = function(msg, wOptions)
	{
		var options = MUI._getModalOptions('alert', msg);
		new MUI.Modal(options);		
	}

	MUI.information = function(msg, wOptions)
	{
		var options = MUI._getModalOptions('information', msg);
		new MUI.Modal(options);		
	}

	
	/**
	 * Ionize generic form window
	 * Use to load a window which contains a form 
	 *
	 * @param	string		Window ID
	 * @param	string		Window Form ID
	 * @param	string		Lang translation key or string as title of the window
	 * @param	string		URL called in case of form validation
	 * @param	object		Window extended options
	 *
	 */
	MUI.formWindow = function(id, form, title, wUrl, wOptions, data)
	{
		// Cleans URLs
		wUrl = MUI.cleanUrl(wUrl);

		var options  = 
		{
			id: 'w' + id,
			title: ($type(Lang.get(title)) == '') ? title : Lang.get(title),
			loadMethod: 'xhr',
			method:'post',
			data: data,
			contentURL: admin_url + wUrl,
			onContentLoaded: function(c)
			{
				// Get the form action URL and adds 'true' so the transport is set to XHR
				var formUrl = $(form).getProperty('action');

				// Set the form submit button action and send the DOM Element to update with the according URL
				MUI.setFormSubmit(form, ('bSave' + id), formUrl);

				// Add the cancel event if cancel button exists
				// All buttons name starts with 'b'
				if (bCancel = $('bCancel' + id))
				{
					bCancel.addEvent('click', function(e)
					{
						var e = new Event(e).stop();
						MUI.closeWindow($('w' + id));
					});
				}
				
				// Event on save button
				if (bSave = $('bSave' + id))
				{
					bSave.addEvent('click', function(e)
					{
						var e = new Event(e).stop();
						
						// closeFunc is needed for IE8
						var closeFunc = function()
						{
							MUI.closeWindow($('w' + id));
						}
						closeFunc.delay(50);
					});
				}
				
				// Window resize
				if ($type(wOptions) != false && wOptions.resize == true)
				{
					MUI.windowResize(id);
				}
			},
			y: 70,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff'			
		};
		
		// Extends the window options
		if ($type(wOptions) != false) {$extend(options, wOptions);}
		
		// Window creation
		new MUI.Window(options);
	}

	/**
	 * Opens a data window, without buttons
	 * Usefull for editing a list
	 *
	 */
	MUI.dataWindow = function(id, title, wUrl, wOptions, data)
	{
		// Cleans URLs
		wUrl = MUI.cleanUrl(wUrl);

		var options  = 
		{
			id: 'w' + id,
			title: (typeof(Lang.get(title)) == 'undefined') ? title : Lang.get(title),
			loadMethod: 'xhr',
			contentURL: admin_url + wUrl,
			method:'post',
			data: data,
			evalResponse: true,
			width: 310,
			height: 130,
			y: 80,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff',
			onContentLoaded: function(c)
			{
				// Window resize
				if ($type(wOptions) != false && wOptions.resize == true)
				{
					MUI.windowResize(id);
				}
			}			
		};
		
		// Memorize and restore Size & Position
		if (wOptions && wOptions.memorizeSize == true)
		{
			if (Cookie.read('w' + id))
			{
				var cookie = new Hash.Cookie('w' + id, {duration: 365});
				
				$extend(wOptions, 
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
		if (wOptions) {$extend(options, wOptions);}
		
		// Window creation
		return new MUI.Window(options);
	}
	

	MUI._getModalOptions = function(type, msg)
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
			addClass: type,
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }			
		}

		// Event on btn No : Simply close the window
		btnOk.addEvent('click', function() 
		{
			MUI.closeWindow($('w' + id));
		}.bind(this));
		
		return options;
	}
	
	/**
	 * Returns the buttons yes / no HTMLDOMElement
	 *
	 * @param	string		Window ID (to link with the close button)
	 * @param	string		URL or Callback JS function to call if yes answer
	 * @param	string		Element to update after url completion
	 * @param	string		URL of the update element
	 *
	 */
	MUI._getConfirmationButtons = function(id, callback)
	{
		// Btn Yes / No creation
		var btnYes = new Element('button', {'class':'button yes right mr35'}).set('text', Lang.get('ionize_button_confirm'));
		var btnNo = new Element('button', {'class':'button no '}).set('text', Lang.get('ionize_button_cancel'));
	
		// Event on btn No : Simply close the window
		btnNo.addEvent('click', function() 
		{
			MUI.closeWindow($('w' + id));
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
				if ($type(callback) != 'function')
				{
					// Send the standard form object
					MUI.sendForm(callback);
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
			MUI.closeWindow($('w' + id));
			
			
		}.bind(this));
	
		// Buttons container
		return new Element('div', {'class':'buttons'}).adopt(btnYes, btnNo)
	}
	
	/**
	 * Resizes one window based on its content
	 *
	 * @param	String		Windows ID, without the Mocha prefix (w)
	 *
	 */
	MUI.windowResize = function(id, size)
	{
		// windows content size
		var cs = false;
		var gotSize = $('w' + id + '_content').getSize();

		if ($type(size) =='object')
		{
			cs = {};
			if ($type(size.width) != false) { cs['x'] = size.width; } else { cs['x'] = gotSize.x };
			if ($type(size.height) != false) { cs['y'] = size.height; } else { cs['y'] = gotSize.y };
		}
		else
		{
			cs = gotSize;
		}
		
		// main panel content size
		var mps = $('mainPanel').getSize();

		if ((cs.y + 80) < mps.y)
		{
//			setTimeout(function()
//			{
				$('w' + id).retrieve('instance').resize({height: cs.y + 10, width: cs.x, centered:false, top:70 });
//			}, 70);
		}
		else
		{
//			setTimeout(function()
//			{
				$('w' + id).retrieve('instance').resize({height: mps.y - 30, width: cs.x, centered:false, top:70 });						
//			}, 70);
		}
	
	}
	
	MUI.myChain.callChain();
}