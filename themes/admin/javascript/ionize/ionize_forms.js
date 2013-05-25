ION.append({

	hasUnsavedData: false,

	setUnsavedData:function()
	{
		ION.hasUnsavedData = true;
	},

	initSaveWarning:function(form)
	{
		ION.hasUnsavedData = false;
		var formInputs = $(form).getElements('input');
		formInputs.append($(form).getElements('textarea'));
		formInputs.addEvent('change', function(event)
		{
			ION.hasUnsavedData = true;
		});
	},


	/**
	 * Get the associated form object and send it directly
	 *
	 * @param	string		URL to send the form data
	 * @param	string		Element to update
	 * @param	string		Element update URL
	 */
	sendForm: function(url, form)
	{
		if (!form) {
			form = '';
		}
		else {
			form = $(form);
		}
		ION.updateRichTextEditors();

		new Request.JSON(ION.getJSONRequestOptions(url, form)).send();
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

		new Request.JSON(ION.getJSONRequestOptions(url, data)).send();
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
		if (typeOf($(form))!='null' && typeOf($(button)) != 'null')
		{
			// Form Validation
			var fv = new Form.Validator.Inline(form, {
				errorPrefix: '',
				showError: function(element) {
					element.show();
				}
			});

			// Warning if changed but not saved
			ION.initSaveWarning(form);

			// Stores the button in the form
			$(form).store('submit', $(button));

			// Add the form submit event with a confirmation window
			if ($(button) && (typeOf(confirm) == 'object'))
			{
				$(button).enabled = true;
				var func = function()
				{
					var options = ION.getJSONRequestOptions(url, $(form));
					var r = new Request.JSON(options);
					r.send();
				};

				// Form submit or button event
				$(button).removeEvents('click');
				$(button).addEvent('click', function(e)
				{
					if (typeOf(e) != 'null') e.stop();
					if (this.enabled)
					{
						this.enabled=false;
						$(button).addClass('disabled');
						(function(){
							this.enabled = true;
							this.removeClass('disabled');
						}).delay(4000, this);

						ION.confirmation('conf' + button.id, func, confirm.message);
					}
				});
			}
			// Add the form submit button event without confirmation
			else if ($(button))
			{
				// Form submit or button event
				$(button).enabled = true;
				$(button).removeEvents('click');
				$(button).addEvent('click', function(e)
				{
					if (typeOf(e) != 'null') e.stop();

					// Disable the button for x seconds.
					if (this.enabled)
					{
						this.enabled=false;
						$(button).addClass('disabled');
						(function(){
							this.enabled = true;
							this.removeClass('disabled');
						}).delay(4000, this);


						var parent = $(form).getParent('.mocha');
						var result = fv.validate();

						if ( ! result)
						{
							new ION.Notify(parent, {type:'error'}).show('ionize_message_form_validation_please_correct');
						}
						else
						{
							// tinyMCE and CKEditor trigerSave
							ION.updateRichTextEditors();

							// Get the form
							var options = ION.getJSONRequestOptions(url, $(form));

							var r = new Request.JSON(options);
							r.send();

							// Close the window
							if (typeOf(parent) != 'null')
								parent.close();
						}
					}
				});
			}
		}
		else
		{
			// console.log('ION.setFormSubmit() error : The form "' + form + '" or the button "' + button + '" do not exist.');
		}
	},

	setChangeSubmit: function(form, button, url, confirm)
	{
		// Add the form submit event with a confirmation window
		if ($(button) && (typeOf(confirm) == 'object'))
		{
			var func = function()
			{
				var options = ION.getJSONRequestOptions(url, $(form));

				var r = new Request.JSON(options);
				r.send();
			};
		
			// Form submit or button event
			$(button).removeEvents('change');
			$(button).addEvent('change', function(e)
			{
				e.stop();
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
				e.stop();
				
				// tinyMCE and CKEditor trigerSave
				ION.updateRichTextEditors();
				
				// Get the form
				var options = ION.getJSONRequestOptions(url, $(form));

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