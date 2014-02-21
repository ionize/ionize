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
