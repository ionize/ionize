ION.append({

	/**
	 * Returns the Ionize form object
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 */
	getFormObject: function(url, data)
	{
		if (!data) {
			data = '';
		}

		// Cleans URLs
		url = ION.cleanUrl(url);

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
	 * Get the associated form object and send it directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	sendForm: function(url)
	{
		ION.updateRichTextEditors();

		new Request.JSON(ION.getFormObject(url)).send();
	},

	/**
	 * Get the associated form object and send attached data directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	sendData: function(url, data)
	{
		ION.updateRichTextEditors();

		new Request.JSON(ION.getFormObject(url, data)).send();
	},


	/**
	 * Set an XHR action to a form and add click event to the given element
	 *
	 * @param	string	form ID
	 * @param	string	element on wich attach the action (ID)
	 * @param	string	action URL (with or without the base URL prefix)
	 * @param	object	Confirmation object	{message: 'The confirmation question'}
	 *
	 */
	setFormSubmit: function(form, button, url, confirm)
	{
		// Add the form submit event with a confirmation window
		if ($(button) && (typeOf(confirm) == 'object'))
		{
			var func = function()
			{
				var options = ION.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			};
		
			// Form submit or button event
			$(button).removeEvents('click');
			$(button).addEvent('click', function(e)
			{
				new Event(e).stop();
				
				ION.confirmation('conf' + button.id, func, confirm.message);
			});
		}
		// Add the form submit button event without confirmation
		else if ($(button))
		{
			// Form submit or button event
			$(button).removeEvents('click');
			$(button).addEvent('click', function(e)
			{
				new Event(e).stop();
				
				// tinyMCE and CKEditor trigerSave
				ION.updateRichTextEditors();
				
				// Get the form
				var options = ION.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			});
		}
	},


	setChangeSubmit: function(form, button, url, confirm)
	{
		// Add the form submit event with a confirmation window
		if ($(button) && (typeOf(confirm) == 'object'))
		{
			var func = function()
			{
				var options = ION.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			};
		
			// Form submit or button event
			$(button).removeEvents('change');
			$(button).addEvent('change', function(e)
			{
				new Event(e).stop();
				
				ION.confirmation('conf' + button.id, func, confirm.message);
			});
		}
		// Add the form submit button event without confirmation
		else if ($(button))
		{
			// Form submit or button event
			$(button).removeEvents('change');
			$(button).addEvent('change', function(e)
			{
				new Event(e).stop();
				
				// tinyMCE and CKEditor trigerSave
				ION.updateRichTextEditors();
				
				// Get the form
				var options = ION.getFormObject(url, $(form));
				
				var r = new Request.JSON(options);
				
				r.send();
			});
		}
	},


	/**
	 * CTRL+s or Meta+s save event
	 *
	 */
	addFormSaveEvent: function(button)
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
	},
	
	updateRichTextEditors: function()
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
	},
	
	
	/**
	 * Cleans all the inputs (input + textarea) from a givve form
	 *
	 */
	clearFormInput: function(args)
	{
		// Inputs and textareas : .inputtext
		$$('#' + args.form + ' .inputtext').each(function(item, idx)
		{
			item.setProperty('value', '');
			item.set('text', '');
		});
		
		// Checkboxes : .inputcheckbox
		$$('#' + args.form + ' .inputcheckbox').each(function(item, idx)
		{
			item.removeProperty('checked');
		});
	}

});