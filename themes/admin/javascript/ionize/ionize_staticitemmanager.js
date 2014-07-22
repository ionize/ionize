/**
 * Ionize Static Item Manager
 *
 */
ION.StaticItemManager = new Class({

	Implements: [Events, Options],

	options: {
		wId:   'wItems'          // Window ID
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
		this.w =            null;      // window
		this.wContainer =   null;      // Items container (in window)
		this.destination =  null;      // Destination container (ID) : In parent panel

		this.definition =   null;

		this.click_timer = null;

		// Extend Manager
		// this.extendManager = new ION.ExtendManager();
		this.extendManager = extendManager;

		// console.log('ION.StaticItemManager : initialize()');

		return this;
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

		if (typeOf(options.id_definition) != 'null')
		{
			this.id_definition = options.id_definition;
			this.getDefinition();
		}
		if (typeOf(options.destination) != 'null') this.destination = options.destination;

		this.setWindowInfo();
	},


	/**
	 * Get Definition and stores it
	 * in this.definition
	 *
	 */
	getDefinition: function()
	{
		var self = this;
		var options = arguments[0];

		if (options && options['id_definition']) this.id_definition = options['id_definition'];

		if (this.id_definition)
		{
			ION.JSON(
				ION.adminUrl + 'item_definition/get',
				{
					id_definition: this.id_definition
				},
				{
					onSuccess: function(definition)
					{
						self.definition = definition;

						if (options && options.onSuccess)
							options.onSuccess(definition);
					}
				}
			)
		}
	},


	/**
	 * Gets items which belongs to one item definition
	 * Builds the Items instances list
	 *
	 * (backend main definition panel)
	 *
	 */
	getItemsFromDefinition: function()
	{
		var self = this;

		if (this.id_definition)
		{
			ION.JSON(
				ION.adminUrl + 'item/get_list_from_definition/json',
				{
					id_item_definition: this.id_definition
				},
				{
					onSuccess: function(json)
					{
						self.buildDefinitionItemList(json);
					}
				}
			);
		}
	},


	buildDefinitionItemList: function(json)
	{
		var self = this;

		if ($(this.destination))
		{
			$(this.destination).empty();

			new ION.List(
				{
					container: $(this.destination),
					items: json,
					sortable:true,
					sort: {
						handler: '.drag',
						id_key: 'id_item',
						url: ION.adminUrl + 'item/save_ordering'
					},
					elements:[
						{element: 'span', 'class': 'icon drag left absolute'},                          // Sort
						{element: 'span', 'class': 'lite drag left absolute ml30', text: 'id_item'},    // Item ID
						// Delete
						{
							element: 'a',
							'class': 'icon delete right absolute mr10',
							onClick: function(item)
							{
								self.deleteItem(item);
							}
						},
						// Edit
						{
							element: 'a',
							'class': 'icon edit right absolute mr35',
							onClick: function(item)
							{
								self.editItem(item.id_item);
							}
						},
						// Title
						{
							// Receives the item and the container's LI
							element: function(item, container)
							{
								Object.each(item.fields, function(f)
								{
									if (f.html_element == 'input' && ['text', 'date', 'email', 'tel'].contains(f.html_element_type))
									{
										var field = new ION.FormField({
											container : container,
											'class':'small mr50 ml30 mb0 mt0',
											label: {'class': 'lite', text: f.label}
										});

										if (f.translated == 1)
										{
											var w = 100 / Object.getLength(f.lang_data);

											// Each lang div
											Object.each(f.lang_data, function(data, lang_code)
											{
												var div = new Element('div', {'class':'left'}).inject(field.getContainer());
												div.setStyle('width', w + '%');

												var divImg = new Element('div', {'class':'left w20'}).inject(div);
												var img = new Element('img', {'class':'mt3'}).inject(divImg);
												img.setProperty('src', ION.themeUrl + 'styles/original/images/world_flags/flag_'+lang_code+'.gif');

												var content = data.content.length > 20 ? data.content.substring(0,25) + '...' : data.content;

												new Element('div', {
													'class':'ml30',
													text: content
												}).inject(div);
											})
										}
										else
										{
											var content = f.content.length > 20 ? f.content.substring(0,20) + '...' : f.content;
											var span = new Element('span', {text: content});
											field.adopt(span);
										}
										return field;
									}
									return null;
								});
							}
						}
					]
				}
			);
		}
		else
		{
			console.log('ION.StaticItemManager : Container not found : ' + this.destination);
		}
	},


	createItem: function(id_definition)
	{
		//
		//
		//

		// Only create one instance if the definition is known
		if (this.id_definition)
		{
			this.getItemForm();
		}
	},


	/**
	 * Edit one item instance
	 *
	 * @param id
	 */
	editItem: function(id)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'item/get_item/json',
			{
				id_item: id
			},
			{
				onSuccess: function(item)
				{
					self.getDefinition({
						id_definition: item.id_item_definition,
						onSuccess: function()
						{
							self.getItemForm(id);
						}
					});
				}
			}
		);
	},


	getItemForm: function()
	{
		var self = this;

		var id_item = arguments[0];
		if ( ! id_item) id_item = '';

		// Window Title
		var wTitle = (id_item == '') ? Lang.get('ionize_title_item_new') : Lang.get('ionize_title_edit_item');

		// Window subtitle
		var subTitle = [];
		if (id_item != '') subTitle.push({key: Lang.get('ionize_label_id'), value: id_item});
		subTitle.push({key: Lang.get('ionize_label_key'), value: this.definition.name});

		this._createFormWindow(
		{
			id: 'wItem' + id_item,
			title: wTitle,
			width: 620,
			height: 380,
			contentTitle: this.definition.title_item,
			contentTitleClass: 'definition items',
			contentSubTitle: subTitle,
			form:
			{
				id: 'itemForm' + id_item,
				action: ION.adminUrl + 'item/save',
				// Tells the form we want to reload (means one reload button will be created)
				reload: function(json)
				{
					self.editItem(json.id_item);
					self.getItemsFromDefinition();
				},
				onSuccess: function(json)
				{
					self.getItemsFromDefinition();
				}
			},
			onDrawEnd: function(w, form)
			{
				var options = {
					parent: 'item',
					id_field_parent: self.id_definition,
					destination: form.id
				};

				if (id_item != '') options['id_parent'] = id_item;

				// Init the ExtendManager
				self.extendManager.init(options);

				// Get Item Definition Extend Fields
				self.extendManager.getParentInstances();

				// Added after extendManager
				new Element('input', {type:'hidden', name:'id_item', value: id_item}).inject(form);
				new Element('input', {type:'hidden', name:'id_item_definition', value: self.id_definition}).inject(form);

				// Ordering Select
				var field = new ION.FormField({
					container: form,
					label: {
						text: Lang.get('ionize_label_ordering'),
						'class': 'small'
					}
				});

				var select = new Element('select', {id:'ordering',name:'ordering', 'class':'select'});
				select.adopt(new Element('option', {value:'first', text:Lang.get('ionize_label_ordering_first')}));
				select.adopt(new Element('option', {value:'last', text:Lang.get('ionize_label_ordering_last')}));

				field.adopt(select);
			}
		});
	},

	/**
	 * Delete one item instance
	 *
	 * @param id
	 */
	deleteItem: function(item)
	{
		var self = this;
		var confirm = arguments[1];

		if ( ! confirm)
		{
			var message = Lang.get('ionize_confirm_element_delete');
			var callback = self.deleteItem.pass([item, true], self);
			ION.confirmation('requestConfirm' + item.id_item, callback, message);
		}
		else
		{
			ION.JSON(
				ION.adminUrl + 'item/delete',
				{
					id_item: item.id_item
				},
				{
					onSuccess: function(msg)
					{
						if (msg.message_type == 'success')
						{
							self.getItemsFromDefinition(item.id_item_definition);
						}
					}
				}
			);
		}
	},


	/**
	 * Adds info to the window
	 *
	 */
	setWindowInfo: function()
	{
		if (this.w != null)
		{
			if (this.parent && this.id_parent)
				this.w.setInfo(String.capitalize(this.parent) + ' : ' + this.id_parent);
			else
				this.w.setInfo('WARNING : No parent !!!');

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
	 * All items for Item "link to parent" window
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
	 * for the Items instances Drag'n'drop window to parent
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
						var li = self._getInstanceDomElement(item);
						if (li != null)
							li.inject(ul, 'bottom');
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


	_getInstanceDomElement: function(item)
	{
		var self = this;
		var id = item.id_item;

		var li = null;

		// Only display if the item has at least one field set
		if (item.fields.length > 0)
		{
			li = new Element('li', {
				'class': 'list pointer',
				'data-id': item.id_item,
				'data-id-definition': item.id_item_definition
			});

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
			edit.addEvent('click', function()
			{
				self.editItem(id);
			});

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
				function(element)
				{
					self.linkItemtoParent(element.getProperty('data-id'));
				}
			);
		}

		return li;
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
						'class':'list pb15 pl10 relative'
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
							edit.addEvent('click', function()
							{
								self.editItem(id);
							});
						}
					});


					// Sortable
					var sortable = new Sortables(ul,
					{
						revert: true,
						handle: '.drag',
						clone: true,
						constrain: false,
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
		if( Browser.name!='ie') if (e) e.stop();

		if (clicks === 2)
		{
			var id_item = element.getProperty('data-id');
			this.linkItemtoParent(id_item);
		}
		else{}
	},

	_createWindow: function(opt)
	{
		var options = {
			container: document.body,
			content:{},
			y: 40,
			maximizable: true,
			contentBgColor: '#fff'
		};

		Object.append(options, opt);

		if (options.contentTitle)
		{
			var contentTitleClass = (options.contentTitleClass) ? ' ' + options.contentTitleClass : '';

			var ode = (options.onDrawEnd) ? options.onDrawEnd : null;

			options['onDrawEnd'] = function(w)
			{
				var h2 = new Element('h2', {
					'class': 'main' + contentTitleClass,
					'text' : options.contentTitle
				}).inject(w.el.content);

				// Subtitle is one array of objects
				if (options.contentSubTitle)
				{
					var subtitle = new Element('div', {
						'class': 'main subtitle'
					}).inject(w.el.content, 'bottom');

					var p = new Element('p').inject(subtitle);

					Array.each(options.contentSubTitle, function(sub, idx)
					{
						if (idx > 0)
							new Element('span', {'text': ' | '}).inject(p);

						new Element('span', {'class': 'lite', 'text': sub.key + ' : ' }).inject(p);
						new Element('span', {'text': sub.value}).inject(p);
					});

					// Options onDrawEnd
					if (ode != null) ode(w);
				}
			}
		}

		var w = new MUI.Window(options);

		return w;
	},

	/**
	 *
	 * @param opt
	 * @returns {MUI.Window}
	 * @private
	 */
	_createFormWindow: function(opt)
	{
		var ode = (opt.onDrawEnd) ? opt.onDrawEnd : null;

		var form = null;

		if (opt.form)
		{
			var options =
			{
				onDrawEnd: function(w)
				{
					form = new Element('form', {
						id: opt.form.id,
						'class': opt.form.class,
						action: opt.form.action,
						method: 'post'
					}).inject(w.el.content);

					var divButtons = new Element('div', {'class':'buttons'}).inject(w.el.content);

					var saveButton = new Element('button', {
						'class':'button right yes',
						id: 'save' + opt.form.id,
						text: Lang.get('ionize_button_save_close')
					}).inject(divButtons);

					if (opt.form.reload)
					{
						var saveReloadButton = new Element('button', {
							'class':'button blue right ml10',
							text: Lang.get('ionize_button_save')
						}).inject(divButtons);

						saveReloadButton.addEvent('click', function()
						{
							ION.JSON(
								opt.form.action,
								form,
								{
									onSuccess:function(json)
									{
										w.close();
										opt.form.reload(json);
									}
								}
							);
						});
					}

					var cancelButton = new Element('button', {
						'class':'button right red',
						id: 'cancel' + opt.form.id,
						text: Lang.get('ionize_button_cancel')
					}).inject(divButtons);

					cancelButton.addEvent('click', function(){ w.close(); });

					ION.setFormSubmit(
						form,              // Form Object
						saveButton.id,     // Save button ID
						form.action,       // Save URL
						null,              // Confirmation Object (null in this case, no conf. to open one window)
						opt.form           // Options, to pass onSuccess() method
					);

					// Options onDrawEnd : Add of the form
					if (ode != null) ode(w, form);
				}
			}
		}
		else
		{
			options = {};
		}

		Object.append(opt, options);

		var w = this._createWindow(opt);

		var response = {
			w : w,
			form: form
		};

		return w;
	}
});
