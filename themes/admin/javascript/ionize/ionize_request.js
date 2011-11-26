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
		if ( ! options.update)
			new Request.HTML(ION.getHTMLRequestOptions(url, data, options)).send();
		else
		{
			if (options.update)
			{
				if ($(options.update))
					new Request.HTML(ION.getHTMLRequestOptions(url, data, options)).send();
			}
		}
	},


	/**
	 * Create one Request event
	 *
	 * @param	HTMLDomElement		Dom Element on which add the 'click' event
	 * @param	String				URL of the request. Relative to http://domain.tld/admin/
	 * @param	Object				Data to send as POST
	 * @param	Object				Options object.
	 *								'confirm' : Boolean. true to open a confirmation window
	 *								'message' : String. The confirmation message
	 *
	 */
	initRequestEvent: function(item, url, data, options, mode)
	{
		var data = (typeOf(data) == 'null') ? {} : data;
		
		var mode = (typeOf(mode) == 'null') ? 'JSON' : mode;

		// Some safety before adding the event.
		item.removeEvents('click');

		item.addEvent('click', function(e)
		{
			e.stop();
			
			// Confirmation screen
			if (typeOf(options) != 'null' && options.confirm == true)
			{
				var message = (typeOf(options.message) != 'null') ? options.message : Lang.get('ionize_confirm_element_delete'); 
				
				// Callback request
				var callback = ION.JSON.pass([url,data]);

				if (mode == 'HTML')
					callback = ION.HTML.pass([url,data,options]);

				ION.confirmation('requestConfirm' + item.getProperty('rel'), callback, message);
			}
			else
			{
				if (mode == 'HTML')
					ION.HTML(url, data, options);
				else
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
	 * @param	String		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	Object		Form data to send as POST
	 * @param	Object		Options
	 *						'onSuccess' : Function to use as callback on success
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
				MUI.hideSpinner();

				onSuccess(responseJSON, responseText);
				
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
/*					if (responseJSON.message_type == 'error')
					{
						ION.error(responseJSON.message);
					}
					else
					{
*/
						ION.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
//					}
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
	 * @param	object		Options
	 *						'update' : DOM Element ID to update with the result
	 *						'append' : DOM Element ID to append the result to.
	 *						'onSuccess' : Function to use as callback after success
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
				MUI.hideSpinner();			

				onSuccess(responseTree, responseElements, responseHTML, responseJavaScript);
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
	
