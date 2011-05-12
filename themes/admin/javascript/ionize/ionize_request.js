ION.append({
	
	/**
	 * GLOBAL REQUEST FUNCTIONS
	 * Not to be used directly. Use initRequestEvent() instead to add one event to an element.
	 *
	 */
	JSON: function(url, data, options)
	{
		new Request.JSON(ION.getJSONRequestOptions(url, data, options)).send();
	},

	HTML: function(url, data, options)
	{
		new Request.HTML(ION.getHTMLRequestOptions(url, data, options)).send();
	},


	/**
	 * Create one Request event
	 *
	 */
	initRequestEvent: function(item, url, data, options)
	{
		var data = (typeOf(data) == 'null') ? {} : data;

		// Some safety before adding the event.
		item.removeEvents('click');

		item.addEvent('click', function(e)
		{
			e.stop();
			
			// Confirmation screen
			if (typeOf(options) != 'null' && typeOf(options.message) != 'null')
			{
				var message = (Lang.get(options.message)) ? Lang.get(options.message) : Lang.get('app_message_confirm'); 
				
				// Callback request
				var callback = ION.JSON.pass([url,data]);

				ION.confirmation('requestConfirm' + rel, callback, message);
			}
			else
			{
				ION.JSON(url, data);
			}
		});
	},
	
	
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
	getJSONRequestOptions: function(url, data, options)
	{
		if (!data) {
			data = '';
		}

		// Cleans URLs
		url = ION.cleanUrl(url);

		var onSuccess = function(responseJSON, responseText) {};
		if (typeOf(options) != 'null' && typeOf(options.onSuccess) != 'null') { onSuccess = options.onSuccess; }

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
				ION.notification('error', xhr.responseJSON);
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
					ION.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback)
				{
					ION.execCallbacks(responseJSON.callback);
				}
	
				// User notification
				if (responseJSON && responseJSON.message_type)
				{
					if (responseJSON.message_type == 'error')
					{
						ION.error(responseJSON.message);
					}
					else
					{
						ION.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
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
	getHTMLRequestOptions: function(url, data, options)
	{
		if (!data) {
			data = '';
		}

		var update = (typeOf(options) != 'null' && options.update) ? options.update : null;
		var append = (typeOf(options) != 'null' && options.append) ? options.append : null;

		var onSuccess = function(responseTree, responseElements, responseHTML, responseJavaScript) {};
		if (typeOf(options) != 'null' && typeOf(options.onSuccess) != 'null') { onSuccess = options.onSuccess; }

		// Cleans URLs
		url = ION.cleanUrl(url);

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
				ION.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
				onSuccess(responseTree, responseElements, responseHTML, responseJavaScript);
	
				MUI.hideSpinner();			
			}
		};
	},
	
	
	/**
	 * Execute the callbacks
	 *
	 * @param	Mixed.	Function name or array of functions.
	 *
	 */
	execCallbacks: function(args)
	{
		var callbacks = new Array();

		// More than one callback
		if (typeOf(args) == 'array') {
			callbacks = args;
		}
		else {
//			callbacks.include(args);
			callbacks.push(args);
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
	}
});
	
