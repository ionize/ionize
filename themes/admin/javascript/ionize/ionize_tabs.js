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
				},
				onLoaded: function(tab, section, tabManager){
			 		console.log('tab loaded : ' + tab.id);
				},
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
		cookieDays: 999,
		'class': null,

		// Editable mode options
		editable: false,
		post: null,									// Data to be posted with each request
		sort: {
			handler:null,        					// Class of the icon used to sort elements by drag'n'drop
			id_key: null,							// Key to use as ID for each element.
			url:	ION.adminUrl + 'ui/save_order'	// URL of the sorting controller. Must be set to activate the sorting
		}
	},

	container: null,
	tabs: [],
	sections: [],
	clickers: [],

	initialize: function(options)
	{
		var self = this;

		this.setOptions(options);

		if (options.container)
			this.container = (typeOf(options.container) == 'string') ? $(options.container) : options.container;

		var prev = this.init();
		if (prev) return prev;

		// Recall or Show
		var recallTab = this.recall();

		if (this.options.cookieName && recallTab !== false) recallTab.retrieve('clicker').click();
		else this.show(this.options.initPanel);

		if (options.editable)
		{
			ION.loadAsset(
				ION.themeUrl + 'javascript/ionize/ionize_ui.js',
				{
					onComplete: function()
					{
						self.makeEditable();
					}
				}
			);
		}

		return this;
	},

	init: function()
	{
		if (this.container && this.container.retrieve('tabs'))
			return this.container.retrieve('tabs');

		var self = this,
			o = this.options;

		this.tabParent = new Element('div', {'class': 'mainTabs'});
		this.tabUl = new Element('ul', {'class':'tab-menu'}).inject(this.tabParent);
		this.tabSection = new Element('div');

		// Additional CSS classes
		if (this.options['class'] != null) this.tabParent.addClass(this.options['class']);

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

		return tab;
	},

	hasTab: function(key, value)
	{
		var has = false;

		this.tabs.each(function(tab)
		{
			var item = tab.retrieve('item');

			if (typeOf(item[key]) != 'null' && item[key] == value)
				has = true;
		});

		return has;
	},

	getTab: function(key, value)
	{
		var result = null;

		this.tabs.each(function(tab)
		{
			var item = tab.retrieve('item');

			if (typeOf(item[key]) != 'null' && item[key] == value)
				result = tab;
		});

		return result;
	},

	getSection: function(key, value)
	{
		var result = null;

		this.tabs.each(function(tab)
		{
			var item = tab.retrieve('item');

			if (typeOf(item[key]) != 'null' && item[key] == value)
				result = tab.retrieve('section');
		});

		return result;
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
	},

	removeTab: function(idx)
	{
		var tab = this.tabs[idx];
		if ( ! tab) return;
		var sect = tab.retrieve('section');
		sect.destroy();
		tab.destroy();
		delete(this.tabs[idx]);
		delete(this.sections[idx]);
		this.show(0);
	},

	makeEditable: function()
	{
		var self = this;

		this.addTabAddButton();

		// Make title editable
		Object.each(this.tabs, function(tab)
		{
			self.makeTabEditable(tab);
		});

		this._setSortable();
	},


	makeTabEditable: function(tab)
	{
		var self = this,
			a = tab.getElement('a'),
			title = a.get('text') != '' ? a.get('text') : 'New tab',
			handle = new Element('span', {'class':'icon drag horiz left mr5'}),
			delIcon = new Element('span', {'class':'icon tab delete horiz right mr-5'}),
			span = new Element('span', {'class':'text left','text': title}),
			section = tab.retrieve('section'),
			item = tab.retrieve('item')
		;

		// unique Id for Sortables
		tab.setProperty('id', ION.generateHash());

		// Title & Drag handle
		tab.store('title', title);
		a.empty().adopt(span).adopt(delIcon);

		// Drag Handle
		handle.inject(a, 'top');

		// Editable
		span.setProperty('contenteditable', true);

		// Tabs Events
		a.addEvent('click', function(ev){ ev.stopPropagation(); });
		span.addEvent('blur', function(){ self.onTitleChange(tab); });
		delIcon.addEvent('click', function(){ self.deleteTab(tab); });

		// Each tab is one UI Element
		var uiElement = new ION.UiElement({
			container: section,
			id_ui_element: item.id_ui_element,
			getManageList: true,
			onDelete: function(){
				self.sortables.removeItems(tab);
				self.removeTab(self.tabs.indexOf(tab));
			}
		});
		tab.store('uiElement', uiElement);
	},


	onTitleChange: function(tab)
	{
		if (typeOf(tab.getProperty('id')) != 'null')
		{
			var text = tab.retrieve('clicker').getElement('span.text').get('text');

			if (tab.retrieve('title') != text)
			{
				tab.store('title', text);

				var uiElement = tab.retrieve('uiElement');

				uiElement.update('title', text);
			}
		}
	},

	createNewTab: function()
	{
		var self = this,
			title = 'New tab'
		;

		var pos = self.tabs.length;

		if (typeOf(this.options.post.panel) != 'null')
		{
			var ui = new ION.Ui({
				onElementAdd: function(uiObj, json)
				{
					var tab = self.addTab({label: title}, pos);
					tab.store('item', json);

					self.makeTabEditable(tab);
					self.sortables.addItems(tab);

					tab.setProperty('id', ION.generateHash());

					self.tabAdd.inject(self.tabUl, 'bottom');
				}
			});

			ui.addElement('tab', title, pos, this.options.post.panel);
		}
		else
		{
			console.log('ION.Tabs.createNewTab() ERROR : No panel set in ION.Tabs options.');
		}
	},

	addTabAddButton: function()
	{
		var self = this,
			tabAdd = new Element('li', {'class':'setup'}).inject(this.tabUl),
			a = new Element('a').inject(tabAdd),
			span = new Element('span').inject(a);

		this.tabAdd = tabAdd;

		a.addEvent('click', function()
		{
			self.createNewTab();
			tabAdd.inject(self.tabUl, 'bottom');
		});
	},

	deleteTab: function(tab)
	{
		ION.confirmation(
			'confirmTabDelete' + tab.id,
			function()
			{
				tab.retrieve('uiElement').delete();
			},
			Lang.get('ionize_message_confirm_tab_delete')
		);
	},

	_setSortable: function()
	{
		var self = this;

		// Sortable
		this.sortables = new Sortables(this.tabUl,
		{
			revert: true,
			handle: '.drag',
			clone: true,
			constrain: false,
			snap:10,
			stopPropagation:true,
			opacity: 0.5,
			onStart:function(el, clone)
			{
				clone.addClass('clone');
			},
			onComplete: function(item, clone)
			{
				// Hides the current sorted element (correct a Mocha bug on hiding modal window)
				item.removeProperty('style');

				// Get the new order
				var serialized = this.serialize(0, function(item)
				{
					// Check for the not removed clone
					if (item.id != '')
					{
						var data = item.retrieve('item');
						if (self.options.sort.id_key != null && data[self.options.sort.id_key])
							return data[self.options.sort.id_key]
					}
					return;
				});

				// Items sorting
				self._sortItems(serialized);
			}
		});
	},

	_sortItems: function(serialized)
	{
		var serie = [];

		serialized.each(function(item) {
			if (typeOf(item) != 'null')	serie.push(item);
		});

		var data = {order:serie};
		Object.append(data, this.options.post);

		// Send the new order to the controller
		if (this.options.sort.url)
		{
			ION.JSON(
				ION.adminUrl + 'ui/save_ordering',
				data,
				{}
			);
		}
		else
		{
			alert('New order, but no sort.url set : ' + serie);
		}
	}
});

