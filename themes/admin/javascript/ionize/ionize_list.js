
/**
 * Ionize Table List
 * Table > TR > TD
 *
 */

ION.TableList = new Class({

	Implements: [Events, Options],

	options: {
		id:         'tableList',
		container:  '',                 // DOM HTML Container. String or DOM Element
		empty:      true,               // Should the container be empty
		build:      true,               // Should the table be built on init
		items:      [],                 // JSON object : Array of items objects (lis) to build

		'class':    'list',             // CSS Class fo the table
		header:     true,               // Build one header ?
		headerClass:null,           	// CSS class or each th of the header ?
		alternateRows: true,            // Alternate row background colors ?

		post:       {},                 // Additional main data to post to each URL called by this class

		columns:    null                // Columns description
					/*
					[
						{
							key:            '',                 // Key of each item to display. Must be set
							label:          '',                 // Label of the column, can be ''
							type:           'string',           // Type of the column. Used for sorting

							'class':        '',                 // optional. CSS Class of the TD
							element:        'span',             // optional. If set, creates one HTML element of this type
							elementClass:   '',                 // optional. CSS class of the element,
							title:			'',					// optional. If set, used as title attribute for the item
							onClick:        function(item){}    // optional
						}
					],
					*/
		// onDraw:      function(ION.List){}        // Fired when the whole table is built.
		// onItemDraw:  function(item){}            // Fired after one row was drawn. Receives the row object.
	},

	initialize: function(options)
	{
		this.setOptions(options);

		var o = this.options;

		this.container = typeOf(o.container) == 'string' ? $(o.container) : o.container;

		this.build();

		return this;
	},

	build: function()
	{
		var self = this;
		var o = this.options;

		if (o.empty) this.container.empty();

		// Create header from first item if no columns description received
		if (o.items.length > 0 && (o.columns == null || o.columns.length == 0))
		{
			o.columns = [];
			var first = o.items[0];
			Object.each(first, function(value, key)
			{
				var c = { key:key, label:key, type:typeOf(value)};
				o.columns.push(c);
			});
		}

		// Table
		this.table = new Element('table', {
			'class': o.class,
			id: o.id
		});

		// Header
		if (o.header)
			this.buildHeader();

		// Body
		var tbody = new Element('tbody').inject(this.table);

		// Rows
		Object.each(o.items, function(item, idx)
		{
			var tr = new Element('tr').inject(tbody);
			if (idx % 2 && o.alternateRows) tr.addClass('odd');

			// Stores data, for further use
			tr.store('data', item);

			Array.each(o.columns, function(column)
			{
				var td = new Element('td').inject(tr);

				if (column.class) td.addClass(column.class);

				td.adopt(self._getItem(item, column));
			});

			self.fireEvent('onItemDraw', [tr, item]);
		});

		// Inject in container
		if (this.container && o.build == true)
			this.table.inject(this.container);
	},

	/**
	 *
	 * @param item
	 * @param c     Column option item
	 * @private
	 */
	_getItem: function(item, c)
	{
		var el = c.element ? new Element(c.element) : new Element('span');

		if (c.elementClass) el.addClass(c.elementClass);

		if ( c.type != 'icon' && typeOf(item[c.key]) != 'null')
			el.set('html', item[c.key]);

		if (c.title) el.setProperty('title', c.title);

		if(c.onClick)
		{
			el.addEvent('click', function()
			{
				var data = this.getParent('tr').retrieve('data');
				c.onClick(data);
			});
		}

		return el;
	},


	buildHeader: function()
	{
		var o = this.options;

		var thead = new Element('thead').inject(this.table);
		var thead_tr = new Element('tr').inject(thead);

		// Header
		Array.each(o.columns, function(column)
		{
			var th = new Element('th', {
				axis: column.type,
				text: column.label
			}).inject(thead_tr);

			if (o.headerClass != null)
				th.addClass(o.headerClass);

		});
	},

	getDomElement: function()
	{
		return this.table;
	}

});



/**
 * Ionize List
 * UL > LI
 *
 */
ION.List = new Class({

	Implements: [Events, Options],

	options: {
		id:             'list',
		container:      '',             // DOM HTML Container. String or DOM Element
		styles: {
			ul: 'list',                 // UL CSS classes
			li: ''                      // LI CSS classes
		},
		buildUl:        true,

		items:          [],             // JSON object : Array of items objects (lis) to build
		post: {},                       // Additional main data to post to each URL called by this class

		sort: {
			handler:    '.drag',        // Class of the icon used to sort elements by drag'n'drop
			id_key:     null,           // Key to use as ID for each element.
			url:        null            // URL of the sorting controller. Must be set to activate the sorting
		},

		elements:[
			{
				element: 'span',        // Sort Drag icon must be span
				'class': 'icon drag sort left'
				// url: ''
			},
			{
				element: 'a',
				'class': 'icon edit left',
				onClick: function(item)
				{
					alert('You clicked on : ' + JSON.encode(item));
				}
			},
			{
				element: 'span',
				text:    'title'                    // Key of the item to use as text
			},
			{
				element: 'a',                       // DOM HTML element
				'class': 'icon unlink right',       // CSS Class of the DOM HTML element
				url:     null,                      // If set, one click posts options.post & each elements
													// keys / values to this URL
				onClick: function(item)             // Event on click on the icon. If set, the url isn't used to post the data.
				{
					alert('You clicked on : ' + JSON.encode(item));
				},

				onSuccess: function(json)           // Event on success. Fired after XHR request
				{
					alert('Received from server : ' + json)
				}
			}
		]

		// onDraw:      function(ION.List){}        // Fired when the whole list is built.
		// onItemDraw:  function(item){}            // Fired after one item was drawn. Receives the item object.
	},


	/**
	 *
	 * @param options
	 */
	initialize: function()
	{
		var options = arguments[0] ? arguments[0] : {};

		if (options.elements) this.options.elements = [];

		this.setOptions(options);

		this.container = (typeOf(options.container) == 'object') ? options.container : $(options.container);

		if (this.options.buildUl == false)
		{
			this.ul = this.container;
			this.ul.addClass(this.options.styles.ul);
		}
		else
		{
			this.ul = new Element('ul', {
				id: this.options.id,
				'class': this.options.styles.ul
			}).inject(this.container);

			this.ul.setStyle('position', 'relative');
		}


		this.items = options.items;

		this._buildList(this.items);

		this.fireEvent('onDraw', this);

		return this;
	},


	getDomList: function()
	{
		return this.ul;
	},


	_buildList: function(items)
	{
		var self = this;

		// Items
		Array.each(items, function(item, key)
		{
			var li = new Element('li', {
				id: self.options.id + '_' + key
			}).inject(self.ul);

			if (self.options.styles.li)
				li.addClass(self.options.styles.li);

			var data = {};
			Object.append(data, self.options.post);
			Object.append(data, item);

			// Stores the item data into the LI
			li.store('data', data);

			// Icons or Texts
			Array.each(self.options.elements, function(el)
			{
				if (el.element)
				{
					if (typeOf(el.element) == 'function')
					{
						var part = el.element(item, li);

						if (typeOf(part) != 'null')
						{
							part.inject(li);
							if (el.class) part.addClass(el.class);
							else part.addClass('left');
						}
					}
					else
					{
						var part = new Element(el.element).inject(li);

						// Left by default
						if (el.class) part.addClass(el.class);
						else part.addClass('left');

						// Set the text of the part
						if (el.text && item[el.text])
							part.set('html', item[el.text]);
						else if (el.text )
							part.set('html', li.id);

						// onClick : Send the item
						if(el.onClick)
						{
							part.addEvent('click', function()
							{
								var data = this.getParent('li').retrieve('data');
								el.onClick(data);
							})
						}
						// else, send data to controller
						else if (el.url)
						{
							// Options
							var options = {};
							if (el.onSuccess) options['onSuccess'] = el.onSuccess;

							// JSON request
							part.addEvent('click', function()
							{
								var data = this.getParent('li').retrieve('data');
								ION.JSON(
									el.url,
									data,
									options
								);
							});
						}
					}
				}
			});
		});

		if (Object.getLength(items) > 0)
			this.ul.addClass('filled');

		this._setSortable();
	},


	_setSortable: function()
	{
		var self = this;

		// Sortable
		new Sortables(this.ul,
		{
			revert: true,
			handle: this.options.sort.handler,
			clone:true,
			constrain: false,
			opacity: 0.5,
			onStart:function(el, clone)
			{
				clone.addClass('clone');
			},
			onComplete: function(item, clone)
			{
				// Hides the current sorted element (correct a Mocha bug on hiding modal window)
				item.removeProperty('style');

				// Get the new order
				var serialized = this.serialize(0, function(item)
				{
					// Check for the not removed clone
					if (item.id != '')
					{
						var data = item.retrieve('data');
						if (self.options.sort.id_key != null && data[self.options.sort.id_key])
							return data[self.options.sort.id_key]
						else
							return item.id;
					}
					return;
				});

				// Items sorting
				self._sortItems(serialized);
			}
		});
	},


	_sortItems: function(serialized)
	{
		var serie = [];

		serialized.each(function(item) {
			if (typeOf(item) != 'null')	serie.push(item);
		});

		var data = {order:serie};

		Object.append(data, this.options.post);

		// Send the new order to the controller
		if (this.options.sort.url)
		{
			ION.JSON(
				this.options.sort.url,
				data,
				{}
			);
		}
		else
		{
			alert('New order : ' + serie);
		}
	}
});
