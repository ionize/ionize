
/**
 * Ionize Table List
 * Table > TR > TD
 *
 */

ION.TableList = new Class({

	Implements: [Events, Options],

	filter_inputs: null,

	options: {
		id:         'tableList',
		container:  '',                 // DOM HTML Container. String or DOM Element
		empty:      true,               // Should the container be empty
		build:      true,               // Should the table be built on init
		items:      [],                 // JSON object : Array of items objects (lis) to build

		'class':    'list',             // CSS Class fo the table
		header:     true,               // Build one header ?
		alternateRows: true,            // Alternate row background colors ?
		buildIfEmpty: false,			// Build the header, even the table contains no data

		sortable: 	false,
		sortOn: 	0,
		sortBy: 	'ASC',

		dataSort:	false,
		dataSortable: [
			/*
			{
				key: 	456
				sort: 	'ASC'
			}
			*/
		],

		sort: [],

		post:       {},                 // Additional main data to post to each URL called by this class

		columns:    null,				// Columns definition
					/*
					[
						{
							key:            '',                 	// Key of each item to display. Must be set
							displayed_key:  '',                 	// Key displayed instead of "key" : Can be set to filter on a value and display another in the table's lines
							label:          '',                 	// Label of the column, can be ''
							type:           'string',           	// Type of the column. Used for sorting and filter (select, radio, checkbox)
							data:           [{value:'', label:''}],	// Data to use as select in case the type is "select"

							'class':        '',                 	// optional. CSS Class of the TD
							element:        'span',             	// optional. If set, creates one HTML element of this type
							elementClass:   '',                 	// optional. CSS class of the element
							onClick:        function(item){}    	// optional
						}
					],
					*/
		filter: {									// Filters definition
			keys: [],								// Colums keys to filter on. Must be keys defined in columns
			position: 'last',						// Position of the send button. 'first', 'last'
			filters: null,							// Previous filters
			styles: {}
			// _sort_ : [							// Sorting object
			//		{
			//			key: 588,
			//			value: DESC
			// ]
			// Event on Filter button click
			// onFilter: function(post, self, self.getDomElement(), self.container){}
		},
		pagination: {
			nb_by_page: -1,			// Number by page
			nb: 0,					// Total number of items
			current_page: 0			// Current page
			// onClick: function(post, self, self.getDomElement(), self.container){}
		}

		// onDraw:      function(ION.List){}        // Fired when the whole table is built.
		// onItemDraw:  function(tr, item){}        // Fired after one row was drawn. Receives the row object.
		// onCellDraw:  function(td, item){}        // Fired after one cell was drawn. Receives the row object.
	},

	initialize: function(options)
	{
		this.setOptions(options);

		var o = this.options;

		this.container = typeOf(o.container) == 'string' ? $(o.container) : o.container;

		this.build();

		this.extendManager = null;

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

		if (o.items.length > 0 || o.buildIfEmpty)
		{
			// Table
			this.table = new Element('table', {
				'class': o.class,
				id: o.id
			});

			// Header
			if (o.header) this.buildHeader();

			// Filters
			if (o.filter.keys.length > 0) this.buildFilter();

			// Body
			var tbody = new Element('tbody').inject(this.table);

			// Rows
			Object.each(o.items, function(item, idx)
			{
				var tr = new Element('tr').inject(tbody);
				if (idx % 2 && o.alternateRows) tr.addClass('altRow');

				// Stores data, for further use
				tr.store('data', item);

				Array.each(o.columns, function(column)
				{
					var td = new Element('td').inject(tr);

					if (column.class) td.addClass(column.class);

					if (typeOf(o['vertical-align']) != 'null')
						td.addClass(o['vertical-align']);

					if (typeOf(column['vertical-align']) != 'null')
					{
						if (typeOf(o['vertical-align']) != 'null')
							td.removeClass(o['vertical-align']);

						td.addClass(column['vertical-align']);
					}

					td.adopt(self._getItem(item, column, idx));

					if (typeOf(column.onCellDraw) == 'function')
						column.onCellDraw(td, item);

					self.fireEvent('onCellDraw', [td, item]);
				});

				if (o.filter.keys.length > 0)
				{
					var td = new Element('td').inject(tr);
				}

				self.fireEvent('onItemDraw', [tr, item]);
			});

			// Sortable (JS)
			if (o.sortable)	new SortableTable(this.table, {sortOn: o.sortOn, sortBy: o.sortBy});

			// Inject in container
			if (this.container && o.build == true) this.table.inject(this.container);

			// Pagination (injected after the table)
			if (o.pagination.nb_by_page > 0) this.buildPagination();
		}
	},

	/**
	 *
	 * @param	{Object}	item
	 * @param	{Object}	c     Column option item
	 * @param	{String}	idx
	 * @private
	 */
	_getItem: function(item, c, idx)
	{
		var el = c.element ? new Element(c.element) : new Element('span'),
			key = c.displayed_key ? c.displayed_key : c.key;

		if (c.elementClass) el.addClass(c.elementClass);

		// Content
		// Date format
		var value = (key == '-rownum-') ? parseInt(idx) + 1 :
					(
						c.extend ? this._getExtendDisplayedValue(item, c.extend) :
						(
							item[key] ? item[key] :
							(
								c.content ? c.content : ''
							)
						)
					);

		if (c.type == 'date')
			value = Date.formatFromMySql(value, Settings.get('date_format'));

		if ( c.type != 'icon' && (typeOf(item[key]) != 'null' || (typeOf(c.content) != 'null' || value !='')))
			el.set('html', value);

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

	_getExtendDisplayedValue: function(item, extend)
	{
		var value = '';

		if (typeOf(item['_extends']) != 'null' && typeOf(item['_extends'][extend.name]) != 'null')
		{
			if (item['_extends'][extend.name].length > 0)
			{
				value = [];
				item['_extends'][extend.name].each(function(val){
					value.push(val.value_displayed);
				});
				value = value.join(', ');
			}
		}

		return value;
	},

	buildHeader: function()
	{
		var self = this,
			o = this.options,
			thead = new Element('thead').inject(this.table),
			thead_tr = new Element('tr').inject(thead)
		;

		// Header
		Array.each(o.columns, function(column)
		{
			var th = new Element('th', {
				axis: column.type,
				text: typeOf(column.label) != 'null' ? column.label : ''
			}).inject(thead_tr);

			if (column.class) th.setProperty('class', column.class);

			var ckey = typeOf(column.extend) != 'null' ? column.extend.id_extend_field : (column.key ? column.key : null);

			var sort = typeOf(column.extend) == 'null' && (typeOf(column.sort) == 'null' || column.sort == true ) ? true : false;

			// No Sort on Extend
			// @todo : Find a solution
			if (ckey && sort == true)
			{
				// dataSortable ?
				if (Object.getLength(self.options.sort) > 0)
				{
					if (ckey)
					{
						Array.each(self.options.sort, function (sort)
						{
							if (sort.key == ckey) {
								th.addClass('sort' + sort.value);
							}
						});
					}
				}

				if (self.options.dataSort)
				{
					th.addEvent('click', function () {
						self.submitSortable(ckey, th);
					});
				}
			}
		});

		if (o.filter.keys.length > 0)
		{
			new Element('th').inject(thead_tr);
		}
	},

	buildFilter: function()
	{
		var self = this,
			o = this.options,
			filter_keys = o.filter.keys,
			filter_styles = o.filter.styles,
			filters = o.filter.filters,
			thead = this.table.getElement('thead') || new Element('thead').inject(this.table),
			thead_tr = new Element('tr', {'class':'filters'}).inject(thead),
			hash = ION.generateHash()
		;

		// Filters inputs
		self.filter_inputs = [];

		// Header
		Array.each(o.columns, function(column)
		{
			var th = new Element('th').inject(thead_tr);

			if (filter_keys.contains(column.key))
			{
				// Extend
				if (typeOf(column.extend) != 'null')
				{
					if (self.extendManager == null) self.extendManager = new ION.ExtendManager();

					var options = {
						validateClass:'',
						container: th,
						dom_type: ['radio', 'checkbox'].contains(column.extend.html_element_type) ? 'select' : column.extend.html_element_type,
						name: column.extend.id_extend_field,
						setDefaultValue: false,
						noValueLabel: Lang.get('ionize_select_all'),
						onChange: function(el, value, extend)
						{
							self.submitFilter();
						}
					};

					if (['checkbox', 'radio', 'select'].contains(column.extend.html_element_type))
					{
						options['renderAs'] = 'select-multiple';
						options['class'] = 'w100p';
						options['attributes'] = [{key:'resizable', value:'resizable'}];
					}

					// Build the Extend
					var i = self.extendManager.getExtendField(column.extend, options);
					i.setProperty('id', column.key + '_' + hash);

					self.filter_inputs.push(i.getFormElement());

					// Restore previous filter value
					Object.each(filters, function(val, key)
					{
						if (key == column.key)
						{
							if (['checkbox', 'select'].contains(column.extend.html_element_type))
							{
								var values = val.split(',');

								if (i.get('tag') == 'select')
								{
									i.getElements('option').each(function (option) {
										if (values.contains(option.value)) {
											option.setProperty('selected', 'selected');
										}
									});
								}
							}
							else
								i.getFormElement().setProperty('value', val);
						}
					});
				}
				else
				{
					// If column.type is set : build one select
					if (column.type && ['checkbox', 'radio', 'select'].contains(column.type) && column.data)
					{
						var i = new Element('select', {
							'class': 'inputtext w100p',
							id: 	 column.key + '_' + hash,
							name: 	column.key + '[]',
							multiple: 'multiple'
						}).inject(th);

						new Element('option', {value: '', text: Lang.get('ionize_select_all')}).inject(i);

						var opt_value_key = typeOf(column.type_value) != 'null' ? column.type_value : 'value';
						var opt_label_key = typeOf(column.type_label) != 'null' ? column.type_label : 'label';

						column.data.each(function(option){
							new Element('option', {value: option[opt_value_key], text: option[opt_label_key]}).inject(i);
						});

						i.addEvent('change', function () {
							self.submitFilter();
						});

						self.filter_inputs.push(i);

						// Restore previous filter value
						Object.each(filters, function(val, key)
						{
							if (key == column.key)
							{
								if (['checkbox', 'select'].contains(column.type))
								{
									var values = typeOf(val) != 'array' ? val.split(',') : val;

									if (i.get('tag') == 'select')
									{
										i.getElements('option').each(function (option) {
											if (values.contains(option.value)) {
												option.setProperty('selected', 'selected');
											}
										});
									}
								}
								else
									i.getFormElement().setProperty('value', val);
							}
						});
					}
					else
					{
						var i = new Element('input', {
							type:    'text',
							id: 	 column.key + '_' + hash,
							name:    column.key,
							'class': 'inputtext'
						}).inject(th);

						// Event
						i.addEvent('keydown', function(event)
						{
							if(event.key == 'enter') self.submitFilter();
						});

						self.filter_inputs.push(i);

						// Restore previous filter value
						Object.each(filters, function(val, key)
						{
							if (key == column.key)
								i.setProperty('value', val);
						});
					}
				}

				// Style
				var s = self._getFilterStyle(column.key);
				if (s != null && ! ['checkbox', 'radio', 'select'].contains(column.type))
					if (s['class']) i.addClass(s['class']);
				else
					if (column.type == 'number') i.addClass('w60');
			}
		});

		// SAve received filters
		this.saveFilter(filters);

		// Reset Filter button
		var th = new Element('th').inject(thead_tr),
			icon_reset = new Element('a', {'class':'icon clear center', title:Lang.get('ionize_button_reset_filter')}).inject(th);

		icon_reset.addEvent('click', function(){
			self.resetFilter();
		})
	},

	_getFilterStyle: function(key)
	{
		var r = null,
			o = this.options.filter.styles;

		Object.each(o, function(s, k)
		{
			if (k == key) r = s;
		});

		return r;
	},

	submitSortable: function(key, th)
	{
		var self= this,
			value = th.hasClass('sortASC') ? 'DESC' : (th.hasClass('sortDESC') ? null : 'ASC'),
			found = false;

		th.removeClass('sortASC').removeClass('sortDESC').addClass('sort' + value);

		if (Object.getLength(self.options.sort) > 0)
		{
			Array.each(self.options.sort, function(item, idx)
			{
				if (item.key == key)
				{
					found = true;

					if (value == null)
						self.options.sort.erase(item);
					else
						self.options.sort[idx]['value'] = value;
				}
			});
		}

		if (found == false)
		{
			self.options.sort.push({
				key: key,
				value: value
			});
		}

		this.submitFilter();
	},

	submitFilter: function()
	{
		var post = this.getPostData();

		this.saveFilter(post);

		if (typeOf(this.options.filter.onFilter) == 'function')
			this.options.filter.onFilter(post, this, this.getDomElement(), this.container);
		else
		{
			console.log('No onFilter() event defined for this table. Here are the data :');
			console.log(post);
		}
	},

	getPostData: function()
	{
		var post = {};

		Object.each(this.filter_inputs, function(i)
		{
			if (i.value != '')
			{
				var name = i.name,
					value = i.value;

				if (i.get('tag') == 'select' && i.getProperty('multiple') == true)
				{
					var selected = i.getSelected(),
						values = [];

					selected.each(function(option){
						values.push(option.value);
					});

					value = values.join(',');
					name = i.name.substring(0, i.name.length-2);
				}

				post[name] = value;
			}
		});

		post = Object.append(post, this.options.post);

		// Add sort
		if (Object.getLength(this.options.sort) > 0)
			post['sort'] = this.options.sort;

		return post;
	},

	resetFilter: function()
	{
		var filters =[];
		filters[this.options.id] = null;

		ION.register('filters', Object.merge(ION.registry('filters'), filters));

		if (typeOf(this.options.filter.onFilter) == 'function')
			this.options.filter.onFilter({}, this, this.getDomElement(), this.container);
		else
		{
			console.log('No onFilter() event defined for this table. Here are the data :');
		}
	},

	saveFilter: function(post)
	{
		if (typeOf(post) == 'object')
		{
			var filters = [];

			if (!ION.registry('filters')) ION.register('filters', []);

			filters[this.options.id] = post;

			ION.register('filters', Object.merge(ION.registry('filters'), filters));
		}
	},

	getSavedFilter: function()
	{
		var filters = ION.registry('filters'),
			f = typeOf(filters[this.options.id]) == 'object' ? filters[this.options.id] : null;

		return f;
	},

	buildPagination: function()
	{
		var self = this,
			o = this.options.pagination,
			position = typeOf(this.options.pagination.position) == 'null' ? 'bottom' :
				['top', 'bottom', 'both'].contains(this.options.pagination.position) ? this.options.pagination.position : 'bottom',
			nb_pages = parseInt(o.nb) / parseInt(o.nb_by_page)
		;

		if (nb_pages > 0)
		{
			var ulTop = ['top', 'both'].contains(position) ? new Element('ul', {'class':'pagination'}).inject(this.table, 'before') : null,
				ulBottom = ['bottom', 'both'].contains(position) ? new Element('ul', {'class':'pagination'}).inject(this.table, 'after') : null;

			if (ulTop != null) self._buildPaginationUl(ulTop, nb_pages);
			if (ulBottom != null) self._buildPaginationUl(ulBottom, nb_pages);
/*
			for(var i=0; i<nb_pages; i++)
			{
				var j = i+1;
				var li = new Element('li').inject(ul);
				var a = new Element('a', {html:j, 'data-id':j}).inject(li);
				if (j == o.current_page) a.addClass('current');

				if (typeOf(o.onClick) == 'function')
				{
					a.addEvent('click', function()
					{
						var post = self.getPostData(),
							id = this.getProperty('data-id');

						o.onClick(id, post, self, self.getDomElement(), self.container);
					})
				}
			}
*/
		}
	},

	_buildPaginationUl: function(ul, nb_pages)
	{
		var self = this,
			o = this.options.pagination;

		// Previous button
		if (o.current_page > 1)
		{
			// First page
			if (o.current_page > 2)
			{
				var li = new Element('li').inject(ul),
					a = new Element('a', {html:' << ', 'data-id':1, title:Lang.get('ionize_label_pagination_first_page')}).inject(li);

				a.addEvent('click', function()
				{
					var post = self.getPostData(),
						id = this.getProperty('data-id');

					o.onClick(id, post, self, self.getDomElement(), self.container);
				})
			}

			var li = new Element('li').inject(ul),
				i_page = parseInt(o.current_page) - 1,
				a = new Element('a', {html:'Page ' + i_page, 'data-id':i_page}).inject(li);

			if (typeOf(o.onClick) == 'function')
			{
				a.addEvent('click', function()
				{
					var post = self.getPostData(),
						id = this.getProperty('data-id');

					o.onClick(id, post, self, self.getDomElement(), self.container);
				});
			}
		}

		// Next button
		if (o.current_page < nb_pages)
		{
			// ...
			if (o.current_page > 1)
				new Element('li', {'class': 'lite', text:' ... '}).inject(ul);

			// Next
			var li = new Element('li').inject(ul),
				i_page = parseInt(o.current_page) + 1,
				a = new Element('a', {html:'Page ' + i_page, 'data-id':i_page}).inject(li);

			if (typeOf(o.onClick) == 'function')
			{
				a.addEvent('click', function()
				{
					var post = self.getPostData(),
						id = this.getProperty('data-id');

					o.onClick(id, post, self, self.getDomElement(), self.container);
				})
			}

			// Last Page
			var li = new Element('li').inject(ul),
				a = new Element('a', {html:' >> ', 'data-id':nb_pages, title:Lang.get('ionize_label_pagination_last_page')}).inject(li);

			a.addEvent('click', function()
			{
				var post = self.getPostData(),
					id = this.getProperty('data-id');

				o.onClick(id, post, self, self.getDomElement(), self.container);
			})
		}
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
		'class':    	null,             // CSS Class fo the UL
		id:             'list',
		empty:      true,               // Should the container be empty
		container:      '',             // DOM HTML Container. String or DOM Element
		styles: {
			ul: 'list',                 // UL CSS classes
			li: ''                      // LI CSS classes
		},
		buildUl:        true,			// If set to false, does not build the UL inject the list in the parent
		sortable:		false,			// Is the list sortable. False by default

		properties: 	null,				// UL properties. Optional. will be added to the UL object.
										// Example : [{data-id: 123}]

		droppable: 		false,
		dropOn:			'',				// Class of the droppable list
		onDrop: 		null,

		dragOn:			null,

		items:          [],             // JSON object : Array of items objects (lis) to build
		post: {},                       // Additional main data to post to each URL called by this class

		sort: {
			handler:    '.drag',        	// Class of the icon used to sort elements by drag'n'drop
			id_key:     null,           	// Key to use as ID for each element.
			url:        null            	// URL of the sorting controller.
			// callback: function(serie)	// Callback function : Receives the ordered serie
											// Either url OR callback must be set
		},

		// Elements composing each list item
		elements:[
			{
				element: 'span',        // Sort Drag icon must be span
				'class': 'icon drag sort left'
				// url: ''
			},
			{
				element: 'a',
				'class': 'icon edit left',
				title: 'name',						// Key to use as title
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
				onClick: function(item, li)         // Event on click on the icon. If set, the url isn't used to post the data.
				{
					alert('You clicked on : ' + JSON.encode(item));
				},

				onSuccess: function(json)           // Event on success. Fired after XHR request
				{
					alert('Received from server : ' + json)
				}
			}
			/*
			{
				element: function(item, li, index){
					// Build DOM Element
					// Return DOM Element
				}
			}
			*/
		]

		// onDraw:      function(ION.List){}        // Fired when the whole list is built.
		// onItemDraw:  function([li, item]){}            // Fired after one item was drawn. Receives the item object.
	},


	/**
	 * @param	{Object}	options
	 */
	initialize: function()
	{
		var self = this,
			options = arguments[0] ? arguments[0] : {};

		if (options.elements) this.options.elements = [];

		this.setOptions(options);

		if (typeOf(options.container) == 'null')
		{
			console.log('ION.List error : Please set the "container" option');
			return;
		}

		this.container = (typeOf(options.container) == 'object') ? options.container : $(options.container);

		if (this.options.empty) this.container.empty();

		if (this.options.buildUl == false)
		{
			this.ul = this.container;
			this.ul.addClass('list');
			this.ul.addClass(this.options.styles.ul);
		}
		else
		{
			this.ul = new Element('ul', {
				id: this.options.id,
				'class': 'list ' + this.options.styles.ul
			}).inject(this.container);

			if (this.options['class'] != null)
				this.ul.addClass(this.options['class']);

			this.ul.setStyle('position', 'relative');
		}

		if (this.options.droppable == true)
		{
			this.ul.addClass('droppable');
			if (this.options.dropOn != null)
			{
				var c = this.options.dropOn.replace(/\.+/ig, '');
				this.ul.addClass(c);
			}
			if (typeOf(options.onDrop) == 'function')
			{
				this.ul.onDrop = options.onDrop;
			}
		}

		if (this.options.properties)
			self.ul.setProperties(this.options.properties);

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
				id: self.options.id + '_' + key,
				'class': 'list'
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
					var part;
					if (typeOf(el.element) == 'function')
					{
						part = el.element(item, li, key);

						if (typeOf(part) != 'null')
						{
							part.inject(li);
							if (el.class) part.addClass(el.class);
							else part.addClass('left');
						}
					}
					else
					{
						part = new Element(el.element).inject(li);

						// Left by default
						if (el.class) part.addClass(el.class);

						if ( ! part.hasClass('right') && ! part.hasClass('left'))
							part.addClass('left');

						// Set the text of the part
						if (el.content)
						{
							part.set('html', el.content);
						}
						else
						{
							if (el.text && item[el.text])
							{
								part.set('html', item[el.text]);
							}
							else if((item[el.text] == '' || item[el.text] == null) && el['text-failover'])
								part.set('html', item[el['text-failover']]);
							else if (el.text)
							{
								if (typeOf(el.empty) != 'null') part.set('html', el.empty);
								else part.set('html', li.id);
							}
						}

						// Add one safe span inside the text element : ellipsize, overflow:hidden etc.
						if ( ! part.hasClass('icon') && part.get('html') != '')
						{
							var html = part.get('html');
							part.empty();
							new Element('span', {'class': 'ellipsis', html:html}).inject(part);
						}

						// Title, Alt
						['title', 'alt', 'data-tooltip'].each(function(attr){
							if (el[attr])
							{
								if (item[el[attr]])	part.setProperty(attr, item[el[attr]]);
								else part.setProperty(attr, el[attr]);
							}
						});

						if (el['tooltip-class']) part.addClass(el['tooltip-class']);

						// onClick : Send the item
						if(el.onClick)
						{
							part.addEvent('click', function(e)
							{
								e.stop();
								var data = this.getParent('li').retrieve('data');
								el.onClick(data, li);
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

			// Not so clean, but :after peudo class aren't enough in this case;
			new Element('div', {'class':'clearfix'}).inject(li, 'bottom');

			// Add Drag'n'Drop capabilities
			if (self.options.dragOn != null)
				ION.addDragDrop(li, self.options.dragOn);

			self.fireEvent('onItemDraw', [li, item]);
		});

		if (Object.getLength(items) > 0)
			this.ul.addClass('filled');

		if (this.options.sortable)
			this._setSortable();
	},


	_setSortable: function()
	{
		var self = this;

		// Sortable
		this.sortables = new Sortables(this.ul,
		{
			revert: true,
			handle: this.options.sort.handler,
			clone: true,
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
					if ( ! item.hasClass('clone'))
					{
						var data = item.retrieve('data');
						if (self.options.sort.id_key != null && data[self.options.sort.id_key])
							return data[self.options.sort.id_key];
						else
							return item.id;
					}
					return;
				});

				// Items sorting
				self._sortItems(serialized);
			}
		});

		// @todo : Add optional droppable

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
		else if (this.options.sort.callback)
		{
			this.options.sort.callback(serie);
		}
	}
});
