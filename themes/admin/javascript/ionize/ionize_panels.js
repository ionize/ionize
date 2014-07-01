ION.append({

	/**
	 *
	 * @param options    Options object
	 *
	 * @usage
	 *
	 * ION.splitPanel({
	 *      'urlMain': 'URL to the main panel content',
	 *      'urlOptions': 'Url to the option panel content',
	 *      'title': 'Title of the main panel',
	 *
	 * });
	 *
	 */
	splitPanel: function(options)
	{
		if ($('mainPanel'))
		{
			// Collapse / Expanded status from cookie
			var isCollapsed = false;
			var opened = Cookie.read('sidecolumn');

			if (typeOf(opened) != 'null' && opened == 'false')
				isCollapsed = true;

			MUI.Content.update({
				element: 'mainPanel',
				title: options.title,
				clear:true,
				loadMethod:'control',
				controls:[
					{
						control:'MUI.Column',
						container: 'mainPanel',
						id: 'splitPanel_mainColumn',
						placement: 'main',
						sortable: false,
						panels:[
							{
								control:'MUI.Panel',
								id: 'splitPanel_mainPanel',
								container: 'splitPanel_mainColumn',
								header: false,
								padding:0,

								content: {
									url: options.urlMain
									/*
									onLoaded: function(){
										//$('splitPanel_mainColumn').setStyle('width', 'inherit');
									}
									*/

								}
							}
						]
					},
					{
						control:'MUI.Column',
						container: 'mainPanel',
						id: 'splitPanel_sideColumn',
						placement: 'right',
						sortable: false,
						isCollapsed: isCollapsed,
						width: 330,
						resizeLimit: [330, 400],
						panels:[
							{
								control:'MUI.Panel',
								header: false,
								id: 'splitPanel_sidePanel',
								cssClass: 'panelAlt',
								padding:typeOf(options.paddingOptions != 'null') ? options.paddingOptions : 8,
								content: {
									url: options.urlOptions,
									onLoaded: function(){
										$('splitPanel_sidePanel').setStyle('width', 'inherit');
									}
								},
								container: 'splitPanel_sideColumn'
							}
						]
					}
				]
			});
		}
	}
});


/**
 * Dashboard
 *
 * @type {NamedClass}
 *
 */
ION.Dashboard = new Class({

	Implements: [Events, Options],

	container: null,

	panels: [],						// Ref. to each ION.DashboardPanel of this dashboard

	options: {
		id:	null,
		container:	null,			// Parent DOM container
		panels: []					// Panels Definitions
	},


	initialize: function(options)
	{
		this.setOptions(options);

		// If panel has no ID, give it one.
		this.id = this.options.id = this.options.id || 'dashboard' + (++MUI.idCount);
		this.cookieName = this.id;

		if (typeOf(options.container) != 'null')
			this.container = typeOf(options.container) == 'string' ? $(options.container) : options.container;

		this.draw();
	},


	draw: function()
	{
		var o = this.options,
			self = this
		;

		if ( ! o.container) return;

		this.el = new Element('div', {
			'class': 'dashboard clearfix',
			id: this.id
		}).inject(o.container).store('instance', this);

		Object.each(o.panels, function(panel)
		{
			panel['container'] = self.el;
			var p = new ION.DashboardPanel(panel);
			self.panels.push(p);
		});

		// Dashboard Resize Event
		var parent = this.el.getParent('.panel');

		if (parent)
		{
			parent = MUI.get(parent.id);
			parent.addEvent('resize', function()
			{
				var size = parent.el.panel.getSize();
				self.el.setStyle('min-height', size.y);
			});
			parent.fireEvent('resize');
		}

		return this;
	},

	getPanels: function()
	{
		return this.panels;
	},

	getPanel: function(name)
	{
		var panel = null;
		Object.each(this.panels, function(p){
			if (p.name == name)
				panel = p;
		});

		return panel;
	}
});



ION.DashboardPanel = new Class({

	Implements: [Events, Options],

	el: {},

	options: {
		id:						null,			// id of the main div tag for the panel
		container:				null,			// Container's ID
		dashboard:				null,

		// header
		header:					true,			// true to create a panel header when panel is created
		title:					null,			// the title inserted into the panel's header

		// Style options:
		type:					'',				// 'small', 'half', 'medium'

		height:					125,			// the desired height of the panel
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

		// Very important
		this.name = options.name;

		if (typeOf(options.container) != 'null')
			this.container = typeOf(options.container) == 'string' ? $(options.container) : options.container;

		// Container will be the dashboard
		if (options.dashboard && typeOf(options.dashboard) != 'null')
		{
			this.container = options.dashboard.el;
		}

		if ( ! this.container)
			return;

		this.draw();
	},


	draw: function()
	{
		var o = this.options;

		// Element
		this.el.element = new Element('div', {
			id: o.id,
			'class': 'dashboard-panel ' + o.type
		}).inject(this.container).store('instance', this);

		// Block
		this.el.block = new Element('div', {
			'class':'block'
		}).inject(this.el.element);

		// Wrapper
		this.el.wrapper = new Element('div', {
			id: o.id + '_wrapper',
			'class': 'panelWrapper expanded'
		}).inject(this.el.block).setStyle('min-height', o.height);

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

	/**
	 * Returns the DOM Element in which the content can be put
	 * (by one external lib for example)
	 *
	 */
	getContainer: function()
	{
		return this.el.content;
	},

	getName: function()
	{
		return this.name;
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

	toggle: function(){
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








/**
 * Dashboard Content Panels
 *
 * @type {NamedClass}
 *
 */
ION.ContentPanel = new NamedClass('ION.ContentPanel', {

	Implements: [Events, Options],

	options: {
		id:						null,			// id of the main div tag for the panel
		container:				null,			// the name of the column to insert this panel into

		// header
		header:					true,			// true to create a panel header when panel is created
		title:					false,			// the title inserted into the panel's header

		// footer
		footer:					false,			// true to create a panel footer when panel is created

		// Style options:
		height:					125,			// the desired height of the panel
		cssClass:				'',				// css class to add to the main panel div
		scrollbars:				true,			// true to allow scrollbars to be shown
		padding:				12,				// default padding for the panel

		// Other:
		collapsible:			true,			// can the panel be collapsed
		isCollapsed:			false,			// is the panel collapsed
		collapseFooter:			true			// collapse footer when panel is collapsed
	},

	initialize: function(options){
		this.setOptions(options);

		Object.append(this, {
			partner: null,
			el: {}
		});

		// If panel has no ID, give it one.
		this.id = this.options.id = this.options.id || 'panel' + (++MUI.idCount);
		this.cookieName = this.id;

		this.draw();
	},

	draw: function(container)
	{
		var options = this.options;

		if (!container) container = options.container;
		if (typeOf(container) == 'string') container = $(container);
		if (typeOf(container) != 'element') return;

		// Check if panel already exists
		if (this.el.panel) return this;

		var content = container.get('html');
		var size = container.getSize();
		container.empty().addClass('content-panel');

		// Wrapper
		var div = options.element ? options.element : $(options.id + '_wrapper');
		if (!div) div = new Element('div', {'id': options.id + '_wrapper'}).inject(container);
		div.empty().addClass('panelWrapper expanded');
		this.el.element = div;

		this.el.panel = new Element('div', {
			'id': options.id,
			'class': 'content-panel-content'
		}).inject(div)
			.addClass(options.cssClass)
			.store('instance', this);

		this.el.content = new Element('div', {
			'id': options.id + '_pad',
			'class': 'pad'
		}).inject(this.el.panel).set('html', content);

		var headerItems = [];
		var footerItems = [];

		if (options.header){
			this.el.header = new Element('div', {
				'id': options.id + '_header',
				'styles': { 'display': options.header ? 'block' : 'none' }
			}).inject(this.el.panel, 'before');

			if (options.collapsible){
				this._collapseToggleInit();
				headerItems.unshift({content:this.el.collapseToggle, divider:false});
			}

			if (options.title){
				this.el.title = new Element('h2', {
					'id': options.id + '_title',
					'html': options.title
				});
				headerItems.push({id:options.id + 'headerContent',content:this.el.title,orientation:'left', divider:false});
			}

			MUI.create({
				control: 'MUI.Dock',
				container: this.el.panel,
				element: this.el.header,
				id: options.id + '_header',
				cssClass: 'content-panel-header',
				docked:headerItems
			});
		}

		if (options.footer){
			this.el.footer = new Element('div', {
				'id': options.id + '_footer',
				'class': 'panel-footer',
				'styles': { 'display': options.footer ? 'block' : 'none' }
			}).inject(this.el.panel, 'after');

			MUI.create({
				control: 'MUI.Dock',
				container: this.el.element,
				id: options.id + '_footer',
				cssClass: 'panel-footer',
				docked: footerItems
			});
		}

		// Do this when creating and removing panels
		if (!container) return;
		container.getChildren('.panelWrapper').removeClass('bottomPanel').getLast().addClass('bottomPanel');

		Object.each(this.el, (function(ele){
			if (ele != this.el.headerToolbox) ele.store('instance', this);
		}).bind(this));

		this._initCollapsed();

		return this;
	},


	collapse: function()
	{
		if(this.el.footer) {
			if(this.options.collapseFooter) this.el.footer.hide();
		}

		this.el.panel.hide();
		this.isCollapsed = true;
		this.el.element.addClass('collapsed')
			.removeClass('expanded');

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
		if(this.el.footer && this.options.collapseFooter) this.el.footer.show();
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

	toggle: function(){
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


ION.MainPanel = new NamedClass('ION.MainPanel', {

	Implements: [Events, Options],

	options: {
		title: {
			text: 	null,
			icon: 	null
		},
		barTitle:	null,		// Panel's title
		icon:		'',
		styles:		{}			// To unset padding, use {padding: '0px'} as styles option
	},

	initialize: function(o)
	{
		// Get Main Panels
		this.mainPanel = $(ION.mainpanel);
		this.mainPanel.removeClass('bg-gray');
		this.mainColumn = new Element('div', {id:'maincolumn'}).inject(this.mainPanel);


		if (typeOf(o.title) != 'null')
		{
			var options = {
				container: this.mainColumn,
				title: typeOf(o.title.text) != 'null' ? o.title.text : ''
			};

			if (typeOf(o.title.icon) != 'null') options['class'] = o.title.icon;
			if (typeOf(o.subtitle) != 'null') options['subtitle'] = o.subtitle;

			// Window title
			this.mainTitle = new ION.WindowTitle(options);
		}
		else
			this.mainTitle = null;

		// Container
		this.container = new Element('div').inject(this.mainColumn);

		var bTitle = typeOf(o.barTitle) == 'null' ?
					((typeOf(o.title) != 'null' && typeOf(o.title.text) != 'null') ? o.title.text : '') : o.barTitle;

		// Init the main column into the main panel
		MUI.Content.update({
			'element': this.mainPanel,
			'content': this.mainColumn,
			'title': bTitle
		});

		// Panel class
		if (o['class']) this.mainPanel.addClass(o['class']);

		// Pad style
		if (o.styles) this.getPad().setStyles(o.styles);

		// Empty toolboxes
		this._initToolbox();

		if (typeOf(o.onLoad) == 'function')	o.onLoad(this);

		return this;
	},

	getContainer: function()
	{
		return this.container;
	},

	getTitle: function()
	{
		return this.mainTitle;
	},

	setTitle: function(title)
	{
		if (this.mainTitle != null)
			this.mainTitle.setTitle(title);
	},

	setSubtitle: function(subtitle)
	{
		if (this.mainTitle != null)
			this.mainTitle.setSubtitle(subtitle);
	},

	removeSubtitle: function()
	{
		if (this.mainTitle != null)
			this.mainTitle.removeSubtitle();
	},

	getPanel: function()
	{
		return this.mainPanel;
	},

	getPad: function()
	{
		return this.mainPanel.getFirst('.pad');
	},

	getColumn: function()
	{
		return this.mainColumn;
	},

	_initToolbox: function()
	{
		// Creates the header toolbox if it doesn't exists
		if ( ! $('mainPanel_headerToolbox')) {
			new Element('div', {
				'id': 'mainPanel_headerToolbox',
				'class': 'buttonbar'
			}).inject($('mainPanel_header'));
		}

		$('mainPanel_headerToolbox').empty();
	}
});