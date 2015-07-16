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
	 * @param	{String}	url		URL to send the form data
	 * @param	{String}	form	Element to update
	 */
	sendForm: function(url, form)
	{
		form = !form ? '' : $(form);

		ION.updateRichTextEditors();

		new Request.JSON(ION.getJSONRequestOptions(url, form)).send();
	},

	/**
	 * Get the associated form object and send attached data directly
	 *
	 * @param	{String}	url		URL to send the form data
	 * @param	{String}	data	Element to update
	 */
	sendData: function(url, data)
	{
		ION.updateRichTextEditors();

		new Request.JSON(ION.getJSONRequestOptions(url, data)).send();
	},


	/**
	 * Set an XHR action to a form and add click event to the given element
	 *
	 * @param	{String}	form ID
	 * @param	{String}	button		element on which attach the action (ID)
	 * @param	{String}	url			action URL (with or without the base URL prefix)
	 * @param	{Object}	confirm		Confirmation object	{message: 'The confirmation question'}
	 * @param	{Object}	options
	 *{
	} */
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

					if (typeOf(options.onBeforeSaveClose) == 'function')
						options.onBeforeSaveClose($(form));

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

					if (options && typeOf(options.onBeforeSaveClose) == 'function')
						options.onBeforeSaveClose($(form));

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
		multiple:	false,
		multiple_max_size: 8,		// Multiple max size
		multiple_size: null,		// Multiple size

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
		this.select = new Element('select', {name: o.name, 'class': o['class']});
		if (o.id != '')	this.select.setProperty('id', o.id);

		if (o.multiple) this.select.setProperty('multiple', 'multiple');

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

		if (this.container && o.empty)
			this.container.empty();

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
			o = this.options
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

		Object.each(data, function(row, idx)
		{
			// Group
			if (typeOf(row) == 'array')
			{
				self.createOptionGroup(idx, row);
			}
			else
			{
				self.createOption(row);
			}
		});

		if (o.multiple && data)
		{
			if (o.multiple_size != null)
				this.select.setProperty('size', o.multiple_size);
			else
			{
				var nb = Object.getLength(data);
				if (nb < o.multiple_max_size)
					this.select.setProperty('size', nb);
				else
					this.select.setProperty('size', o.multiple_max_size)
			}
		}

		// Fires "change" on init
		if (o.fireOnInit) this.select.fireEvent('change');

		this.onDraw();
	},

	createOption: function(row)
	{
		var self = this,
			o = this.options,
			key = o.key,
			lab = o.label,
			selected = o.selected && typeOf(o.selected) != 'array' ? [o.selected.toString()] : o.selected,
			selectedIndex = (selected && Object.getLength(selected) == 0) ? o.selectedIndex : null,
			container = typeOf(arguments[1]) != 'null' ? arguments[1] : self.select
		;

		var value = typeOf(row[key]) != 'null' ? row[key] : row;
		var label = '';

		// Function Label
		if (typeOf(lab) == 'function')
			label = lab(row);
		else
			label = typeOf(row[lab]) != 'null' ? row[lab] : (value != null ? value : '');

		if (value != null && ! o.ignore_keys.contains(value))
		{
			var opt = new Element('option', {
				value: value,
				text: label
			}).inject(container);

			var to_select = false;

			Object.each(selected, function(val){
				if (val == value)
					to_select = true;
			});

			if (to_select || (selectedIndex && selectedIndex == value))
			{
				opt.setProperty('selected', 'selected');
			}

			// Stores the data used to build the option
			// Can be retrieved with : opt.retrieve('data');
			opt.store('data', row);

			self.fireEvent('onOptionDraw', [opt, row]);
		}
	},

	createOptionGroup: function(label, rows)
	{
		var self = this,
			o = this.options,
			container = arguments[2] ? arguments[2] : this.select,
			opt = new Element('optgroup', {label: label}).inject(container);

		Object.each(rows, function(row)
		{
			self.createOption(row, opt)
		});
	},

	getDomElement: function()
	{
		return this.select;
	},

	getSelected: function()
	{
		return this.select.getSelected();
	},

	getSelectedData: function()
	{
		var data = this.getSelected().retrieve('data');
		if (typeOf(data) == 'array') data = data[0];
		return data;
	},

	getSelectedValue: function()
	{
		return this.options.selected;
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

	hide: function()
	{
		this.select.hide();
	},

	show: function()
	{
		this.select.show();
	},

	isVisible: function()
	{
		return this.select.isVisible();
	},

	destroy: function()
	{
		this.getDomElement().destroy();
		delete(this);
		return null;
	},

	fire: function()
	{
		this.select.fireEvent('change');
	}
});

ION.Form.SelectList = new Class({

	Implements: [Events, Options],

	elements: [],

	selected: null,

	options:
	{
		container:  null,           // Container ID or container DOM Element
		baseClass: 	'button',
		mainClass: 	'selectList',	// Element's container class
		partners: 	['.buttonList'],	// Partners selectors, reacting on click event
		'class':    '',    			// CSS class,
		title: 			'',			// Button title
		icon:			null,		// Icon class
		iconClass:		'',			// Additional icon CSS class
		enabled: 		true,

		id:         '',             // ID of the select

		post:       {},             // Data to post to the URL

		data:       null,           // JSON array to use as data
		url :       null,           // URL to use as data source
		ignore_keys: [],			// Keys to ignore when building the select options

		key:        null,			// Key of the data array to use as value
		label:      null,           // Key of the data array to use as label
		selected:	[],				// Selected Value or array of Selected Values

		fireOnInit: false,			// Fires the onChange event after init.

		rule:       null            // @todo. Rule to apply to the select

		// onChange(el, value, label)
	},

	initialize: function(o)
	{
		var self = this;

		this.setOptions(o);

		this.container = typeOf(o.container) == 'string' ? $(o.container) : o.container;

		var cl = typeOf(o['class'] != 'null') ? this.options.baseClass + ' ' + o['class'] : this.options.baseClass;

		this.element = new Element('a', {'class': cl});
		this.element.store('instance', this);

		this.title = new Element('span', {'html': o.title}).inject(this.element);

		this.addCaret();

		this.elContainer = new Element('div', {'class': this.options.mainClass});
		this.element.inject(this.elContainer);

		if (o.url)
		{
			ION.JSON(
				o.url,
				o.post,
				{
					onSuccess: function(json)
					{
						self.addListElements(json);
					}
				}
			);
		}
		else if (Object.getLength(o.data) > 0)
		{
			this.addListElements(o.data);
		}

		// Store the event
		this.options.onClick = function()
		{
			if (self.elContainer.hasClass('open'))
			{
				self.elContainer.removeClass('open');
			}
			else
			{
				$$('.' + self.options.mainClass).removeClass('open');

				self.options.partners.each(function(p){
					$$(p).removeClass('open');
				});

				self.elContainer.addClass('open');
				self._correctContainerPosition();
				self._addFilter();
			}
		};

		// Window click event
		if ( ! document.hasFormSelectListEvent)
		{
			document.addEvent('click', function(e){
				var el = e.target.getParent('.selectList');
				if ( ! el)
					$$('.selectList').removeClass('open')
			});
			document.hasFormSelectListEvent = true;
		}

		// Disable if asked to
		if (o.enabled == false) this.disable();
		else this.enable();

		if (this.container) this.elContainer.inject(this.container);

	//	this.fireEvent('onLoaded', this.button);

		// Fires "change" on init
		if (o.fireOnInit) this.fireChange();

		return this;
	},

	addListElements: function(elements)
	{
		var self = this,
			o = this.options,
			ul = this.elContainer.getElement('ul.dropdown-menu'),
			selected = o.selected && typeOf(o.selected) != 'array' ? [o.selected] : []
		;

		if ( ! ul) ul = new Element('ul', {'class':'dropdown-menu'}).inject(this.elContainer);

		// First value
		var li = new Element('li').inject(ul);
		var a = new Element('a', {text: this.options.title}).inject(li);
		a.addEvent('click', function(){
			self.onChange(null, this.title, null);
		});
		this.elements.push(a);

		Array.each(elements, function(el)
		{
			if (typeOf(el[self.options.key]) != 'null')
			{
				var li = new Element('li').inject(ul);
				var a = new Element('a', {text: el[self.options.label]}).inject(li);
				a.store('item', el);
				self.elements.push(a);

				if (selected.contains(el[self.options.key]))
					self._select(a, el[self.options.key], el[self.options.label]);

				a.addEvent('click', function()
				{
					self.onChange(el[self.options.key], el[self.options.label], el, a);
				});
			}
		});
	},

	onChange: function(key, label, item, el)
	{
		this.unSelectAll();

		if (item == null)
			this._select(null, null, this.options.title, item);
		else
			this._select(el, key, label, item);

		this.fireEvent('change', [this.selected.value, this.selected.label, this.selected.item]);
	},

	setChangeCallback: function(cb)
	{
		this.removeEvents('change');
		this.setOptions({onChange:cb});
	},

	fireChange: function()
	{
		if (this.selected != null)
			this.fireEvent('change', [this.selected.value, this.selected.label, this.selected.item]);
		else
			this.fireEvent('change', [null, null, this.options.title, null]);
	},

	unSelectAll: function()
	{
		this.elements.each(function(el){
			el.removeClass('selected');
		});
	},

	_select: function(el, value, label, item)
	{
		this.title.set('html', label);
		if (el != null)	el.addClass('selected');
		this.selected = {value:value, label:label, item:item};
	},

	setSelected: function(selected)
	{
		var self = this;

		if (typeOf(self.options.key) != 'null')
		{
			this.elements.each(function(el)
			{
				var item = el.retrieve('item');
				if (item && item[self.options.key] == selected[self.options.key])
				{
					self.unSelectAll();
					self._select(el, item[self.options.key], item[self.options.label]);
				}
			});
		}
	},

	select: function(value)
	{
		var self = this;

		if (typeOf(self.options.key) != 'null')
		{
			this.elements.each(function(el)
			{
				var item = el.retrieve('item');

				if (item && item[self.options.key] == value)
				{
					self.unSelectAll();
					self._select(el, item[self.options.key], item[self.options.label]);
				}
			});
		}
	},

	getSelected: function()
	{
		return this.selected;
	},

	enable: function()
	{
		if ( ! this.isEnabled)
		{
			this.element.removeProperty('disabled');
			this.element.removeClass('disabled');

			if (typeOf(this.options.onClick) == 'function')
				this.elContainer.addEvent('click', this.options.onClick);
		}
	},

	disable: function()
	{
		this.element.setProperty('disabled', 'disabled');
		this.element.addClass('disabled');

		this.elContainer.removeEvents();

		this.isEnabled = false;
	},

	hide: function()
	{
		this.elContainer.hide();
	},

	show: function()
	{
		this.elContainer.show();
	},

	destroy: function()
	{
		this.elContainer.destroy();
	},

	getElement: function()
	{
		if (this.selected != null)
			this.setSelected(this.selected);

		return this.elContainer;
	},

	addCaret: function()
	{
		new Element('span', {'class':'caret'}).inject(this.element);
	},

	_correctContainerPosition: function()
	{
		var ul = this.elContainer.getElement('ul.dropdown-menu'),
			lis = ul.getElements('li'),
			dim = ul.getCoordinates(),
			docDim = document.getCoordinates();

		if ((dim.left + dim.width) > docDim.width)
			ul.setStyles({'right': 0, left:'auto'});

		if(lis.length <= 7)
			this.elContainer.getElement('ul.dropdown-menu').setStyles({
				'overflow-y': 'hidden',
				'max-height': 'auto'
			});
	},

	_addFilter: function()
	{
		var ul = this.elContainer.getElement('ul.dropdown-menu'),
			lis = ul.getElements('li'),
			filterEl = ul.getElement('.filter')
		;

		if( lis.length >= 10 )
		{
			if (filterEl) filterEl.destroy();

			filterEl = new Element('li',{'class':'filter'}).inject(ul, 'top');
			var input = new Element('input', {'class':'inputtext'}).inject(filterEl);

			filterEl.addEvent(
				'click', function(e){
					e.stop();
				}
			);
			lis.show();

			this.filter = new ION.ElementFilter(
				input,
				lis,
				{
					trigger: 'keyup',
					cache: false,
					onShow: function(element) {	element.show();	},
					onHide: function(element) {	element.hide();	}
				}
			);
		}
	}
});


/**
 *
 * @type {Class}
 */
ION.Form.TextList = new Class({

	Implements: [Events, Options],

	options:
	{
		container:  null,           // Container ID or container DOM Element
		'class':    '',    			// CSS class,
		empty:		true,			// Empty container on init

		name:       '',             // Form input Name (hidden field, will store the values)
		id:         '',             // ID

		// TextList options
		unique:		true,
		minLength:	3,

		search: {
			url:	null,			//
			post:	null,
			data:	null
		},

		data: {						// Existing data
			url:	null,			// URL to get existing data from
			post:	null,			// usually ID, coma separated : {post_var_name: '1,4,8'}
			data:	null			// Plain existing data IDs
		},

		ignore_keys: [],			// Keys to ignore when building the select options

		key:        null,			// Key of the data array to use as value
		label:      null,           // Key of the data array to use as label
		selected:	null			// Selected Value or array of Selected Values (String or Array)
	},

	initialize: function(options)
	{
		this.setOptions(options);

		var o = this.options;

		// Container : If set, the select will be injected in this container
		if (o.container)
		{
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;
			if (o.empty) this.container.empty();
		}

		if (this.container == null) this.container = new Element('div');

		this.buildElement();

		return this;
	},

	buildElement: function()
	{
		var o = this.options,
			input_data = new Element('input', {name: o.name, 'type':'hidden'}).inject(this.container),
			input_list = new Element('input').inject(this.container);

		var element = new TextboxList(
			input_list,
			{
				unique: o.unique,
				plugins: {
					autocomplete: {
						placeholder: null,
						onlyFromValues: true,
						queryRemote: true,
						minLength: o.minLength,
						remote: {
							url: o.search.url,
							extraParams: o.search.post
						}
					}
				},
				bitsOptions: {
					editable: {
						addKeys: false,
						stopEnter: false
					}
				},
				onUpdate: function(formatted_values)
				{
					input_data.setProperty('value', formatted_values);
				}
			}
		);

		if (o.data.post != null)
		{
			ION.JSON(
				o.data.url,
				o.data.post,
				{
					onSuccess: function(r)
					{
						element.plugins['autocomplete'].setSelected(r);
					}
				}
			);
		}
	},


	getDomElement: function()
	{
		return this.container;
	}
});


ION.Form.AutoCompleter = new Class({

	Implements: [Events, Options],

	options:
	{
		container:  null,           // Container ID or container DOM Element
		'class':    '',    			// CSS class,
		empty:		true,			// Empty container on init

		name:       '',             // Form input Name (hidden field, will store the values)
		id:         '',             // ID

		minLength:	3,
		maxChoices:	20,
		filterSubset: true,
		forceSelect: false,
		notFoundNotSaved: false,

		url: null,
		post: null,

//		ignore_keys: [],			// Keys to ignore when building the select options

		key:        null,			// Key of the data array to use as value
		label:      null,           // Key of the data array to use as label

		selected:	{
			value: null,
			label: null
		}
	},

	initialize: function(options)
	{
		this.setOptions(options);

		var o = this.options;

		// Container : If set, the select will be injected in this container
		if (o.container)
		{
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;
			if (o.empty) this.container.empty();
		}

		if (this.container == null) this.container = new Element('div');

		this.buildElement();

		return this;
	},

	buildElement: function()
	{
		var o = this.options,
			input_data = new Element('input', {name: o.name, 'type':'hidden', value: o.selected.value}).inject(this.container),
			input_list = new Element('input', {'class': 'inputtext', value: o.selected.label}).inject(this.container);

		// @todo : If returned list is empty : display one message : 'Data will not be saved'
		new Autocompleter.Request.JSON(
			input_list,
			o.url,
			{
				postVar: 'search',
				postData: o.post,
				indicatorClass: 'autocompleter-loading',
				minLength: o.minLength,
				maxChoices: o.maxChoices,
				zIndex: 200000,
				selectMode:false,
				relative: true,
				filterSubset: o.filterSubset,
				forceSelect: o.forceSelect,
				'injectChoice': function(choice)
				{
					var label = choice[o.label],
						element = new Element('li', {'html': this.markQueryValue(label)}).store('data', choice)
					;

					element.inputValue = label;
					this.addChoiceEvents(element).inject(this.choices);
				},
				emptyChoices: function()
				{
					this.hideChoices();
				},
				onSelection: function(element, selected, value, input)
				{
					var data = selected.retrieve('data');
					input_data.set('value', data[o.key]);
				},
				onBlur: function(element)
				{
					if (o.notFoundNotSaved == true)
					{
						if (element.value == '' || element.value != this.opted)
							input_data.set('value', '');
					}
					else {
						input_data.set('value', element.value);
					}
				}
			}
		);
	},

	getDomElement: function()
	{
		return this.container;
	}
});

ION.Form.InputString = new Class({

	Implements: [Events, Options],

	options:
	{
		container:  null,           // Container ID or container DOM Element
		tag:    	'p',    		// HTML DOM Element tag to use,
		'class':    '',    			// CSS class,
		empty:		true,			// Empty container on init

		name:       '',             // Form input Name (hidden field, will store the values)
		id:         '',             // ID

		value:		'',				// Value at init
		url:		null,			// Save value URL
	},

	initialize: function(options)
	{
		this.setOptions(options);

		var o = this.options;

		// Container : If set, the select will be injected in this container
		if (o.container)
		{
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;
			if (o.empty) this.container.empty();
		}

		if (this.container == null) this.container = new Element('div');

		this.buildElement();

		return this;
	},

	buildElement: function()
	{
		var o = this.options,
			string_tag = new Element(o.tag, {text: o.value, contenteditable:true}).inject(this.container),
			string_input = new Element('input', {value: o.value, 'class':'inputtext'}).inject(this.container).hide()
		;

		if (o['class']) string_tag.addClass(o['class']);
		if (o['id']) string_tag.setProperty('id', o['id']);

		string_tag.addEvent('click', function(e)
		{
			string_input.show().focus();
			string_tag.hide();
		});

		string_input.addEvent('blur', function(e)
		{
			if (string_input.value != string_tag.get('text'))
			{
				string_tag.set('text', string_input.value)

			}
			string_input.hide();
			string_tag.show();
		});
	}

});