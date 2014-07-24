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

	cancelSaveWarning:function()
	{
		ION.hasUnsavedData = false;
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
	setFormSubmit: function(form, button, url, confirm, options)
	{
		if (typeOf($(form))!='null' && typeOf($(button)) != 'null')
		{
			// Create the Validator if it doesn't exists
			// Create it first in views or JS classes if needed, to have only one instance
			if (! $(form).retrieve('validator'))
			{
				// Name of the validator : Standard ionize !
				$(form).store(
					'validator',
					new Form.Validator.Inline(form, {
						errorPrefix: '',
						showError: function(element) {
							element.show();
						}
					})
				);
			}

			// Warning if changed but not saved
			ION.initSaveWarning(form);

			// Stores the button in the form
			$(form).store('submit', $(button));

			// Confirmation
			if ($(button) && (typeOf(confirm) == 'object'))
			{
				$(button).enabled = true;
				var func = function()
				{
					var requestOptions = ION.getJSONRequestOptions(url, $(form), options);
					var r = new Request.JSON(requestOptions);
					r.send();
				};

				// Form submit or button event
				$(button).removeEvents('click');
				$(button).addEvent('click', function(e)
				{
					if (typeOf(e) != 'null') e.stop();

					var validator = $(form).retrieve('validator');

					if (validator && ! validator.validate())
					{
						new ION.Notify(form, {type:'error'}).show('ionize_message_form_validation_please_correct');
					}
					else
					{
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
					}
				});
			}
			// No confirmation
			else if ($(button))
			{
				// Form submit or button event
				$(button).enabled = true;
				$(button).removeEvents('click');
				$(button).addEvent('click', function(e)
				{
					if (typeOf(e) != 'null') e.stop();

					var validator = $(form).retrieve('validator');

					if (validator && ! validator.validate())
					{
						new ION.Notify(form, {type:'error'}).show('ionize_message_form_validation_please_correct');
					}
					else
					{
						// Cancel the save warning (content changed)
						ION.cancelSaveWarning();

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

							// tinyMCE and CKEditor trigerSave
							ION.updateRichTextEditors();

							// Get the form
							var requestOptions = ION.getJSONRequestOptions(url, $(form), options);

							var r = new Request.JSON(requestOptions);
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
			console.log('ION.setFormSubmit() error : The form "' + form + '" or the button "' + button + '" do not exist.');
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
	},

	getFormFieldContainer: function(options)
	{
		var cl = options.class ? ' ' + options.class : '';
		var label = options.label ? options.label : '';

		var dl = new Element('dl', {
			'class':'small' + cl
		});
		var dt = new Element('dt', {
			'class':''
		}).inject(dl);

		new Element('label', {text: label}).inject(dt);

		var dd = new Element('dd', {
			'class':''
		}).inject(dl);

		return dl;
		/*
		 <!-- Ordering : First or last (or Element one if exists ) -->
		 <?php if( empty($id_item)) :?>
		 <dl class="small mb10">
		 <dt>
		 <label for="ordering"><?php echo lang('ionize_label_ordering'); ?></label>
		 </dt>
		 <dd>
		 <select name="ordering" id="ordering<?php echo $id_item; ?>" class="select">
		 <?php if( ! empty($id_item)) :?>
		 <option value="<?php echo $ordering; ?>"><?php echo $ordering; ?></option>
		 <?php endif ;?>
		 <option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
		 <option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
		 </select>
		 </dd>
		 </dl>
		 <?php endif ;?>

		 */

	}

});

ION.FormField = new Class({

	Implements: [Events, Options],

	options: {
		/*
		 * User's options :
		 *
		 container: '',         // Container ID or container DOM Element
		 class: '',             // DL class
		 label: {
		    text: '',           // label text
		    class: ''           // label class
		 },
		 help: '',              // Help String
		 */
	},

	initialize: function(options)
	{
		this.dl = new Element('dl');
		if (options.class) this.dl.setProperty('class', options.class);

		var dt = new Element('dt').inject(this.dl);

		// Label
		if (options.label)
		{
			this.label = new Element('label', {text: options.label.text}).inject(dt);
			if (options.label.class) this.label.setProperty('class', options.label.class);
			if (options.label.for) this.label.setProperty('for', options.label.for);
			if (options.help) this.label.setProperty('title', options.help);
		}
		this.fieldContainer = new Element('dd').inject(this.dl);

		if (options.container)
		{
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;
			this.dl.inject(this.container);
		}

		return this;
	},

	adopt: function(field)
	{
		this.fieldContainer.adopt(field);
	},

	getContainer: function()
	{
		return this.fieldContainer;
	},

	getDomElement: function()
	{
		return this.dl;
	},

	getLabel: function()
	{
		return this.label;
	},

	hide: function()
	{
		this.dl.hide();
	},

	show: function()
	{
		this.dl.show();
	}
});

ION.Form = {};
ION.Form.Select = new Class({

	Implements: [Events, Options],

	options:
	{
		container:  null,           // Container ID or container DOM Element
		'class':    'inputtext',    // CSS class,

		name:       '',             // Name of the Select
		id:         '',             // ID of the select

		post:       {},             // Data to post to the URL
		data:       null,           // JSON array to use as data
		url :       null,           // URL to use as data source
		ignore_keys: [],			// Keys to ignore when building the select options

		key:        null,			// Key of the data array to use as value
		label:      null,           // Key of the data array to use as label
		selected:	[],				// Selected Value or array of Selected Values

		firstValue: null,			// First manually added value
		firstLabel: null,			// First manually added label

		fireOnInit: false,			// Fires the onChange event after init.

		rule:       null            // @todo. Rule to apply to the select

		// onDraw: 			function(this, DOMElement select)
		// onChange: 		function(value, data, selected, this)
		// onOptionDraw: 	function(option, row)					// Fired when one option element was drawn
	},

	initialize: function(options)
	{
		this.setOptions(options);

		var self = this,
			o = this.options
		;

		// Select
		// if (typeOf(options['class'] != 'null') && options['class'].contains('inputtext') == false) o['class'] += ' inputtext';
		this.select = new Element('select', {name: o.name, 'class':o.class});
		if (o.id != '')	this.select.setProperty('id', o.id);

		// this.setOptions() remove the functions from the options
		// We need to get access to them through the original options object
		if (options.onChange)
		{
			this.select.addEvent('change', function()
			{
				var data = this.getSelected().retrieve('data');
				if (typeOf(data) == 'array') data = data[0];
				// Store the selected value (in case of options refresh)
				self.options.selected = this.value;
				options.onChange(this.value, data, this.getSelected(), self.select, self);
			});
		}

		// Container : If set, the select will be injected in this container
		if (o.container)
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		// Get data from one URL
		// One JSON array is expected as result
		if (o.url)
		{
			this.getOptionsFromUrl();
		}
		else if (Object.getLength(o.data) > 0)
		{
			this.buildOptions(o.data);
		}

		return this;
	},

	onDraw: function()
	{
		// Inject the select into the container
		if (this.container)
			this.select.inject(this.container);

		// onDraw Event gts fired
		this.fireEvent('onDraw', [this.select, this]);
	},

	getOptionsFromUrl: function()
	{
		var self = this;

		ION.JSON(
			this.options.url,
			this.options.post,
			{
				onSuccess: function(json)
				{
					self.buildOptions(json);
				}
			}
		);
	},

	/**
	 *
	 * @param options {
	 * 			data:	If set, will be used to feed the select
	 * 			url:	If set, will be used to feed the select
	 * 		  }
	 */
	refresh: function(options)
	{
		if (typeOf(options.data) != 'null')
		{
			this.buildOptions(options.data)
		}
		else if (typeOf(options.url) != 'null')
		{
			this.getOptionsFromUrl();
		}
	},


	buildOptions: function(data)
	{
		var self = this,
			o = this.options,
			key = o.key,
			lab = o.label,
			selected = o.selected && typeOf(o.selected) != 'array' ? [o.selected] : [],
			selectedIndex = Object.getLength(selected) == 0 ? o.selectedIndex : null
		;

		self.select.empty();

		// Has the select one first value (usually '-- Select something --')
		if (o.firstLabel != null && o.firstValue != null)
		{
			new Element('option', {'value': o.firstValue}).set(
				'html',
				o.firstLabel
			).inject(this.select);
		}


		Array.each(data, function(row, idx)
		{
			var value = typeOf(row[key]) != 'null' ? row[key] : row;
			var label = typeOf(row[lab]) != 'null' ? row[lab] : (value != null ? value : '');

			if (value != null && !o.ignore_keys.contains(value))
			{
				var opt = new Element('option', {
					value: value,
					text: label
				}).inject(self.select);

				if (selected.contains(value) || (selectedIndex && selectedIndex == idx))
					opt.setProperty('selected', 'selected');

				// Stores the data used to build the option
				// Can be retrieved with : opt.retrieve('data');
				opt.store('data', row);

				self.fireEvent('onOptionDraw', [opt, row]);
			}
		});

		// Fires "change" on init
		if (o.fireOnInit)
			this.select.fireEvent('change');

		this.onDraw();
	},

	getDomElement: function()
	{
		return this.select;
	},

	getSelected: function()
	{
		return this.select.getSelected();
	},


	selectValue: function(val)
	{
		var options = this.select.getElements('option');

		options.each(function(opt)
		{
			opt.removeProperty('selected');

			if (opt.value == val)
				opt.setProperty('selected', 'selected');
		});

		this.select.fireEvent('change');
	},

	selectLabel: function(text)
	{
		var options = this.select.getElements('option');

		options.each(function(opt)
		{
			opt.removeProperty('selected');

			if (opt.get('text') == text)
				opt.setProperty('selected', 'selected');
		});

		this.select.fireEvent('change');
	},

	destroy: function()
	{
		this.getDomElement().destroy();
		delete(this);
		return null;
	}

});

