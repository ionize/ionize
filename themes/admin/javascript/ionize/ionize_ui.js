/**
 * Manages a set of UiElement.
 * For example, one tab serie with 3 tabs
 *
 * @type {Class}
 */
ION.Ui = new Class({

	Implements: [Events, Options],

	initialize: function(options)
	{
		this.setOptions(options);
	},


	getPanelElements: function(panel, type, options)
	{
		ION.JSON(
			ION.adminUrl + 'ui/get_panel_elements',
			{
				panel: panel,
				type: type
			},
			{
				onSuccess:function(json)
				{
					if (typeOf(options.onSuccess) == 'function')
					{
						options.onSuccess(json)
					}
				}
			}
		);
	},


	addElement: function(type, title, ordering, panel)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'ui/add_element',
			{
				type: type,
				title: title,
				ordering: ordering,
				panel: panel
			},
			{
				// Gets back the created element
				onSuccess: function(json)
				{
					self.fireEvent('onElementAdd', [this, json]);
				}
			}
		);
	}
});




/**
 * UI Element
 * Each instance of UI Element manages one element
 * Eg. One Tab serie with 3 tabs will be managed by 3 instances
 *
 * @type {Class}
 */
ION.UiElement = new Class({

	Implements: [Events, Options],

	container: null,

	id_ui_element: null,

	options: {
		post:{},								// Data posted with each request
		getManageList: false					// If set to true, fills up the container with the list of fields, in "manage" mode

		/*
			id_ui_element: 	int					// Element ID, srt at initialization
			container: 		HTML DOM Element	// Container

		*/
	},


	initialize: function(options)
	{
		var self = this;

		this.setOptions(options);

		if (options.container)
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		this.id_ui_element = options.id_ui_element;

		self.options.post = Object.merge(self.options.post, {id_ui_element:this.id_ui_element});

		// Management mode
		if (this.options.getManageList == true)
		{
			// Container : List of fields
			var p = new Element('p', {'class':'lite p10', html: Lang.get('ionize_help_drop_fields_here')}).inject(this.container);

			this.fieldContainer = new Element('ul', {'class':'droppable dropExtend w500 mb20'}).inject(this.container);

			this.fieldContainer.addEvent('onDrop', function(element)
			{
				var extend = element.retrieve('data');
				self.linkFieldToElement(extend.id_extend_field);
			});

			this.getManageFieldList();
		}
	},

	getFieldList: function(options)
	{
		ION.JSON(
			ION.adminUrl + 'ui/get_element_fields',
			{id_ui_element: this.id_ui_element},
			{
				onSuccess: function(json)
				{
					if (typeOf(options.onSuccess) == 'function')
						options.onSuccess(json);
				}
			}
		);
	},


	getManageFieldList: function()
	{
		var self = this

		ION.JSON(
			ION.adminUrl + 'ui/get_element_fields',
			{id_ui_element: self.id_ui_element},
			{
				onSuccess: function(json)
				{
					self.fieldContainer.empty();

					new ION.List({
						container: self.fieldContainer,
						items: json,
						buildUl: false,
						sortable: true,
						post: self.options.post,
						sort: {
							handler: '.drag',
							id_key: 'id_extend_field',
							url: ION.adminUrl + 'ui/save_element_fields_ordering'
						},
						elements:[
							// Sort
							{
								element: 'span',
								'class': 'icon drag left'
							},
							// Title
							{
								element: 'span',
								'class': 'unselectable',
								text: 'label'
							},
							// Unlink
							{
								element: 'a',
								'class': 'icon unlink right',
								onClick: function(extend)
								{
									self.unlinkFieldFromElement(extend.id_extend_field);
								}
							},
							// Type name
							{
								element: 'span',
								'class': 'right lite type_name mr15',	// store
								text: 'type_name'
							}
						]
					});
				}
			}
		);
	},


	unlinkFieldFromElement: function(id_field)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'ui/unlink_field_from_element',
			{
				id_extend: id_field,
				id_ui_element: this.id_ui_element
			},
			{
				// Refresh the list
				onSuccess: function()
				{
					self.getManageFieldList();
				}
			}
		);

	},


	linkFieldToElement: function(id_field)
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'ui/link_field_to_element',
			{
				id_extend: id_field,
				id_ui_element: this.id_ui_element
			},
			{
				// Refresh the list
				onSuccess: function()
				{
					self.getManageFieldList();
				}
			}
		);
	},


	update: function(field, value)
	{
		var data = {id_ui_element: this.id_ui_element};

		data[field] = value;

		ION.JSON(ION.adminUrl + 'ui/update_element', data, {});
	},


	delete: function()
	{
		var self = this;

		ION.JSON(
			ION.adminUrl + 'ui/delete_element',
			{id_ui_element: this.id_ui_element},
			{
				// Remove from UI when removed from server
				// ... Job of the parent lib
				onSuccess: function()
				{
					self.fireEvent('onDelete');
				}
			}
		);
	}
});