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
		if (typeOf(options) == 'null' && typeOf(options.update) == 'null')
		{
			ION.notification('error', 'No "update" HTML Element in your request');
		}
		else
		{
			var doRequest = true;

			// Do not do the request if the element does not exists
			var update = (typeOf(options) != 'null' && options.update) ? options.update : null;
			if ( update != null && ! $(update))
				doRequest = false;

			// Do not do the request if the element does not exists
			var append = (typeOf(options) != 'null' && options.append) ? options.append : null;
			if ( append != null && ! $(append))
				doRequest = false;

			if (doRequest)
				new Request.HTML(ION.getHTMLRequestOptions(url, data, options)).send();
		}
	},


	/**
	 * Create one Request event
	 *
	 * @param item          Dom Element on which add the 'click' event
	 * @param url           URL of the request. Relative to http://domain.tld/admin/
	 * @param data          Data to send as POST
	 * @param options       Options object.
	 *								'confirm' : Boolean. true to open a confirmation window
	 *								'message' : String. The confirmation message
	 * @param mode          'HTML' or 'JSON'. Default 'JSON'
	 *
	 */
	initRequestEvent: function(item, url, data, options, mode)
	{
		var data = (typeOf(data) == 'null') ? {} : data;
		
		var mode = (typeOf(mode) == 'null') ? 'JSON' : mode;

		// Some safety before adding the event.
		if (typeOf(item) == 'element')
		{
			item.removeEvents('click');

			item.addEvent('click', function(e)
			{
				e.stop();

				// Confirmation screen
				if (typeOf(options) != 'null' && options.confirm == true)
				{
					var message = (typeOf(options.message) != 'null') ? options.message : Lang.get('ionize_confirm_element_delete');

					// Callback request
					var callback = ION.JSON.pass([url,data,options]);

					if (mode == 'HTML')
						callback = ION.HTML.pass([url,data,options]);

					ION.confirmation('requestConfirm' + item.getProperty('data-id'), callback, message);
				}
				else
				{
					if (mode == 'HTML')
						ION.HTML(url, data, options);
					else
						ION.JSON(url, data, options);
				}
			});
		}
	},


	initInputChange: function(selectors, updateSelectors)
	{
		$$(selectors).each(function(item, idx)
		{
			var id = item.getProperty('data-id');

			// input name
			var name = item.getProperty('data-name');

			var url = item.getProperty('data-url');

			var input = new Element('input', {'id':id, 'type': 'text', 'class':'inputtext', 'name':name, 'value': item.get('text')});
			input.addEvent('keypress', function(event)
			{
				if (event.key == 'enter')
				{
					event.stop();
					input.fireEvent('blur');
					return false;
				}
			});

			var post = {};

			Array.each(item.attributes, function(item, idx)
			{
				if ((item.name).substring(0,4) == 'data')
					post[(item.name).substring(5)] = item.value;
			});
			post['selector'] = updateSelectors;

			input.addEvent('blur', function(e)
			{
				var value = (input.value).trim();
				if (value != '' && value != item.get('text'))
				{
					post.value = value;
					item.set('text', value);
					ION.sendData(url, post);
				}
				input.setProperty('value', value);
				input.hide();
				item.show();
			});

			input.inject(item, 'before').hide();

			item.addEvent('click', function(e)
			{
				input.show().focus();
				item.hide();
			});
		});
	},
	
	/**
	 * Returns the JSON Request options object
	 * 
	 * @usage : No direct use. Use ION.JSON() instead.
	 * 
	 * @param	String		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	Object		Form data to send as POST
	 * @param	Object		Options
	 *						'onSuccess' : Function to use as callback on success
	 *
	 */
	getJSONRequestOptions: function(url, data, options)
	{
		if (typeOf(data) == 'null') data = {};

		// Cleans URLs
		url = ION.cleanUrl(url);

		var onRequest = function() {};
		var onSuccess = function(responseJSON, responseText) {};

		if (typeOf(options) != 'object') options = {};
		if (typeOf(options.onRequest) != 'null') { onRequest = options.onRequest; }
		if (typeOf(options.onSuccess) != 'null') { onSuccess = options.onSuccess; }

		Object.append(
			options,
			{
				url: admin_url + url,
				method: 'post',
				loadMethod: 'xhr',
				data: data,
				onRequest: function()
				{
					MUI.showSpinner();
					onRequest();
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
					if (responseJSON && typeOf(responseJSON.message_type) != 'null')
					{
						if (responseJSON.message_type != '')
							ION.notification.delay(50, MUI, new Array(responseJSON.message_type, responseJSON.message));
					}
				}
			}
		);

        return options;
	},


	/**
	 * Returns the HTML Request options object
	 * 
	 * @usage : No direct use. Use ION.HTML() instead.
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
			callbacks.push(args);
		}
		
		callbacks.each(function(item, idx)
		{
			var cb = (item.fn).split(".");
			var func = null;
			var obj = window[cb.shift()];

			// Find the func
			func = obj;
			Array.each(cb, function(item) {
				func = func[item];
			});

			if (func)
				(func).delay(100, obj, item.args);
			else
				console.log('ERROR : The function ' + item.fn + ' does not exists');
		});
	}
});
	
