/**
 * Ionize Static Item Manager
 *
 */
ION.StaticItemManager = new Class({

	Implements: [Events, Options],

	options: {
		wId:   'wItem'          // Window ID
	},


	/**
	 *
	 * @param options
	 */
	initialize: function(options)
	{
		this.setOptions(options);

		this.baseUrl =		ION.baseUrl;
		this.adminUrl =		ION.adminUrl;
		this.themeUrl =		ION.themeUrl;

		this.parent =		null;
		this.id_parent =	null;
		this.w =            null;       // window
		this.wContainer =   null;      // Items container (in window)
		this.destination =  null;       // Destination container (ID)

		this.click_timer = null;

	},

	/**
	 * Initialize basis properties
	 *
	 * @param options
	 */
	init: function(options)
	{
		this.parent = options.parent;
		this.id_parent = options.id_parent;

		if (typeOf(options.destination) != 'null')
			this.destination = options.destination;

		this.setWindowInfo();
	},


	/**
	 * Edit one item
	 *
	 * @param id
	 */
	editItem: function(id)
	{
		ION.formWindow(
			'item' + id,
			'itemForm' + id,
			'ionize_title_edit_content_element',
			'item/edit',
			{width:600, height:350},
			{'id_item': id}
		);
	},


	/**
	 * Adds info to the window
	 *
	 */
	setWindowInfo: function()
	{
		if (this.w != null)
		{
			this.w.setInfo(String.capitalize(this.parent) + ' : ' + this.id_parent);
		}
	},


	/**
	 * Opens wondow of all items,
	 * grouped by item definitions
	 *
	 * @param options
	 */
	openListWindow: function(options)
	{
		if (typeOf(options) != 'null') this.init(options);

		if (this.w == null)
		{
			this.initListWindow();

			this.setWindowInfo();
		}

		this.getItemListContent();
	},


	/**
	 * Creates the Items List Window
	 *
	 */
	initListWindow: function()
	{
		var self = this;

		// Mocha Window
		this.w = new MUI.Window(
		{
			id: this.options.wId,
			title: Lang.get('ionize_label_add_item'),
			container: document.body,
			cssClass: 'panelAlt',
			content:{},
			width: 310,
			height: 340,
			y: 40,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			maximizable: false,
			contentBgColor: '#fff',
			onClose: function()
			{
				self.w = null;
			},
			onDrawEnd: function(w)
			{
				var newLeft = window.getSize().x - (w.el.windowEl.offsetWidth + 50);
				w.el.windowEl.setStyles({left:newLeft});
				w.el.contentWrapper.addClass('panelAlt');
			}
		});

		// Build the Header
		var container = $(this.options.wId + '_content');

		var h2 = new Element('h2', {
			'class': 'main definition items',
			'text' : Lang.get('ionize_title_static_items')
		}).inject(container);

		var subtitle = new Element('div', {'class': 'main subtitle'}).inject(container, 'bottom');
		var p = new Element('p').inject(subtitle);
		var subtitleText = new Element('span', {'class': 'lite', 'text': Lang.get('ionize_subtitle_static_item_list')}).inject(p);

		// Content container
		this.wContainer = new Element('div', {'id':'static-items'}).inject(container, 'bottom');
	},


	/**
	 * Gets the Items List
	 *
	 */
	getItemListContent:function()
	{
		if (this.wContainer)
		{
			var self = this;

			// Get definitions, with items linked to them
			ION.JSON(
				ION.adminUrl + 'item/get_definitions_with_items',
				{},
				{
					onSuccess: function(responseJSON)
					{
						self.buildItemList(responseJSON);
					}
				}
			);
		}
	},


	/**
	 * Build the Items list HTML
	 *
	 * @param json
	 */
	buildItemList: function(json)
	{
		var self = this;

		if (this.wContainer)
		{
			this.wContainer.empty();

			// Each item is one definition
			Object.each(json, function(definition)
			{
				if (definition.items.length > 0)
				{
					// Title : Toggler
					var h = new Element('h3', {
						'class':'toggler toggler-items',
						'title':definition.description,
						'text': definition.title_definition
					}).inject(self.wContainer, 'bottom');

					// Toggler wrapper
					var tw = new Element('div', {
						'class':'element element-items'
					}).inject(self.wContainer, 'bottom');

					// UL
					var ul = new Element('ul', {
						'class':'list pb15 pl15'
					}).inject(tw, 'bottom');

					// Items List
					Object.each(definition.items, function(item)
					{
						var id = item.id_item;

						// Only display if the item has at least one field set
						if (item.fields.length > 0)
						{
							var li = new Element('li', {
								'class': 'list pointer',
								'data-id': item.id_item,
								'data-id-definition': item.id_item_definition
							}).inject(ul, 'bottom');

							var field = null;

							// Get the field declared as main
							Object.each(item.fields, function(f) { if (f.main == 1) field = f; });

							// Not found : Get the 1st one.
							if (field == null) field = item.fields[0];

							// Title
							var title = new Element('a', {
								'class': 'left title unselectable',
								'text': field.content
							}).inject(li);

							// Add edit icon
							var edit = new Element('a', {'class':'icon edit right'}).inject(li);
							edit.addEvent('click', function(){ self.editItem(id); })

							// Double click on item
							li.addEvents({
								'click': function(e)
								{
									clearTimeout(self.click_timer);
									self.click_timer = self.relayItemListClick.delay(700, self, [e, this, 1]);
								},
								'dblclick': function(e)
								{
									clearTimeout(self.click_timer);
									self.click_timer = self.relayItemListClick.delay(0, self, [e, this, 2]);
								}
							});

							ION.addDragDrop(
								li,
								'#splitPanel_mainPanel_pad.pad', 	    // Droppables class
								function(element, droppable, event)
								{
									self.linkItemtoParent(element.getProperty('data-id'));
								}
							);
						}
					});
				}
			});

			// Create the toggler
			ION.initAccordion(
				'.toggler.toggler-items',
				'.element.element-items',
				true,
				'itemsAccordion'
			);
		}
	},


	linkItemtoParent: function(id_item)
	{
		var self = this;

		// Get definitions, with items linked to them
		ION.JSON(
			ION.adminUrl + 'item/link_to_parent',
			{
				'id_item': id_item,
				'parent': this.parent,
				'id_parent': this.id_parent
			},
			{
				onSuccess: function()
				{
					self.getParentItemList();
				}
			}
		);
	},


	unlinkItemfromParent: function(id_item)
	{
		var self = this;

		// Get definitions, with items linked to them
		ION.JSON(
			ION.adminUrl + 'item/unlink_from_parent',
			{
				'id_item': id_item,
				'parent': this.parent,
				'id_parent': this.id_parent
			},
			{
				onSuccess: function()
				{
					self.getParentItemList();
				}
			}
		);
	},


	getParentItemList: function()
	{
		// Only do it if the parent and parent ID are set
		if (this.parent && this.id_parent)
		{
			var self = this;

			ION.JSON(
				ION.adminUrl + 'item/get_parent_item_list',
				{
					'parent': this.parent,
					'id_parent': this.id_parent
				},
				{
					onSuccess: function(responseJSON)
					{
						if (self._hasItems(responseJSON))
							self.buildParentList(responseJSON);
						else
						{
							self.removeParentList();
						}
					}
				}
			);
		}
	},

	removeParentList: function()
	{
		var container = $(this.destination);

		// No container : Stop here
		if ( ! container) return null;

		// Case of tabs
		if (container.hasClass('mainTabs'))
		{
			// Get back the instance
			var tabSwapper = container.retrieve('tabSwapper');

			if (typeOf(tabSwapper) != 'null')
			{
				if (tabSwapper.hasTabId('staticItems'))
				{
					tabSwapper.removeTabById('staticItems');
				}
			}
		}
	},

	buildParentList: function(json)
	{
		var self = this;

		var container = this.getParentItemsContainer();

		if (container)
		{
			container.empty();

			var _nb_items = 0;

			Object.each(json, function(definition)
			{
				if (definition.items.length > 0)
				{
					_nb_items += definition.items.length;

					// Title : Toggler
					var h = new Element('h3', {
						'class':'toggler toggler-parent-items',
						'text': definition.title_definition
					}).inject(container, 'bottom');

					// Toggler wrapper
					var tw = new Element('div', {
						'class':'element element-parent-items'
					}).inject(container, 'bottom');

					// UL
					var ul = new Element('ul', {
						'class':'list pb15 pl10'
					}).inject(tw, 'bottom');

					// Items List
					Object.each(definition.items, function(item)
					{
						var id = item.id_item;

						// Only display if the item has at least one field set
						if (item.fields.length > 0)
						{
							var li = new Element('li', {
								'id' : 'i' + id,
								'class': 'list pointer',
								'data-id': item.id_item,
								'data-id-definition': item.id_item_definition
							}).inject(ul, 'bottom');

							var field = null;

							// Get the field declared as main
							Object.each(item.fields, function(f)
							{
								if (f.main == 1) field = f;
							});

							// Not found : Get the 1st one.
							if (field == null) {
								field = item.fields[0];
							}

							// Drag'n'Drop
							var dnd = new Element('span', {
								'class': 'icon left drag'
							}).inject(li);

							// Title
							var title = new Element('a', {
								'class': 'left title unselectable',
								'text': field.content
							}).inject(li);

							// Unlink icon
							var delIcon = new Element('a', {'class':'icon unlink right'}).inject(li);
							delIcon.addEvent('click', function(){ self.unlinkItemfromParent(id); });

							// Add edit icon
							var edit = new Element('a', {'class':'icon edit right mr10'}).inject(li);
							edit.addEvent('click', function(){ self.editItem(id); });
						}
					});


					// Sortable
					var sortable = new Sortables(ul,
					{
						revert: true,
						handle: '.drag',
						clone: true,
						constrain: true,
						opacity: 0.5,
						onStart:function(el, clone)
						{
							clone.addClass('clone');
						},
						onComplete: function(item, clone)
						{
							// Hides the current sorted element (correct a Mocha bug on hidding modal window)
							item.removeProperty('style');

							// Get the new order
							var serialized = this.serialize(0, function(item)
							{
								// Check for the not removed clone
								if (item.id != '')
									return item.getProperty('data-id');
								return;
							});

							// Items sorting
							self.sortItemsForParent(serialized);
						}
					});
				}
			});

			// Create the toggler
			ION.initAccordion(
				'.toggler.toggler-parent-items',
				'.element.element-parent-items',
				true,
				'parentItemsAccordion'
			);

			// Update the tab nb items
			this.setParentNbItemsInfo(_nb_items);
		}
	},

	sortItemsForParent: function(serialized)
	{
		var serie = [];
		serialized.each(function(item)
		{
			if (typeOf(item) != 'null')	serie.push(item);
		});

		ION.JSON(
			ION.adminUrl + 'item/order_for_parent',
			{
				parent:this.parent,
				id_parent:this.id_parent,
				order:serie
			},
			{}
		);
	},


	_hasItems: function(json)
	{
		var answer = false;

		Object.each(json, function(definition)
		{
			if (definition.items.length > 0)
				answer = true;
		});
		return answer;
	},


	/**
	 * Analyse the destination and try to find out the container
	 * Build it if mandatory
	 */
	getParentItemsContainer: function()
	{
		var container = $(this.destination);
		var section = null;

		// No container : Stop here
		if ( ! container) return null;

		// Case of tabs
		if (container.hasClass('mainTabs'))
		{
			// Get back the instance
			var tabSwapper = container.retrieve('tabSwapper');

			if (typeOf(tabSwapper) != 'null')
			{
				section = tabSwapper.getSection('.staticItems');

				// Build one tabswapper section
				if ( ! section)
				{
					var title = Lang.get('ionize_title_static_items');
					section = tabSwapper.addNewTab(title, 'staticItems');
					section.addClass('mt20');
					section.addClass('p10');
				}
			}
		}

		return section;
	},

	setParentNbItemsInfo: function(nb_items)
	{
		var container = $(this.destination);

		// Case of tabs
		if (container.hasClass('mainTabs'))
		{
			// Get back the instance
			var tabSwapper = container.retrieve('tabSwapper');
			tabSwapper.setTabInfo('.staticItems', nb_items);
		}
	},

	relayItemListClick: function(e, element, clicks)
	{
		// IE7 / IE8 event problem
		if( ! Browser.ie) if (e) e.stop();

		if (clicks === 2)
		{
			var id_item = element.getProperty('data-id');
			this.linkItemtoParent(id_item);
		}
		else{}
	}
});
