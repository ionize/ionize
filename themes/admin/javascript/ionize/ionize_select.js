/**
 *
 *
 */
ION.Select = new Class({

	Implements: [Events, Options],

	options:
	{
		url: '',                    // URL of the Select HTMLElement feed
		name: '',
		cssClass:'',
		textField: 'text',			// the name of the field that has the item's text
		valueField: 'value',		// the name of the field that has the item's value
		titleValue:	null,		    // Title
		titleText:	null,		    // Title
		selectedValue: null,
		multiSelect:false,
		data: {}                    // data object to send by POST to the Select feed url

		/*
		 * Events
		 *
		 * onChange: function(
		 *      obj         // Reference to the Select instance
		 * )
		 * onInit: function(
		 *      obj         // Reference to the Select instance
		 * )
		 */
	},

	initialize: function(options)
	{
		this.setOptions(options);

		this.storage = new Array();

		return this.getHtml();
	},

	/**
	 *
	 * @returns {HTMLElement}
	 */
	getHtml: function()
	{
		var self = this;

		this.select = new Element('select', {'class':this.options.cssClass, 'name':this.options.name});

		if (this.options.multiSelect)
			this.select.addProperty('multiple', 'multiple');

		// Set the Select title (first value)
		if (typeOf(this.options.titleText) != 'null')
		{
			new Element('option', {'value': this.options.titleValue}).set(
				'html',
				this.options.titleText
			).inject(this.select);
		}

		new Request.JSON({
			url: self.options.url,
			method: 'post',
			loadMethod: 'xhr',
			data: self.options.data,
			onSuccess: function(responseJSON)
			{
				Object.each(responseJSON, function(item)
				{
					var option = new Element(
						'option',
						{
							'value': item[self.options.valueField]
						}
					).set(
						'html',
						item[self.options.textField]
					).inject(self.select, 'bottom');

					if (self.options.selectedValue)
					{
						if (item[self.options.valueField] == self.options.selectedValue)
						{
							option.setProperty('selected', 'selected');
						}
					}
				});

				self.fireEvent('init', [self, self.select.value]);

				self.select.addEvent('change', function(e)
				{
					e.stop();
					self.fireEvent('change', [self, this.value]);
				});
			}
		}).send();

		return this.select;
	},

	getName: function()
	{
		return this.options.name;
	},

	delete:function()
	{
		if (typeOf(this.select) != 'null')
			this.select.destroy();
	},

	store: function(key, value)
	{
		this.storage[key] = value;
	},

	retrieve: function(key)
	{
		if (typeOf(this.storage[key]) != 'null')
			return this.storage[key];

		return null;
	}
});