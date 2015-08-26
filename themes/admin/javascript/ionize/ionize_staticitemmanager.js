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

		this.parentListContainer =  null;      // In parent panel
		this.definitionInstanceListContainer = null;

		this.panel = null;						// Main Panel, in Definition screen
		this.definitionPanel = null;			// Definition Panel, in Definition screen

		this.definition =   null;

		this.click_timer = null;

		// Extend Manager
		this.extendManager = new ION.ExtendManager();

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
			// this.id_definition = options.id_definition;
			this.getDefinition(options);
		}

		if (typeOf(options.parentListContainer) != 'null') this.parentListContainer = $(options.parentListContainer);

		this.setWindowInfo();
	},

	getMainPanel: function()
	{
		var self = this;

		// Empty toolbox
		ION.getToolbox();

		// Split panels
		MUI.Content.update(
		{
			element: 'mainPanel',
			title: Lang.get('ionize_title_static_items'),
			clear: true,
			loadMethod:'control',
			controls:[
				{
					control: 'MUI.Column',container: 'mainPanel',id: 'splitPanel_mainColumn',placement: 'main',	sortable: false,
					panels:[
						{
							control:'MUI.Panel',
							id: 'splitPanel_mainPanel',
							container: 'splitPanel_mainColumn',
							header: false,
							cssClass:'contactPanel',
							onDrawEnd:function(p)
							{
								var container = p.el.content;
								container.addClass('p20');

								self.panel = container;

								// Title
								new ION.WindowTitle({
									container: container,
									title: Lang.get('ionize_title_static_items'),
									subtitle: [{html:Lang.get('ionize_title_static_item_intro')}],
									'class': 'items'
								});

								// Intro
								var div = new Element('div', {'class':'mt30 ml30 mr30 pl20'}).inject(container);
								new Element('p', {'class':'', html:Lang.get('ionize_message_static_item_intro')}).inject(div);
							}
						}
					]
				},
				{
					control:'MUI.Column',container: 'mainPanel',id: 'splitPanel_sideColumn',placement: 'right',	sortable: false,isCollapsed: false,
					width: 300,
					resizeLimit: [200, 400],
					panels:[
						// Extend Fields
						{
							control:'MUI.Panel',
							header: true,
							'title': Lang.get('ionize_title_static_items_definitions'),
							id: 'splitPanel_definition',
							cssClass: 'panelAlt',
							onDrawEnd: function(p)
							{
								// Container
								var c = p.el.content,
									h = p.el.header;

								c.addClass('p20');

								self.definitionPanel = c;

								// Button : Create
								new ION.Button({
									container: h,
									title: Lang.get('ionize_title_new_definition'),
									'class': 'right mt2',
									icon: 'icon-plus',
									attributes: {
										'title': Lang.get('ionize_title_new_definition')
									},
									onClick: function()
									{
										self.createDefinition();
									}
								});

								// Extend List
								self.getDefinitionList();
							}
						}
					]
				}
			]
		});
	},


	getDefinitionList: function()
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'item_definition/get_list',
			{},
			{
				onSuccess: function(json)
				{
					new ION.List(
					{
						container: self.definitionPanel,
						items: json,
						elements:[
							// Edit
							{
								element: 'a',
								'class': 'icon edit left',
								onClick: function(item)
								{
									ION.JSON(
										ION.adminUrl + 'item_definition/get',
										{
											id_definition: item.id_item_definition
										},
										{
											onSuccess: function(definition)
											{
												self.editDefinition(definition);
											}
										}
									);
								}
							},
							// Title
							{
								element: 'a',
								'class': 'title left',
								text: 'title_definition',
								onClick: function(item)
								{
									self.getDefinitionDetails(item);
								}
							},
							// Delete
							{
								element: 'a',
								'class': 'icon delete right',
								onClick: function(item)
								{
									self.deleteDefinition(item);
								}
							}
						]
					}
					);
				}
			}
		)
	},

	createDefinition: function()
	{
		var item={
			id_item_definition: '',
			name: '',
			descirption: ''
		};

		this.editDefinition(item);
	},

	editDefinition: function(item)
	{
		var self = this,
			id = item.id_item_definition
		;

		new ION.Window(
		{
			type: 'form',
			form: {
				id: 'definitionForm' + id,
				action: ION.adminUrl + 'item_definition/save',
				onSuccess: function(json)
				{
					self.getDefinitionList();
				}
			},
			width: 500,
			height: 320,
			id: 'wDefinition' + id,
			title: {text: Lang.get('ionize_title_edit_definition'), 'class': 'items'},
			onDraw: function(w)
			{
				// FORM
				var form = w.getForm();

				// Hidden fields
				new Element('input', {name:'id_item_definition', type:'hidden', value:id}).inject(form);


				// Fields
				var ff_name = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_label'), for:'def_name_' +id, help:Lang.get('ionize_help_definition_name')}, 'class':'small'}),
					ff_description = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_description'), for:'def_desc_' + id}, 'class':'small'}),
					lang_container = new Element('div', {'class':''}).inject(form),
					lang_tabs = []
				;

				var input_name = new Element('input', {id:'def_name_' + id, name:'name', type:'text','class':'inputtext w96p required', value:item.name}).inject(ff_name.getContainer()),
					input_desc = new Element('textarea', {id:'def_desc_' + id, name:'description','class':'inputtext w96p', value:item.description}).inject(ff_description.getContainer())
				;

				Object.each(Lang.languages, function(label, key)
				{
					var def_title = (typeOf(item.languages) != 'null' && typeOf(item.languages[label]) != 'null') ? item.languages[label]['title_definition'] : '',
						inst_title = (typeOf(item.languages) != 'null' && typeOf(item.languages[label]) != 'null') ? item.languages[label]['title_item'] : ''
					;

					lang_tabs.push({
						label: label,
						onLoaded: function(tab, section)
						{
							var ff_def_title = new ION.FormField({container: section, label: {text: Lang.get('ionize_label_item_title_definition'), for:'title_definition_' +key + id, help:Lang.get('ionize_help_item_title_definition')}, 'class':'small'}),
								input_def_title = new Element('input', {id:'title_definition_' + label + id, name:'title_definition_' + label, type:'text','class':'inputtext w96p', value:def_title}).inject(ff_def_title.getContainer()),
								ff_inst_title = new ION.FormField({container: section, label: {text: Lang.get('ionize_label_item_title_definition_item'), for:'title_item_' +key + id, help:Lang.get('ionize_help_item_title_definition_item')}, 'class':'small'}),
								input_inst_title = new Element('input', {id:'title_item_' + label + id, name:'title_item_' + label, type:'text','class':'inputtext w96p', value:inst_title}).inject(ff_inst_title.getContainer())

						}
					})
				});

				new ION.Tabs({tabs:lang_tabs, container:lang_container});

				// Key
				ION.initCorrectUrl('def_name_' + id, 'def_name_' + id, '-');

				// Autogrow
				ION.initFormAutoGrow(form);

				// @todo :
				// Check, doesn't work
				// Validator : values
				var validator = form.retrieve('validator');

				validator.add('definitionNameUnique', {
					errorMsg: Lang.get('ionize_message_item_definition_already_exists'),
					test: function(element, props)
					{
						if (element.value.length > 0) {
							var req = new Request({
								url: ION.adminUrl + 'item_definition/check_exists',
								async: false,
								data: {
									name: input_name.value,
									id_item_definition: id
								}
							}).send();
							return (req.response.text != '1');
						}
						return true;
					}
				});
			}
		});

	},

	deleteDefinition: function(definition)
	{
		var self = this;

		ION.confirmation(
			'wExtendDefinition' + definition.id_item_definition,
			function()
			{
				ION.JSON(
					ION.adminUrl + 'item_definition/delete',
					{
						id_item_definition: definition.id_item_definition
					},
					{
						onSuccess: function(json)
						{
							self.getMainPanel();
						}
					}
				)
			},
			Lang.get('ionize_confirm_extend_delete')
		);
	},

	getDefinitionDetails: function(item)
	{
		var self = this,
			container = this.panel;

		// Store definition
		this.definition = item;
		this.id_definition = item.id_item_definition;

		// Empty container
		container.empty();

		self.extendManager.init({
			parent: 'item',
			id_parent: item.id_item_definition
		});

		// Title
		new ION.WindowTitle({
			container: container,
			title: item.name,
			subtitle: [
				{key: 'ID', value: item.id_item_definition},
				{key: 'Key', value: item.name}
			],
			'class': 'items'
		});


		// Fields
		if (ION.Authority.can('edit', 'admin/item/definition'))
		{
			new Element('h3', {'class':'toggler itemDefinition', html:Lang.get('ionize_title_item_fields')}).inject(container);

			var divFields = new Element('div', {'class':'element itemDefinition'}).inject(container),
			pFields = new Element('p', {'class':'h30'}).inject(divFields),
			divFieldsContainer = new Element('div', {'class':'mb30'}).inject(divFields),
			btnNewField = new ION.Button({
				container: pFields,
				title: Lang.get('ionize_label_add_field'),
				'class': 'light right',
				icon: 'icon-plus',
				onClick: function()
				{
					self.extendManager.createExtend({
						parent: 'item',
						id_parent: item.id_item_definition,
						onSuccess: function()
						{
							self._getDefinitionFieldList(item, divFieldsContainer);
						}
					});
				}
			});

			this._getDefinitionFieldList(item, divFieldsContainer);
		}

		// Instances
		new Element('h3', {'class':'toggler itemDefinition', html:Lang.get('ionize_title_item_instances')}).inject(container);

		var divInstances = new Element('div', {'class':'element itemDefinition'}).inject(container),
			pInstances = new Element('p', {'class':'h30'}).inject(divInstances),
			divInstancesContainer = new Element('div', {'class':'mb30'}).inject(divInstances),
			btnNewInstance = new ION.Button({
				container: pInstances,
				title: Lang.get('ionize_label_item_add_item'),
				'class': 'light right',
				icon: 'icon-plus',
				onClick: function()
				{
					self.createItem();
				}
			})
		;

		// Get instances
		this.definitionInstanceListContainer = divInstancesContainer;

		this.getItemsFromDefinition();

		ION.initAccordion(
			'.toggler.itemDefinition',
			'.element.itemDefinition',
			true,
			'itemDefinitionAccordion'
		);
	},

	_getDefinitionFieldList: function(item, container)
	{
		var self = this;

		// Fields List
		ION.JSON(
			ION.adminUrl + 'item_definition/get_field_list',
			{
				id_item_definition: item.id_item_definition
			},
			{
				onSuccess: function(json)
				{
					new ION.List({
						container: container,
						items: json,
						sortable:true,
						sort: {
							id_key: 'id_extend_field',
							url: ION.adminUrl + 'extend_field/save_ordering'
						},
						elements:[
							// Drag
							{
								element: 'span',
								'class': 'icon left drag'
							},
							// Main ?
							{
								element:'a',
								'class': 'icon left display flag green ml10 inactive',
								onClick: function(extend)
								{
									ION.JSON(
										ION.adminUrl + 'extend_field/set_main',
										{
											id_extend_field: extend.id_extend_field
										},
										{
											onSuccess: function()
											{
												self._getDefinitionFieldList(item, container);
											}
										}
									);
								}
							},
							// Title
							{
								element: 'a',
								'class': 'title left ml10',
								text: 'name',
								onClick: function(extend)
								{
									self.extendManager.editExtend(
										extend.id_extend_field,
										{
											onSuccess: function()
											{
												self._getDefinitionFieldList(item, container);
											}
										}
									);
								}
							},
							// Delete
							{
								element: 'a',
								'class': 'icon delete right',
								onClick: function(extend)
								{
									self.extendManager.delete(
										extend,
										{
											onSuccess: function(){
												self._getDefinitionFieldList(item, container);
											}
										}
									);
								}
							},
							// Field type
							{
								element: 'span',
								'class' : 'lite right',
								text: 'html_element_type'
							}
						],
						onItemDraw: function(li, extend)
						{
							if (extend.main == 1)
								li.getElement('a.flag').removeClass('inactive');
						}
					});
				}
			}
		);
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

		// if (options && options['id_definition']) this.id_definition = options['id_definition'];

		if (typeOf(options['id_definition']) != 'null')
		{
			ION.JSON(
				ION.adminUrl + 'item_definition/get',
				{
					id_definition: options['id_definition']
				},
				{
					onSuccess: function(definition)
					{
						// Store definition
						self.definition = definition;
						self.id_definition = definition.id_item_definition;

						if (options && options.onSuccess)
							options.onSuccess(definition);
					}
				}
			);
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

		if ($(this.definitionInstanceListContainer))
		{
			this.definitionInstanceListContainer.empty();

			new ION.List(
			{
				container: this.definitionInstanceListContainer,
				items: json,
				sortable:true,
				sort: {
					handler: '.drag',
					id_key: 'id_item',
					url: ION.adminUrl + 'item/save_ordering'
				},
				elements:[
					{element: 'span', 'class': 'icon drag left ml5'}, // Sort
					// {element: 'span', 'class': 'lite drag left', text: 'id_item'}, // Item ID
					// Edit

					{
						element: 'a',
						'class': 'icon edit left',
						onClick: function(item)
						{
							self.editItem(item.id_item);
						}
					},

					// Delete
					{
						element: 'a',
						'class': 'icon delete right mr10',
						onClick: function(item)
						{
							self.deleteItem(item);
						}
					},
					// Title
					{
						// Receives the item and the container's LI
						element: function(item, container)
						{
							var found_main = false;

							Object.each(item.fields, function(f) {
								if (f.main == '1') {
									found_main = true;

									if (f.translated == 1) {
										var w = 100 / Object.getLength(f.lang_data);

										var outerdiv = new Element('div', {'class': 'ml60 mr30'}).inject(container);

										// Each lang div
										Object.each(f.lang_data, function (data, lang_code) {
											var div = new Element('div', {'class': 'left'}).inject(outerdiv);
											div.setStyle('width', w + '%');

											var divImg = new Element('div', {'class': 'left w20'}).inject(div);
											var img = new Element('img', {'class': 'mt3 mb3'}).inject(divImg);
											img.setProperty('src', ION.themeUrl + 'styles/original/images/world_flags/flag_' + lang_code + '.gif');

											var content = data.content.length > 20 ? data.content.substring(0, 35) + '...' : data.content;

											new Element('div', {
												'class': 'ml30',
												text: content
											}).inject(div);
										})
									}
									else {
										var content = f.content.split("\n");
										content = content[0];
										content = content.length > 20 ? content.substring(0, 30) + '...' : content;
										var a = new Element('a', {text: content});
										a.addEvent('click', function(){
											self.editItem(item.id_item);
										});
										container.adopt(a);
									}
									return container;

								}
							});
						}
					}
				]
			});
		}
	},


	createItem: function()
	{
		// Only create one instance if the definition is known
		if (this.id_definition)
		{
			this.getItemForm({
				id_item: ''
			});
		}
		else
		{
			console.log('ION.StaticItemManager.createItem() : No id_definition');
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
							self.getItemForm(item);
						}
					});
				}
			}
		);
	},


	getItemForm: function(item)
	{
		var self = this,
			definition = this.definition,
			id_item = item.id_item
		;

		// Window subtitle
		var subtitle = [];
		if (id_item != '') subtitle.push({key: Lang.get('ionize_label_id'), value: id_item});
		subtitle.push({key: Lang.get('ionize_label_key'), value: definition.name});

		new ION.Window(
		{
			type: 'form',
			form: {
				id: 'itemForm' + id_item,
				action: ION.adminUrl + 'item/save',
				reload: function(json)
				{
					self.editItem(json.id_item);
					self.getItemsFromDefinition();
					self.getParentItemList();
					self.getItemListContent();
				},
				onSuccess: function(json)
				{
					self.getItemsFromDefinition();
					self.getParentItemList();
					self.getItemListContent();
				}
			},
			width: 620,
			height: 380,
			id: 'wItem' + id_item,
			title: {
				text: definition.title_item != '' ? definition.title_item : (id_item != '' ? Lang.get('ionize_title_edit_item') : Lang.get('ionize_title_item_new')),
				'class': 'items'
			},
			subtitle: subtitle,
			onDraw: function(w)
			{
				// FORM
				var form = w.getForm();

				// Extend Field
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
	 * @param	{Object}	item
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
	 * Opens window of all items,
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
		if ($(this.wContainer))
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

			// Edit icon
			var edit = new Element('a', {'class':'icon edit left'}).inject(li);
			edit.addEvent('click', function()
			{
				self.editItem(id);
			});

			var content = field.content.split("\n");
			content = content[0];
			content = content.length > 20 ? content.substring(0, 20) + '...' : content;

			// Title
			var title = new Element('a', {
				'class': 'left title unselectable',
				'text': content
			}).inject(li);

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
		var container = this.parentListContainer;

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

							// Edit icon
							var edit = new Element('a', {'class':'icon edit left mr10'}).inject(li);
							edit.addEvent('click', function()
							{
								self.editItem(id);
							});

							var content = field.content.split("\n");
							content = content[0];
							content = content.length > 20 ? content.substring(0, 20) + '...' : content;

							// Title
							var title = new Element('a', {
								'class': 'left title unselectable',
								'text': content
							}).inject(li);

							// Unlink icon
							var delIcon = new Element('a', {'class':'icon unlink right'}).inject(li);
							delIcon.addEvent('click', function(){ self.unlinkItemfromParent(id); });
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
		var container = this.parentListContainer;
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
		var container = this.parentListContainer;

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
		else
		{
		}
	}
});
