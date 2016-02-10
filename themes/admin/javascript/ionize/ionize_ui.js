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
		var self = this;

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


ION.Ui.Panel = new Class({

	Implements: [Events, Options],

	el: {},

	options: {

		id:						null,			// id of the main div tag for the panel
		container:				null,			// Mandatory

		// header
		header:					true,			// true to create a panel header when panel is created
		title:					null,			// the title inserted into the panel's header

		'min-height':			0,				// the desired min-height of the panel
		height:					0,				// the desired height of the panel
		'class':				'',				// css class to add to the main panel div
		scrollbars:				true,			// true to allow scrollbars to be shown
		padding:				8,				// default padding for the panel

		// Other:
		collapsible:			true,			// can the panel be collapsed
		isCollapsed:			false,			// is the panel collapsed
		collapseFooter:			true			// collapse footer when panel is collapsed
	},

	initialize: function(options)
	{
		this.setOptions(options);

		// If panel has no ID, give it one.
		this.id = this.options.id = this.options.id || 'panel' + (++MUI.idCount);
		this.cookieName = this.id;

		if (typeOf(options.container) == 'null') return;

		this.container = typeOf(options.container) == 'string' ? $(options.container) : options.container;

		return this.draw();
	},

	draw: function()
	{
		var o = this.options;

		// Element
		this.el.element = new Element('div', {
			id: o.id,
			'class': 'dashboard-panel ' + o['class']
		}).inject(this.container).store('instance', this);

		// Block
		this.el.block = new Element('div', {
			'class':'block'
		}).inject(this.el.element);

		// Wrapper
		this.el.wrapper = new Element('div', {
			id: o.id + '_wrapper',
			'class': 'panelWrapper expanded'
		}).inject(this.el.block).setStyle('min-height', o['min-height']);

		// Content
		this.el.panel = new Element('div', {
			'id': o.id + '_content',
			'class': 'content-panel-content'
		}).inject(this.el.wrapper);

		// Pad : Receives the final content
		this.el.content = new Element('div', {
			'id': o.id + '_pad',
			'class': 'pad'
		}).inject(this.el.panel).setStyle('padding', o.padding);

		if (o.height > 0)
			this.el.content.setStyle('height', o.height);

		// Loading
		if (o.loading)
			this.el.wrapper.addClass('loading');

		// Create one header
		if (o.header)
		{
			var headerItems = [];

			this.el.header = new Element('div', {
				'id': o.id + '_header'
			}).inject(this.el.panel, 'before');

			if (o.collapsible){
				this._collapseToggleInit();
				headerItems.unshift({content:this.el.collapseToggle, divider:false});
			}

			if (o.title){
				this.el.title = new Element('h2', {
					'id': o.id + '_title',
					'html': o.title
				});
				headerItems.push({id:o.id + 'headerContent',content:this.el.title,orientation:'left', divider:false});
			}

			MUI.create({
				control: 'MUI.Dock',
				container: this.el.panel,
				element: this.el.header,
				id: o.id + '_header',
				cssClass: 'content-panel-header',
				docked:headerItems
			});
		}

		this._initCollapsed();

		return this;
	},

	setHeight: function(height)
	{
		if (o.height > 0)
			this.el.content.setStyle('height', height);
	},

	/**
	 * Returns the DOM Element in which the content can be put
	 * (by one external lib for example)
	 *
	 */
	getContainer: function()
	{
		return this.el.content;
	},

	adopt: function(content)
	{
		this.el.content.empty();

		if (typeOf(content) == 'string')
			this.el.content.set('html', content);
		else
			this.el.content.adopt(content);
	},

	isLoaded: function()
	{
		this.el.wrapper.removeClass('loading');
	},

	collapse: function()
	{
		this.el.panel.hide();
		this.isCollapsed = true;
		this.el.element.addClass('collapsed').removeClass('expanded');

		if (this.el.collapseToggle)
		{
			this.el.collapseToggle.removeClass('panel-collapsed')
					.addClass('panel-expand')
					.setProperty('title', 'Expand Panel');
		}

		this._saveCollapsed(true);

		return this;
	},

	expand: function()
	{
		this.el.panel.show();
		this.isCollapsed = false;

		this.el.element.addClass('expanded').removeClass('collapsed');

		if (this.el.collapseToggle)
		{
			this.el.collapseToggle.removeClass('panel-expand')
					.addClass('panel-collapsed')
					.setProperty('title', 'Collapse Panel');
		}

		this._saveCollapsed(false);

		return this;
	},

	toggle: function()
	{
		if (this.isCollapsed)
			this.expand();
		else
			this.collapse();
		return this;
	},

	_saveCollapsed: function(status)
	{
		Cookie.write(this.cookieName, status, {duration:this.options.cookieDays});
	},

	_initCollapsed: function()
	{
		var status = ([Cookie.read(this.cookieName), false].pick());

		if (typeOf(status) != 'null' && status == 'true')
			this.collapse();
		else
		{
			if (typeOf(status) == 'null' && this.options.isCollapsed)
				this.collapse();
			else
				this.expand();
		}
	},

	_collapseToggleInit: function(){
		this.el.collapseToggle = new Element('div', {
			'id': this.options.id + '_collapseToggle',
			'class': 'panel-collapse icon16',
			'styles': {
				'width': 16,
				'height': 16
			},
			'title': 'Collapse Panel'
		}).addEvent('click', function(){
			this.toggle();
		}.bind(this));
	}

});