/**
 *
 * @type {Class}
 */
ION.Tabs = new Class({

	Implements: [Events, Options],

	options: {
		tabs: [
			/*
			{
				label: 'My First Tab',
				id: 'first-tab',
				'class': '',
				onClick: function(tab, section, tabManager){
					console.log('clicked on tab : ' + tab.id);
				}
			},
			{
				label: 'My Second Tab',
				id: 'second-tab',
				'class': ''
			}
			*/
		],
		initPanel: 0,
		selectedClass: 'selected',
		mouseoverClass: 'tabOver',
		deselectedClass: '',
		cookieName: null,
		cookieDays: 999
	},

	container: null,
	tabs: [],
	sections: [],
	clickers: [],

	initialize: function(options)
	{
		this.setOptions(options);

		if (options.container)
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		var prev = this.setup();
		if (prev) return prev;

		// Recall or Show
		var recallTab = this.recall();
		if (this.options.cookieName && recallTab !== false) recallTab.retrieve('clicker').click();
		else this.show(this.options.initPanel);

		return this;
	},

	setup: function()
	{
		if (this.container && this.container.retrieve('tabs'))
			return this.container.retrieve('tabs');

		var self = this,
			o = this.options;

		this.tabParent = new Element('div', {'class': 'mainTabs'});
		this.tabUl = new Element('ul', {'class':'tab-menu'}).inject(this.tabParent);
		this.tabSection = new Element('div');

		if (this.container)
		{
			this.tabParent.inject(this.container);
			this.tabSection.inject(this.container);
		}

		Object.each(o.tabs, function(item)
		{
			self.addTab(item, null);
		});
	},

	addTab: function(item, index)
	{
		var self = this;

		if (index == null) index = this.tabs.length;

		// Tab
		var tab = new Element('li');
		var a = new Element('a', {html: item.label}).inject(tab).store('tab', tab);

		// Section (tab content)
		var section = new Element('div');

		if (item.id) tab.setProperty('id', item.id);
		if (item.class) tab.setProperty('class', item.class);

		// If the index is set and there is already one tab at this index
		if (this.tabs[index])
		{
			tab.inject(this.tabs[index], 'before');
			section.inject(this.tabs[index].retrieve('section'), 'before');
		}
		else
		{
			tab.inject(this.tabUl);
			section.inject(this.tabSection);

			// Add section content
			if (typeOf(item.content) != 'null')
			{
				section.adopt(item.content);
			}

		}

		this.tabs.splice(index, 0, tab);
		this.sections.splice(index, 0, section);

		if ( ! item.disabled)
		{
			a.addEvent('click', function()
			{
				self.show(self.tabs.indexOf(this.retrieve('tab')));

				if (typeOf(item.onClick) == 'function')
				{
					item.onClick(this.retrieve('tab'), this.retrieve('tab').retrieve('section'), self);
				}
			});
		}

		tab.store('item', item);
		tab.store('section', section);
		tab.store('clicker', a);

		if (typeOf(item.onLoaded) == 'function')
			item.onLoaded(tab, section, this);

		this.hideSection(index);
	},

	show: function(i)
	{
		if (this.current == null)
		{
			this.tabs.each(function(tab, idx)
			{
				if (i != idx)
					this.hideSection(idx);
			}, this);
		}
		this.showSection(i).save(i);

		return this;
	},

	showSection: function(idx)
	{
		var tab = this.tabs[idx];
		if ( ! tab) return this;

		var sect = tab.retrieve('section');
		if (!sect) return this;

		if (this.current != tab)
		{
			var item = tab.retrieve('item');

			if (this.current != null) this.hideSection(this.tabs.indexOf(this.current));

			sect.setStyles({
				display:'block',
				overflow: 'hidden'
			});

			this.current = tab;
			this.fireEvent('onActive', [idx, sect, tab]);
		}

		tab.swapClass(this.options.deselectedClass, this.options.selectedClass);

		return this;
	},

	hideSection: function(idx)
	{
		var tab = this.tabs[idx];
		if (!tab) return this;

		var sect = tab.retrieve('section');
		if (!sect) return this;

		if (sect.getStyle('display') != 'none')
		{
			sect.setStyle('display', 'none');
			tab.swapClass(this.options.selectedClass, this.options.deselectedClass);
		}

		return this;
	},

	save: function(index)
	{
		if (this.options.cookieName)
			Cookie.write(this.options.cookieName, index, {duration:this.options.cookieDays});
		return this;
	},

	recall: function()
	{
		if (this.options.cookieName)
		{
			var idx = ([Cookie.read(this.options.cookieName), false].pick());

			if (typeOf(idx) != 'null' && this.tabs[idx])
				if( (this.tabs[idx]).hasAttribute('disabled') == false)
					return this.tabs[idx];
		}
		return false;
	}
});

