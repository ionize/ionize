/**
 * Ionize Static Item Manager
 *
 */
ION.ContentTypeManager = new Class({

	Implements: [Events, Options],

	/**
	 * Content Type list container
	 */
	typeListContainer: null,

	extendManager: null,

	contentElementManager: null,


	/**
	 *
	 * @param options
	 */
	initialize: function(options)
	{
		this.setOptions(options);

		// Extend Manager
		this.extendManager = new ION.ExtendManager();
		this.contentElementManager = new ION.ContentElementManager();
	},


	getMainPanel: function()
	{
		var self = this;

		// Close all windows
		MUI.Windows.closeAll();

		// Empty toolbox
		ION.getToolbox();

		// Split panels
		MUI.Content.update(
		{
			element: 'mainPanel',
			title: Lang.get('ionize_title_content_types'),
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
							cssClass:'contentTypePanel',
							onDrawEnd:function(p)
							{
								var container = p.el.content;
								container.addClass('p20');

								self.panel = container;

								// Title
								new ION.WindowTitle({
									container: container,
									title: Lang.get('ionize_title_content_types'),
									subtitle: [{html:Lang.get('ionize_title_content_types_intro')}],
									'class': 'content_types'
								});

								// Intro
								var div = new Element('div', {'class':'mt30 ml30 mr30 pl20'}).inject(container);
								new Element('p', {'class':'lite', html:Lang.get('ionize_help_content_types')}).inject(div);
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
							'title': '',
							id: 'splitPanel_definition',
							cssClass: 'panelAlt',
							onDrawEnd: function(p)
							{
								// Container
								var c = p.el.content,
									h = p.el.header;

								c.addClass('p20');

								self.typeListContainer = c;

								// Button : Create
								new ION.Button({
									container: h,
									title: Lang.get('ionize_title_new_content_type'),
									'class': 'right mt2',
									icon: 'icon-plus',
									attributes: {
										'title': Lang.get('ionize_title_new_definition')
									},
									onClick: function()
									{
										self.createType();
									}
								});

								// Extend List
								self.getTypeList();
							}
						}
					]
				}
			]
		});
	},


	getTypeList: function()
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'content_type/get_list',
			{},
			{
				onSuccess: function(json)
				{
					self.typeListContainer.empty();

					var groups = self._getContentTypeByType(json);

					Object.each(groups, function(group)
					{
						var h = new Element('h3', {
							'class': 'toggler toggler-content-type mt0 mb5',
							'text': group.title
						}).inject(self.typeListContainer);

						var container = new Element('div', {'class':'element element-content-type'}).inject(self.typeListContainer);

						new ION.List(
						{
							container: container,
							items: group.items,
							'class': 'mb20',
							elements:[
								// Title
								{
									element: 'a',
									'class': 'title left',
									text: 'name',
									onClick: function(item)
									{
										self.editType(item);
									}
								},
								// Delete
								{
									element: 'a',
									'class': 'icon delete right',
									onClick: function(item)
									{
										self.deleteType(item);
									}
								}
							]
						});
					});

					ION.initAccordion(
						'.toggler.toggler-content-type',
						'.element.element-content-type',
						true,
						'wContentTypesAccordion'
					);
				}
			}
		);
	},


	createType: function()
	{
		var item={
			id_content_type: '',
			name: '',
			view: null,
			view_single: null
		};

		this.editType(item);
	},


	editType: function(item)
	{
		var self = this,
			container = this.panel;

		// Empty container
		container.empty();

		// Title
		var title = new ION.WindowTitle({
			container: container,
			title: item.name != '' ? item.name : Lang.get('ionize_title_new_content_type'),
			subtitle: [
				{key: 'ID', value: item.id_content_type}
			],
			'class': 'content_types'
		});

		var form = new Element('form', {class:'mt20'}).inject(container),
			ff_name = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_name'), for:'content_type_name' + item.id_content_type}}),
			ff_type = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_type')}}),
			div_views = new Element('div').inject(form),
			div_views_intro = new Element('div', {class:'mb10'}).inject(div_views),
			ff_view = new ION.FormField({container: div_views, label: {text: Lang.get('ionize_label_view')}}),
			div_view = new Element('div').inject(ff_view.getContainer()),
			ff_view_single = new ION.FormField({container: div_views, label: {text: Lang.get('ionize_label_page_single_view')}}),
			div_view_single = new Element('div').inject(ff_view_single.getContainer()),
			ff_save = new ION.FormField({'class':'mt10', container: form}),
			input_name = new Element('input', {id:'content_type_name' + item.id_content_type, class:'inputtext required w200', name:'name', value:item.name}).inject(ff_name.getContainer())
		;

		div_views.hide();

		new Element('h3', {class:'mb0', html:Lang.get('ionize_title_content_types_views')}).inject(div_views_intro);
		new Element('p', {class:'lite', html:Lang.get('ionize_help_content_types_views')}).inject(div_views_intro);

		var viewSelect = new ION.Form.Select({
			name:'view',
			container: div_view,
			url: ION.adminUrl + 'page/get_views',
			selected: item.view,
			key: 'key',
			label: 'label',
			// fireOnInit: true,
			onChange: function(value, data, selected)
			{
				self.updateTypeField(item, 'view', value);
			}
		});

		var singleViewSelect = new ION.Form.Select({
			name:'view_single',
			container: div_view_single,
			url: ION.adminUrl + 'page/get_views',
			selected: item.view_single,
			key: 'key',
			label: 'label',
			// fireOnInit: true,
			onChange: function(value, data, selected)
			{
				self.updateTypeField(item, 'view_single', value);
			}
		});

		new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_view')}).inject(ff_view.getContainer());
		new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_view_single')}).inject(ff_view_single.getContainer());


		if (item.id_content_type == '')
		{
			new ION.Form.Select({
				name:'type',
				container: ff_type.getContainer(),
				data: [
					{key: 'page', label: Lang.get('ionize_label_page')},
					{key: 'article', label: Lang.get('ionize_label_article')}
				],
				key: 'key',
				label: 'label',
				fireOnInit: true,
				onChange: function(value, data, selected)
				{
					if (value == 'page')
					{
						div_views.show();
					}
					else
					{
						div_views.hide();
					}
				}
			});

			title.setSubtitle([
				{html: Lang.get('ionize_help_content_type_new')}
			]);

			// Help
			new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_type')}).inject(ff_type.getContainer());
			new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_name')}).inject(ff_name.getContainer());

			var submitButton = new ION.Button({
				title: Lang.get('ionize_label_save_content_type'),
				'class': 'button light green',
				container: ff_save.getContainer()
			});

			ION.setFormSubmit(
				form,
				submitButton.getElement(),
				ION.adminUrl + 'content_type/save',
				false,
				{
					onSuccess: function(json)
					{
						if (typeOf(json.type) != 'null')
						{
							self.editType(json.type);

							self.getTypeList();
						}
					}
				}
			);
		}
		else
		{
			// Dynamical title
			title.setSubtitle([
				{key: 'ID', value: item.id_content_type},
				{key: 'Type', value: String.capitalize(item.type)}
			]);

			title.getTitle().set('contenteditable', 'true');

			title.getTitle().addEvent('blur', function()
			{
				if (this.get('text') != '')
					self.updateTypeName(item, this.get('text'));
				else
					ION.error(Lang.get('ionize_message_please_set_a_content_type_name'));
			});

			ff_name.getDomElement().destroy();
			ff_type.getDomElement().destroy();
			ff_save.getDomElement().destroy();

			ff_view.show();
			ff_view_single.show();

			if (item.type != 'page')
				div_views.hide();
			else
				div_views.show();

			input_name.addEvent('blur', function()
			{
				item.name = input_name.value;
				ION.JSON(
					ION.adminUrl + 'content_type/save',
					item,
					{
						onSuccess: function()
						{
							self.getTypeList();
						}
					}
				);
			});

			// Set current type to extend manager
			self.extendManager.closeListWindow();
			self.extendManager.init({
				parent: item.type
			});

			var cont_group = new Element('div', {class:''}).inject(form),
				cont_group_intro = new Element('div', {class:''}).inject(cont_group),
				cont_group_content = new Element('div', {class:''}).inject(cont_group),
				ff_new_group = new ION.FormField({container: cont_group_content, label: {text: Lang.get('ionize_label_content_type_new_group')}}),
				ff_groups = new ION.FormField({container: cont_group_content, label: {text: Lang.get('ionize_label_content_type_groups')}}),
				div_groups = new Element('div', {class:'mt10'}).inject(ff_groups.getContainer()),
				div_new_group = new Element('div', {class:'mt10 h30'}).inject(ff_new_group.getContainer()),
				div_new_group_input = new Element('div', {class:'left mr15'}).inject(div_new_group),
				div_new_group_btn = new Element('div', {class:'left'}).inject(div_new_group),
				input_new_group = new Element('input', {class:'inputtext w180'}).inject(div_new_group_input)
			;

			ff_groups.hide();

			new Element('h3', {class:'mb0 mt20', html:Lang.get('ionize_label_title_type_groups')}).inject(cont_group_intro);
			new Element('p', {class:'lite', html:Lang.get('ionize_help_content_types_groups')}).inject(cont_group_intro);


			this.getGroupManagementList(item, div_groups);

			new ION.Button({
				title: Lang.get('ionize_label_content_type_add_new_group'),
				'class': 'button light blue',
				container: div_new_group_btn,
				onClick: function()
				{
					self.addNewGroup(item, input_new_group.value);
				}
			});
		}
	},


	updateTypeField: function(item, field, value)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'content_type/update_field',
			{
				id_content_type: item.id_content_type,
				field: field,
				value: value
			},
			{
				onSuccess: function()
				{
					ION.notification('success', Lang.get('ionize_message_operation_ok'));
					self.getTypeList();
				}
			}
		);
	},


	updateTypeName: function(item, name)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'content_type/update_name',
			{
				id_content_type: item.id_content_type,
				name: name
			},
			{
				onSuccess: function()
				{
					ION.notification('success', Lang.get('ionize_message_operation_ok'));
					self.getTypeList();
				}
			}
		);
	},


	addNewGroup: function(item, name)
	{
		var self = this;

		if (name != '')
		{
			ION.JSON(
				ION.adminUrl + 'content_type/add_group',
				{
					id_content_type: item.id_content_type,
					name: name
				},
				{
					onSuccess: function(json)
					{
						self.editType(item);
					}
				}
			);
		}
		else
		{
			ION.error(Lang.get('ionize_message_please_set_a_group_name'));
		}
	},


	updateGroup: function(group, field, value)
	{
		ION.JSON(
			ION.adminUrl + 'content_type/update_group',
			{
				id_content_type_group: group.id_content_type_group,
				field: field,
				value: value
			},
			{}
		);
	},


	/**
	 *
	 * @param item			Content Type
	 * @param container
	 */
	getGroupManagementList: function(item, container)
	{
		var self = this;

		container.empty();

		this.getGroupsWithItems(
			item,
			{
				onSuccess: function(groups)
				{
					if (groups.length > 0)
 					{
						container.getParent('dl').show();

						// Groups
						Array.each(groups, function(group)
						{
							var div = new Element('div', {id:'contentTypeGroup'+group.id_content_type_group, 'data-id':group.id_content_type_group, 'class':'list pt5 mb15 clearfix'}).inject(container),
								div_title = new Element('div', {class:'h30'}).inject(div),
								delGroupBtn = new Element('a', {class:'icon delete absolute right mr5'}).inject(div_title),
								btn_order = new Element('a', {class:'icon drag left mr10'}).inject(div_title),
								h3 = new Element('h3', {class:'mt0 mb5 no-clear left', html:group.name, contenteditable:true}).inject(div_title),

								ff_extends = new ION.FormField({container: div, 'class':'small', label: {text: Lang.get('ionize_label_extends')}}),
								extend_container = new Element('div', {'class':''}).inject(ff_extends.getContainer()),
								add_extend_container = new Element('div', {class:'mt3 clearfix'}).inject(ff_extends.getContainer()),

								ff_content_elements = new ION.FormField({container: div, 'class':'mt10 small', label: {text: Lang.get('ionize_menu_content_elements')}}),
								content_element_container = new Element('div', {'class':''}).inject(ff_content_elements.getContainer()),
								add_content_element_container = new Element('div', {class:'mt3 clearfix'}).inject(ff_content_elements.getContainer())
								;

							// Add Extends
							new ION.Button({
								title: Lang.get('ionize_label_content_type_add_extend_to_group'),
								container: add_extend_container,
								icon: 'icon-plus',
								'class': 'button light',
								onClick: function()
								{
									self.extendManager.openListWindow();
								}
							});

							// Add Content Elements
							new ION.Button({
								title: Lang.get('ionize_label_content_type_add_content_element_to_group'),
								container: add_content_element_container,
								icon: 'icon-plus',
								'class': 'button light',
								onClick: function()
								{
									self.contentElementManager.openDefinitionListWindow();
								}
							});

							// DeleteGroup button
							delGroupBtn.addEvent('click', function() {
								self.deleteGroup(group, item, container);
							});

							h3.addEvent('blur', function()
							{
								self.updateGroup(group, 'name', h3.get('text'));
							});

							// Extends List
							new ION.List({
								container: extend_container,
								items: group.fields,
								sortable: true,
								droppable: true,
								dropOn: 'dropExtend',
								onDrop: function(element)
								{
									var extend = element.retrieve('data');

									self.linkItemWithGroup(
										'extend_field',
										extend.id_extend_field,
										group,
										groups,
										{
											onSuccess: function()
											{
												self.getGroupManagementList(item, container);
											}
										}
									);
								},
								sort: {
									handler: '.drag',
									id_key: 'id_extend_field',
									callback: function(serie)
									{
										ION.JSON(
											ION.adminUrl + 'content_type/save_item_ordering',
											{
												order: serie,
												item: 'extend_field',
												id_content_type_group: group.id_content_type_group
											},
											{}
										);
									}
								},
								post:{
									id_content_group: group.id_content_group
								},
								elements: [
									// Drag
									{
										element: 'a',
										'class': 'icon drag left'
									},
									// Title
									{
										element: 'a',
										'class': 'title left',
										text: 'name',
										onClick: function(extend)
										{
											self.extendManager.editExtend(
												extend.id_extend_field,
												{
													onSuccess: function()
													{
														self.getGroupManagementList(item, container);
													}
												}
											);
										}
									},
									// Unlink
									{
										element: 'a',
										'class': 'icon unlink right',
										onClick: function(extend)
										{
											self.unlinkItemFromGroup(
												'extend_field',
												extend.id_extend_field,
												group,
												{
													onSuccess: function()
													{
														self.getGroupManagementList(item, container);
													}
												}
											);
										}
									},
									// Extend Type
									{
										element: 'span',
										'class': 'lite right',
										text: 'type_name'
									}
								]
							});

							// Content Element List
							new ION.List({
								container: content_element_container,
								items: group.elements,
								sortable: true,
								droppable: true,
								dropOn: 'dropContentElement',
								onDrop: function(element)
								{
									element = element.retrieve('data');

									self.linkItemWithGroup(
										'element',
										element.id_element_definition,
										group,
										groups,
										{
											onSuccess: function()
											{
												self.getGroupManagementList(item, container);
											}
										}
									);
								},
								sort: {
									handler: '.drag',
									id_key: 'id_element_definition',
									callback: function(serie)
									{
										ION.JSON(
											ION.adminUrl + 'content_type/save_item_ordering',
											{
												order: serie,
												item:'element',
												id_content_type_group: group.id_content_type_group
											},
											{}
										);
									}
								},
								post:{
									id_content_group: group.id_content_group
								},
								elements: [
									// Drag
									{
										element: 'a',
										'class': 'icon drag left'
									},
									// Title
									{
										element: 'a',
										'class': 'title left',
										text: 'title'
									},
									// Unlink
									{
										element: 'a',
										'class': 'icon unlink right',
										onClick: function(element)
										{
											self.unlinkItemFromGroup(
												'element',
												element.id_element_definition,
												group,
												{
													onSuccess: function()
													{
														self.getGroupManagementList(item, container);
													}
												}
											);
										}
									}
								]
							});

						});

						// Order Groups
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
								self.sortGroups(serialized);
							}
						});
					}
				}
			}
		);
	},


	sortGroups: function(serialized)
	{
		var serie = [];
		serialized.each(function(item)
		{
			if (typeOf(item) != 'null')	serie.push(item);
		});

		ION.JSON(
			ION.adminUrl + 'content_type/save_group_ordering',
			{
				order:serie
			},
			{}
		);
	},


	deleteGroup: function(group, item, container)
	{
		var self = this;

		ION.confirmation(
			'wContentTypeGroupDelete' + group.id_content_type_group,
			function()
			{
				ION.JSON(
					ION.adminUrl + 'content_type/delete_group',
					{
						id_content_type_group: group.id_content_type_group
					},
					{
						onSuccess: function()
						{
							self.getGroupManagementList(item, container);
						}
					}
				);
			},
			Lang.get('ionize_confirm_content_type_group_delete')
		);
	},


	getGroupsWithItems: function(item)
	{
		var options = typeOf(arguments[1]) != 'null' ? arguments[1] : {};

		ION.JSON(
			ION.adminUrl + 'content_type/get_groups_with_items',
			{
				id_content_type: item.id_content_type
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


	linkItemWithGroup: function(item, id_item, group, groups)
	{
		var self = this,
			options = typeOf(arguments[4]) != 'null' ? arguments[4] : {},
			isInOtherGroup = false;

		if (item == 'extend_field')
		{
			Array.each(groups, function(group)
			{
				Array.each(group.fields, function(field)
				{
					if (field.id_extend_field == id_item)
					{
						isInOtherGroup = true;
					}
				});
			});
		}

		if (item == 'element')
		{
			Array.each(groups, function(group)
			{
				Array.each(group.elements, function(element)
				{
					if (element.id_element_definition == id_item)
					{
						isInOtherGroup = true;
					}
				});
			});
		}


		if (isInOtherGroup == false)
		{
			ION.JSON(
				ION.adminUrl + 'content_type/link_item_with_group',
				{
					item: item,
					id_item: id_item,
					id_content_type_group: group.id_content_type_group
				},
				{
					onSuccess: function ()
					{
						if (options.onSuccess)
							options.onSuccess();
					}
				}
			);
		}
		else
		{
			ION.notification('error', Lang.get('ionize_message_content_type_item_already_linked_to_another_group'));
		}
	},


	unlinkItemFromGroup: function(item, id_item, group)
	{
		var self = this,
			options = typeOf(arguments[3]) != 'null' ? arguments[3] : {};


		ION.JSON(
			ION.adminUrl + 'content_type/unlink_item_from_group',
			{
				item: item,
				id_item: id_item,
				id_content_type_group: group.id_content_type_group
			},
			{
				onSuccess: function()
				{
					if (options.onSuccess)
						options.onSuccess();
				}
			}
		);
	},


	deleteType: function(item)
	{
		var self = this;

		ION.confirmation(
			'wContentTypeDelete' + item.id_content_type,
			function()
			{
				ION.JSON(
					ION.adminUrl + 'content_type/delete',
					{
						id_content_type: item.id_content_type
					},
					{
						onSuccess: function()
						{
							self.getMainPanel();
						}
					}
				);
			},
			Lang.get('ionize_confirm_content_type_delete', item.type)
		);
	},


	_getContentTypeByType: function(list)
	{
		var data = {};

		Object.each(list, function(item)
		{
			if (typeOf(data[item.type]) == 'null')
			{
				data[item.type] = {
					title: String.capitalize(item.type),
					items: []
				};
			}
			data[item.type]['items'].push(item);
		});

		return data;
	},


	/*
	 * Display on Page / Article Edition panel
	 *
	 */
	displayInParent: function(options)
	{
		var self = this,
			container = typeOf(options.container) != 'null' ? $(options.container) : null,
			id_content_type = options.id_content_type != '' ? options.id_content_type : null;

		this.processed_extend_ids = [];

		if (container != null && id_content_type != null)
		{
			this.extendManager.init({
				parent: this.options.type,
				id_parent: this.options.id_parent
			});

			this.getGroupsWithItems(
				{id_content_type: id_content_type},
				{
					onSuccess: function(groups)
					{
						// Build Tabs
						var tabSwapper = container.retrieve('tabSwapper'),		// Old tabs
							tabsInstance = container.retrieve('tabsInstance'),	// New tabs
							section = null;

						self.extendManager.getParentInstances(
						{
							onSuccess: function(extend_fields)
							{
								if (typeOf(tabSwapper) != 'null')
								{
									Array.each(groups, function(group)
									{
										if (group.fields.length > 0 || group.elements.length > 0)
										{
											section = tabSwapper.addNewTab(
												group.name,
												self.options.type,
												{
													'class': 'extend',       // be able to identify it as extend tab
													'data-parent': self.options.type
												}
											);
											section.addClass('mt20');
											section.addClass('p10');

											section.setProperty('id', 'tabSwapper' + self.options.type + self.options.id_type);

											if (group.fields.length > 0)
											{
												var extend_container = new Element('div', {class:''}).inject(section);

												self.buildParentGroupExtendFields(group, extend_fields, extend_container);
											}

											if (group.elements.length > 0)
											{
												var element_container = new Element('div', {class:''}).inject(section);
												self.buildParentGroupElements(group, group.elements, element_container);
											}
										}
									});
								}
								else if (typeOf(tabsInstance) != 'null')
								{
									Array.each(groups, function(group)
									{
										if (group.fields.length > 0 || group.elements.length > 0)
										{
											tabsInstance.addTab({
												label: group.name,
												onLoaded: function (tab, s)
												{
													var section = s;
													section.addClass('pt15');

													if (group.fields.length > 0)
													{
														var extend_container = new Element('div', {class: ''}).inject(section);
														self.buildParentGroupExtendFields(group, extend_fields, extend_container);
													}

													if (group.elements.length > 0)
													{
														var element_container = new Element('div', {class: ''}).inject(section);
														self.buildParentGroupElements(group, group.elements, element_container);
													}
												}
											});
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


	buildParentGroupExtendFields: function(group, extend_fields, container)
	{
		var self = this,
			_fields = [];

		Array.each(group.fields, function(field)
		{
			Array.each(extend_fields, function(extend_field)
			{
				if (
					extend_field.id_extend_field == field.id_extend_field
					&& ! self.processed_extend_ids.contains(field.id_extend_field)
				)
				{
					_fields.push(extend_field);
					self.processed_extend_ids.push(field.id_extend_field);
				}
			});
		});

		if (_fields.length > 0)
			self.extendManager.buildInstancesList(_fields, container);
	},


	buildParentGroupElements: function(group, elements, container)
	{
		var self = this,
			parent = this.options.type,
			id_parent = this.options.id_parent
		;

		container.empty();

		// Save Event
		self.contentElementManager.addEvent('save', function(instance)
		{
			self.buildParentGroupElements(group, elements, container);
		});

		// Foreach Definition
		Object.each(elements, function(element)
		{
			var div = new Element('div', {class:''}).inject(container),
				div_buttons = new Element('div', {class:'clearfix mb5'}).inject(div),
				div_title = new Element('h3', {'class': 'left m0 mt5', html:element.title}).inject(div_buttons),
				elements_container = new Element('div', {class:''}).inject(container)
			;

			// Create New Content Element
			new ION.Button({
				container: div_buttons,
				title: Lang.get('ionize_button_add') + ' ' + element.title,
				'class': 'right light',
				icon: 'icon-plus',
				onClick: function()
				{
					self.contentElementManager.createInstance(
						element, parent, id_parent
					);
				}
			});

			self.contentElementManager.getParentElementsFromDefinition(
				elements_container, parent, id_parent, element.id_element_definition
			);
		});
	}

});
