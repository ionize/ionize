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
					new ION.List(
					{
						container: self.typeListContainer,
						items: json,
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
							},
							// Type
							{
								element: 'span',
								'class': 'lite right',
								text: 'type'
							}
						]
					});
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
			ff_view = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_view')}}),
			div_view = new Element('div').inject(ff_view.getContainer()),
			ff_view_single = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_page_single_view')}}),
			div_view_single = new Element('div').inject(ff_view_single.getContainer()),
			ff_save = new ION.FormField({'class':'mt10', container: form}),
			input_name = new Element('input', {id:'content_type_name' + item.id_content_type, class:'inputtext required w200', name:'name', value:item.name}).inject(ff_name.getContainer())
		;

		ff_view.hide();
		ff_view_single.hide();

		var viewSelect = new ION.Form.Select({
			name:'view',
			container: div_view,
			url: ION.adminUrl + 'page/get_views',
			selected: item.view,
			key: 'key',
			label: 'label',
			fireOnInit: true,
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
			fireOnInit: true,
			onChange: function(value, data, selected)
			{
				self.updateTypeField(item, 'view_single', value);
			}
		});


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
						ff_view.show();
						ff_view_single.show();
					}
					else
					{
						ff_view.hide();
						ff_view_single.hide();
					}
				}
			});

			title.setSubtitle([
				{html: Lang.get('ionize_help_content_type_new')}
			]);

			// Help
			new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_type')}).inject(ff_type.getContainer());
			new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_name')}).inject(ff_name.getContainer());
			new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_view')}).inject(ff_view.getContainer());
			new Element('p', {'class': 'lite', html: Lang.get('ionize_help_content_type_view_single')}).inject(ff_view_single.getContainer());

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
			{
				ff_view.hide();
				ff_view_single.hide();
			}

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

			var ff_groups = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_content_type_groups')}}),
				ff_new_group = new ION.FormField({container: form, label: {text: Lang.get('ionize_label_content_type_new_group')}}),
				div_groups = new Element('div', {class:'mt10'}).inject(ff_groups.getContainer()),
				div_new_group = new Element('div', {class:'mt10 h30'}).inject(ff_new_group.getContainer()),
				div_new_group_input = new Element('div', {class:'left mr15'}).inject(div_new_group),
				div_new_group_btn = new Element('div', {class:'left'}).inject(div_new_group),
				input_new_group = new Element('input', {class:'inputtext w180'}).inject(div_new_group_input)
			;

			ff_groups.hide();

			this.getGroupAndExtendsList(item, div_groups);

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


	getGroupAndExtendsList: function(item, container)
	{
		var self = this;

		container.empty();

		this.getGroupAndExtends(
			item,
			{
				onSuccess: function(groups)
				{
					if (groups.length == 0)
					{
						// container.getParent('dl').destroy();
					}
					else
					{
						container.getParent('dl').show();

						Array.each(groups, function(group)
						{
							var div = new Element('div', {class:'pt5 mb15'}).inject(container),
								div_title = new Element('div', {class:''}).inject(div),
								delGroupBtn = new Element('a', {class:'icon delete absolute right'}).inject(div_title),
								h3 = new Element('h3', {class:'mt0 mb5 no-clear', html:group.name, contenteditable:true}).inject(div_title),
								ul_container = new Element('div').inject(div),
								add_extend_container = new Element('div', {class:'ml50 mt3'}).inject(div),
								addBtn = new Element('a', {html:Lang.get('ionize_label_content_type_add_extend_to_group')}).inject(add_extend_container)
								;

							// DeleteGroup button
							delGroupBtn.addEvent('click', function() {
								self.deleteGroup(group, item, container);
							});

							h3.addEvent('blur', function()
							{
								self.updateGroup(group, 'name', h3.get('text'));
							});


							// Open Extend window
							addBtn.addEvent('click', function() {
								self.extendManager.openListWindow();
							});

							var ul = new ION.List({
								container: ul_container,
								items: group.fields,
								sortable: true,
								droppable: true,
								dropOn: 'dropExtend',
								onDrop: function(element)
								{
									self.linkExtendWithGroup(element.retrieve('data'), group, item, container);
								},
								sort: {
									handler: '.drag',
									id_key: 'id_extend_field',
									callback: function(serie)
									{
										ION.JSON(
											ION.adminUrl + 'content_type/save_extend_ordering',
											{
												order: serie,
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
										onClick: function(item)
										{
											self.editType(item);
										}
									},
									// Unlink
									{
										element: 'a',
										'class': 'icon unlink right',
										onClick: function(extend)
										{
											self.unlinkExtendFromGroup(extend, group, item, container);
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
						});
					}
				}
			}
		);
	},


	deleteGroup: function(group, item, container)
	{
		var self = this;

		console.log(group);

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
							self.getGroupAndExtendsList(item, container);
						}
					}
				);
			},
			Lang.get('ionize_confirm_content_type_group_delete')
		);
	},


	getGroupAndExtends: function(item)
	{
		var options = typeOf(arguments[1]) != 'null' ? arguments[1] : {};

		ION.JSON(
			ION.adminUrl + 'content_type/get_extends_by_groups',
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


	linkExtendWithGroup: function(extend, group, item, container)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'content_type/link_extend_with_group',
			{
				id_extend_field: extend.id_extend_field,
				id_content_type_group: group.id_content_type_group
			},
			{
				onSuccess: function()
				{
					self.getGroupAndExtendsList(item, container);
				}
			}
		);
	},


	unlinkExtendFromGroup: function(extend, group, item, container)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'content_type/unlink_extend_from_group',
			{
				id_extend_field: extend.id_extend_field,
				id_content_type_group: group.id_content_type_group
			},
			{
				onSuccess: function()
				{
					self.getGroupAndExtendsList(item, container);
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

			this.getGroupAndExtends(
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
										var _fields = [];

										if (group.fields.length > 0)
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

											self.buildParentGroupExtendFields(group, extend_fields, section);
										}
									});
								}
								else if (typeOf(tabsInstance) != 'null')
								{
									Array.each(groups, function(group)
									{
										if (group.fields.length > 0)
										{
											tabsInstance.addTab({
												label: group.name,
												onLoaded: function (tab, s)
												{
													var section = s;
													section.addClass('pt15');

													self.buildParentGroupExtendFields(group, extend_fields, section);
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

		self.extendManager.buildInstancesList(_fields, container);
	}

});
