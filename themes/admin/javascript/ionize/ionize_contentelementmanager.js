/**
 * Ionize Content Element Manager
 *
 */
ION.ContentElementManager = new Class({

	Implements: [Events, Options],

	extendManager: null,

	/**
	 *
	 * @param options
	 */
	initialize: function(options)
	{
		this.setOptions(options);

		// Extend Manager
		this.extendManager = new ION.ExtendManager();
	},


	/**
	 * Deletes one instance
	 *
	 * @param element
	 */
	delete: function(element)
	{
		var options = typeOf(arguments[1]) != 'null' ? arguments[1] : {};

		ION.confirmation(
			'wContentElementDelete' + element.id_element,
			function()
			{
				ION.JSON(
					ION.adminUrl + 'element/delete',
					{
						id_element: element.id_element
					},
					{
						onSuccess: function(json)
						{
							if (options.onSuccess)
								options.onSuccess(json);
						}
					}
				)
			},
			Lang.get('ionize_confirm_extend_delete')
		);
	},


	/**
	 * Returns all Content Element definitions
	 *
	 */
	getDefinitions: function()
	{
		var options = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

		ION.JSON(
			ION.adminUrl + 'element_definition/get_definitions',
			{},
			{
				onSuccess: function(json)
				{
					if (options.onSuccess)
						options.onSuccess(json);
				}
			}
		);
	},


	openDefinitionListWindow: function()
	{
		var self = this;

		// Mocha Window
		this.w = new MUI.Window({
			id: 'wContentElementList',
			title: Lang.get('ionize_title_content_element_list'),
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
		var container = $('wContentElementList_content');

		var h2 = new Element('h2', {
			'class': 'main elements',
			'text' :  Lang.get('ionize_title_content_element_list')
		}).inject(container);


		// List container
		var listContainer = new Element('div').inject(container, 'bottom');


		this.getDefinitions(
		{
			onSuccess: function(definitions)
			{
				new ION.List({
					container: listContainer,
					items: definitions,
					// droppable: true,
					dragOn: '.dropContentElement',
					elements: [
						// Title
						{
							element: 'a',
							'class': 'title left',
							text: 'title',
						}
					]
				});
			}
		});
	},


	/*
	 * Page / Article display methods
	 *
	 */

	/**
	 * Display on Page / Article Edition panel
	 * @param options
	 */
	displayInParentAsTabs: function(options)
	{
		var self = this,
			parent = typeOf(options.parent) != 'null' ? options.parent : null,
			id_parent = typeOf(options.id_parent) != 'null' ? options.id_parent : null,
			container = typeOf(options.container) != 'null' ? $(options.container) : null
		;

		// Build Tabs
		var tabSwapper = container.retrieve('tabSwapper'),		// Old tabs
			tabsInstance = container.retrieve('tabsInstance')	// New tabs
		;

		if (parent != null && id_parent != null)
		{
			this.extendManager.init({
				parent: parent,
				id_parent: id_parent
			});

			this.getParentDefinitions(
				parent,
				id_parent,
				{
					onSuccess: function(definitions)
					{
						Object.each(definitions, function(definition)
						{
							var id = definition.id_element_definition,
								id_tab = definition.name + definition.id_element_definition,
								section = null
							;


							// Old tabs
							if (typeOf(tabSwapper) != 'null')
							{
								if (tabSwapper.hasTabId(id_tab))
								{
									section = $('tabSwapper' + id_tab);
								}
								else
								{
									section = tabSwapper.addNewTab(
										definition.title,
										id_tab,
										{
											'class': 'element'
										}
									);

									section.addClass('mt20');
									section.addClass('p10');
									section.setProperty('id', 'tabSwapper' + id_tab);
								}

								self.getParentElementsFromDefinition(
									section,
									parent,
									id_parent,
									definition.id_element_definition
								);
							}
							// New tabs
							else
							{
								if (tabsInstance.hasTab('id', id_tab))
								{
									section = tabsInstance.getSection('id', id_tab);

									self.getParentElementsFromDefinition(
										section,
										parent,
										id_parent,
										definition.id_element_definition
									);
								}
								else
								{
									tabsInstance.addTab({
										label: group.name,
										id:id_tab,
										onLoaded: function (tab, s)
										{
											var section = s;
											section.addClass('pt15');

											self.getParentElementsFromDefinition(
												section,
												parent,
												id_parent,
												definition.id_element_definition
											);
										}
									});
								}
							}
						});
					}
				}
			);
		}
	},


	getParentElementsFromDefinition: function(container, parent, id_parent, id_element_definition)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'element/get_elements_from_definition',
			{
				parent: parent,
				id_parent: id_parent,
				id_element_definition: id_element_definition
			},
			{
				onSuccess: function(json)
				{
					if (options.onSuccess)
						options.onSuccess(
							json,
							{
								container: container,
								parent: parent,
								id_parent: id_parent,
								id_element_definition: id_element_definition
							}
						);
					else
					{
						container.empty();

						Object.each(json.elements, function(element)
						{
							var div = new Element('div', {id:'contentElement'+element.id_element, 'data-id': element.id_element, class:'list mb10'}).inject(container),
								buttons_container = new Element('div', {class:'h25'}).inject(div),
								extend_container = new Element('div', {class:''}).inject(div),
								btn_del = new Element('a', {class:'icon delete right'}).inject(buttons_container),
								btn_order = new Element('a', {class:'icon drag left'}).inject(buttons_container),
								btn_edit = new Element('a', {class:'left ml10', html:Lang.get('ionize_title_element_edit')}).inject(buttons_container),
								fields = [];

							// Edit
							btn_edit.addEvent('click', function(){
								self.editInstance(element);
							});

							// Delete
							btn_del.addEvent('click', function(){
								self.delete(
									element,
									{
										onSuccess: function(json)
										{
											self.getParentElementsFromDefinition(container, parent, id_parent, id_element_definition);
										}
									}
								);
							});

							// Extends
							self.extendManager.init({
								parent: 'element',
								id_parent: element.id_element
							});

							Object.each(element.fields, function(extend)
							{
								fields.push(extend);
							});

							self.extendManager.buildInstancesList(fields, extend_container, {readOnly:true});
						});

						new Sortables(container,
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
								self.sortElementsForParent(serialized);
							}
						});
					}
				}
			}
		);
	},


	sortElementsForParent: function(serialized)
	{
		var serie = [];
		serialized.each(function(item)
		{
			if (typeOf(item) != 'null')	serie.push(item);
		});

		ION.JSON(
			ION.adminUrl + 'element/save_ordering',
			{
				order:serie
			},
			{}
		);
	},


	getParentDefinitions: function(parent, id_parent)
	{
		var options = typeOf(arguments[2]) != 'null' ? arguments[2] : {};

		ION.JSON(
			ION.adminUrl + 'element_definition/get_definitions_from_parent',
			{
				parent: parent,
				id_parent: id_parent
			},
			{
				onSuccess: function(json)
				{
					if (options.onSuccess)
						options.onSuccess(json);
				}
			}
		);
	},



	createInstance: function(id_element_definition, parent, id_parent)
	{
		var instance = {
			id_element: '',
			id_element_definition: id_element_definition,
			parent: parent,
			id_parent: id_parent
		};

		this.editInstance(instance);
	},


	editInstance: function(instance)
	{
		var self = this,
			subtitle = null;

		if (instance.id_element)
			subtitle = [
				{key: 'ID', value:instance.id_element}
			];

		new ION.Window({
			id: 'editElementInstance' + instance.id_element,
			title: {
				text: instance.id_element != '' ? Lang.get('ionize_label_content_element') : Lang.get('ionize_title_element_new'),
				'class': 'elements'
			},
			subtitle: subtitle,
			type: 'form',
			form: {
				id: 'elementForm' + instance.id_element,
				action: ION.adminUrl + 'element/save',
				reload: function(json)
				{
					self.editInstance(json);
				},
				onSuccess: function(json)
				{
					self.fireEvent('onSave', json);
				}
			},
			width: 620,
			height: 380,
			onDraw: function(w)
			{
				var form = w.getForm();

				form.addClass('mt20');

				new Element('input', {type:'hidden', name:'id_element', value: instance.id_element}).inject(form);
				new Element('input', {type:'hidden', name:'parent', value: instance.parent}).inject(form);
				new Element('input', {type:'hidden', name:'id_parent', value: instance.id_parent}).inject(form);
				new Element('input', {type:'hidden', name:'id_element_definition', value: instance.id_element_definition}).inject(form);

				if( typeOf(instance.ordering) != 'null')
				{
					new Element('input', {type: 'hidden', name: 'ordering', value: instance.ordering}).inject(form);
				}
				else
				{
					var ff_order = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_ordering')}});

					new ION.Form.Select(
					{
						container: ff_order.getContainer(),
						name: 'ordering',
						data: [
							{value:'first', label:Lang.get('ionize_label_ordering_first')},
							{value:'last', label:Lang.get('ionize_label_ordering_last')}
						],
						key: 'value',
						label: 'label'
					});
				}

				var fields_container = new Element('div', {class:''}).inject(form);

				self.extendManager.init({
					parent: 'element',
					id_parent: instance.id_element,
					id_field_parent: instance.id_element_definition,
					destination: fields_container
				});

				self.extendManager.getParentInstances();
			}
		});
	}
});
