initializeForms = function() 
{

	/**
	 * Returns the JSON Request options object
	 * 
	 * @usage : No direct use. Use MUI.JSON() instead.
	 * 
	 * NOTE : SHOULD REPLACE THE MUI.getFormObject() FUNCTION !!!
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 *
	 */
	MUI.getJSONRequestOptions = function(url, data, options)
	{
		if (!data) {
			data = '';
		}

		// Cleans URLs
		url = MUI.cleanUrl(url);

		var onSuccess = function(responseJSON, responseText) {};
		if ($type(options) != false && $type(options.onSuccess) != false) { onSuccess = options.onSuccess; }

		return {
			url: admin_url + url, 
			method: 'post',
			loadMethod: 'xhr',
			data: data,
			onRequest: function()
			{
				MUI.showSpinner();
			},
			onFailure: function(xhr) 
			{
				MUI.hideSpinner();

				// Error notification
				MUI.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseJSON, responseText)
			{
				onSuccess(responseJSON, responseText);
				
				MUI.hideSpinner();
				
				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update)
				{
					// Updates all the elements in the update array
					// look at init-content.js for more details
					MUI.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					MUI.execCallbacks(responseJSON.callback);
				}
	
				// User notification
				if (responseJSON && responseJSON.message_type)
				{
					if (responseJSON.message_type == 'error')
					{
						MUI.error(responseJSON.message);
					}
					else
					{
						MUI.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
					}
				}
			}
		};
	},


	/**
	 * Returns the HTML Request options object
	 * 
	 * @usage : No direct use. Use MUI.JSON() instead.
	 * 
	 * NOTE : SHOULD REPLACE THE MUI.getFormObject() FUNCTION !!!
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 * @param	object		Request options
	 *
	 */
	MUI.getHTMLRequestOptions = function(url, data, options)
	{
		if (!data) {
			data = '';
		}

		var update = ($type(options) != false && options.update) ? options.update : null;
		var append = ($type(options) != false && options.append) ? options.append : null;

		var onSuccess = function(responseTree, responseElements, responseHTML, responseJavaScript) {};
		if ($type(options) != false && $type(options.onSuccess) != false) { onSuccess = options.onSuccess; }

		// Cleans URLs
		url = MUI.cleanUrl(url);

		return {
			url: admin_url + url, 
			method: 'post',
			loadMethod: 'xhr',
			update: update,
			append: append,
			data: data,
			onRequest: function()
			{
				MUI.showSpinner();
			},
			onFailure: function(xhr) 
			{
				MUI.hideSpinner();

				// Error notification
				MUI.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
				onSuccess(responseTree, responseElements, responseHTML, responseJavaScript);
	
				MUI.hideSpinner();			
			}
		};
	},

	/**
	 * Returns the Ionize form object
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 */
	MUI.getFormObject = function(url, data)
	{
		if (!data) {
			data = '';
		}

		// Cleans URLs
		url = MUI.cleanUrl(url);

		return {
			url: admin_url + url, 
			method: 'post',
			loadMethod: 'xhr',
			data: data,
			onRequest: function()
			{
				MUI.showSpinner();
			},
			onFailure: function(xhr) 
			{
				MUI.hideSpinner();

				// Error notification
				MUI.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseJSON, responseText)
			{
				MUI.hideSpinner();
				
				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update)
				{
					// Updates all the elements in the update array
					// look at init-content.js for more details
					MUI.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					MUI.execCallbacks(responseJSON.callback);
				}
	
				// User notification
				if (responseJSON && responseJSON.message_type)
				{
					if (responseJSON.message_type == 'error')
					{
						MUI.error(responseJSON.message);
					}
					else
					{
						MUI.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
					}
				}
			}
		};
	}

	/**
	 * Get the associated form object and send it directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	MUI.sendForm = function(url)
	{
		MUI.updateRichTextEditors();

		new Request.JSON(MUI.getFormObject(url)).send();
	}

	/**
	 * Get the associated form object and send attached data directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	MUI.sendData = function(url, data)
	{
		MUI.updateRichTextEditors();

		new Request.JSON(MUI.getFormObject(url, data)).send();
	}


	/**
	 * Set an XHR action to a form and add click event to the given element
	 *
	 * @param	string	form ID
	 * @param	string	element on wich attach the action (ID)
	 * @param	string	action URL (with or without the base URL prefix)
	 * @param	object	Confirmation object	{message: 'The confirmation question'}
	 *
	 */
	MUI.setFormSubmit = function(form, button, url, confirm)
	{
		// Add the form submit event with a confirmation window
		if ($(button) && ($type(confirm) == 'object'))
		{
			var func = function()
			{
				MUI.showSpinner();

				var options = MUI.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			};
		
			// Form submit or button event
			$(button).addEvent('click', function(e)
			{
				new Event(e).stop();
				
				MUI.confirmation('conf' + button.id, func, confirm.message);
			});
		}
		// Add the form submit button event without confirmation
		else if ($(button))
		{
			// Form submit or button event
			$(button).addEvent('click', function(e)
			{
				new Event(e).stop();
				
				// Show spinner
				MUI.showSpinner();
				
				// tinyMCE and CKEditor trigerSave
				MUI.updateRichTextEditors();
				
				/*
				// mandatory for text save. See how to externalize without make it too complex.
				if (typeof tinyMCE != "undefined")
					tinyMCE.triggerSave();
				if (typeof CKEDITOR != "undefined")
				{
					for (instance in CKEDITOR.instances)
						CKEDITOR.instances[instance].updateElement();
				}
				*/
				
				// Get the form
				var options = MUI.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			});
		}
	}


	/**
	 * CTRL+s or Meta+s save event
	 *
	 */
	MUI.addFormSaveEvent = function(button)
	{
		if ($(button))
		{
			// Remove all existing Ctrl+S Save Event
			$(document).removeEvents('keydown');
			
			// Add new keydown 
			$(document).addEvent('keydown', function(event)
			{
				if((event.control || event.meta) && event.key == 's')
				{
					event.stop();
					if ($(button))
					{
						$(button).fireEvent('click', event);
					}
				}
			});
		}
	}
	
	
	/**
	 * Execute the callbacks
	 *
	 * @param	Mixed.	Function name or array of functions.
	 *
	 */
	MUI.execCallbacks = function(args)
	{
		var callbacks = new Array();
		
		// More than one callback
		if ($type(args) == 'array') {
			callbacks = args;
		}
		else {
			callbacks.push(args)	
		}
	
		callbacks.each(function(item, idx)
		{
			var cb = (item.fn).split(".");
			var func = null;
			var obj = null;
			
			if (cb.length > 1) {
				obj = window[cb[0]];
				func = obj[cb[1]];
			}
			else {
				func = window[cb];
			}
			func.delay(100, obj, item.args);
		});
	
	},
	
	MUI.updateRichTextEditors = function()
	{
		if (typeof tinyMCE != "undefined")
		{
			(tinyMCE.editors).each(function(tiny)
			{
				tiny.save();
			});
		}

		if (typeof CKEDITOR != "undefined")
		{
			for (instance in CKEDITOR.instances)
				CKEDITOR.instances[instance].updateElement();
		}
	}
	
	
	MUI.myChain.callChain();

}
