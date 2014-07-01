/*
---
name: TabSwapper.js
authors: Aaron Newton
description: Handles the scripting for a common UI layout; the tabbed box.
license: MIT-Style License
requires:
 core:1.2.4: [Element.Event, Fx.Tween, Fx.Morph]
 more:1.2.4.2: [Element.Shortcuts, Element.Dimensions, Element.Measure]
provides: [TabSwapper]
...
*/

var TabSwapper = new Class({
	Implements: [Options, Events],
	options: {
   		tabsContainer: null,					// Added by Partikule
   		sectionsContainer: null,					// Added by Partikule
		selectedClass: 'tabSelected',
		mouseoverClass: 'tabOver',
		deselectedClass: '',
		rearrangeDOM: true,
		initPanel: 0, 
		smooth: false, 
		smoothSize: false,
		maxSize: null,
		effectOptions: {
			duration: 500
		},
		cookieName: null, 
		cookieDays: 999
//	onActive: $empty,
//	onActiveAfterFx: $empty,
//	onBackground: $empty
	},
	tabs: [],
	sections: [],
	clickers: [],
	sectionFx: [],

	initialize: function(options)
	{
		this.setOptions(options);
		var prev = this.setup();
		if (prev) return prev;
		if (this.options.cookieName && this.recall()) this.show(this.recall().toInt());
		else this.show(this.options.initPanel);
	},

	setup: function()
	{
		var opt = this.options;
		sections = $$('#' + opt.sectionsContainer + ' '  + opt.sections);
		tabs = $$('#' + opt.tabsContainer + ' ' + opt.tabs);
		if (tabs[0] && tabs[0].retrieve('tabSwapper')) return tabs[0].retrieve('tabSwapper');
		clickers = $$('#' + opt.tabsContainer + ' ' + opt.clickers);
		tabs.each(function(tab, index){
			if (sections[index])
				this.addTab(tab, sections[index], clickers[index], index);
		}, this);
		// Store the instance
		if ($(opt.tabsContainer)) $(opt.tabsContainer).store('tabSwapper', this);
	},

	addTab: function(tab, section, clicker, index)
	{
		tab = document.id(tab); clicker = document.id(clicker); section = document.id(section);
		//if the tab is already in the interface, just move it
		if (this.tabs.indexOf(tab) >= 0 && tab.retrieve('tabbered') 
			 && this.tabs.indexOf(tab) != index && this.options.rearrangeDOM) {
			this.moveTab(this.tabs.indexOf(tab), index);
			return this;
		}
		//if the index isn't specified, put the tab at the end
		if (index == null) index = this.tabs.length;
		//if this isn't the first item, and there's a tab
		//already in the interface at the index 1 less than this
		//insert this after that one
		if (index > 0 && this.tabs[index-1] && this.options.rearrangeDOM) {
			tab.inject(this.tabs[index-1], 'after');
			section.inject(this.tabs[index-1].retrieve('section'), 'after');
		}
		this.tabs.splice(index, 0, tab);
		clicker = clicker || tab;

		tab.addEvents({
			mouseout: function(){
				tab.removeClass(this.options.mouseoverClass);
			}.bind(this),
			mouseover: function(){
				tab.addClass(this.options.mouseoverClass);
			}.bind(this)
		});

		if ( ! tab.hasAttribute('disabled'))
		{
			clicker.addEvent('click', function(e){
				e.preventDefault();
				this.show(index);
			}.bind(this));
		}

		tab.store('tabbered', true);
		tab.store('section', section);
		tab.store('clicker', clicker);
		this.hideSection(index);
		return this;
	},

	addNewTab: function(title, id)
	{
		var options = arguments[2];

		var index = this.tabs.length;

		// Tab
		var a = new Element('a').set('html', title);
		var li = new Element('li', {'id': 'tab' + id, 'class': id}).adopt(a);
		li.inject($(this.options.tabsContainer), 'bottom');

		if (options)
		{
			Object.each(options, function(value, key)
			{
				if (key == 'class')
					li.addClass(value);
				else
					li.setProperty(key, value);
			});
		}

		// Section
		var div = new Element('div', { 'class': 'tabcontent ' + id}).inject(this.options.sectionsContainer, 'bottom');

		this.addTab(li, div, a, index);

		return div;
	},

	getTabs: function()
	{
		return this.tabs;
	},

	getSection: function(selector)
	{
		var section = $(this.options.sectionsContainer).getElement(selector);
		return section;
	},

	removeTabById: function(id)
	{
		var self = this;
		id = 'tab' + id;

		this.tabs.each(function(tab, index)
		{
			if (tab.id == id)
			{
				var section = tab.retrieve('section');
				self.tabs.erase(tab);
				tab.destroy();
				section.destroy();

				if (self.now == index)
				{
					if (index > 0) self.show(index - 1);
					else if (index < self.tabs.length) self.show(index + 1);
				}
			}
		});
	},

	removeTab: function(index)
	{
		var now = this.tabs[this.now];
		if (this.now == index){
			if (index > 0) this.show(index - 1);
			else if (index < this.tabs.length) this.show(index + 1);
		}
		this.now = this.tabs.indexOf(now);
		return this;
	},

	moveTab: function(from, to)
	{
		var tab = this.tabs[from];
		var clicker = tab.retrieve('clicker');
		var section = tab.retrieve('section');
		
		var toTab = this.tabs[to];
		var toClicker = toTab.retrieve('clicker');
		var toSection = toTab.retrieve('section');
		
		this.tabs.erase(tab).splice(to, 0, tab);

		tab.inject(toTab, 'before');
		clicker.inject(toClicker, 'before');
		section.inject(toSection, 'before');
		return this;
	},

	hasTabId: function(id)
	{
		var has = false;
		id = 'tab' + id;

		this.tabs.each(function(tab, idx){
			if (tab.id == id)
				has = true;
		});

		return has;
	},

	setTabInfo:function(selector, text)
	{
		var tab = $(this.options.tabsContainer).getElement(selector);
		if (tab)
		{
			var a = tab.getElement('a');
			var span = a.getElement('span.tab-detail');
			if ( ! span)
				span = new Element('span', {'class':'tab-detail'}).inject(a, 'bottom');
			span.set('text', text);
			a.addClass('detail');
		}
	},

	show: function(i)
	{
		if (this.now == null) {
			this.tabs.each(function(tab, idx){
				if (i != idx) 
					this.hideSection(idx);
			}, this);
		}
		this.showSection(i).save(i);
		return this;
	},

	getCurrentTab:function()
	{
		var tab = null;
		var i = 0;
		if (this.now != null) {
			i = this.now;
		}
		else {
			i = this.recall();
		}
		this.tabs.each(function(t, idx){
			if (i == idx)
			{
				tab = t;
			}
		}, this);

		return tab;
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
					return idx;
		}
		return false;
	},

	clickRecall: function()
	{
		idx = this.recall();
		if (idx)
		{
			var tab = this.tabs[idx];
			tab.fireEvent('click');
			return true;
		}
		return false;
	},

	hideSection: function(idx)
	{
		var tab = this.tabs[idx];
		if (!tab) return this;
		var sect = tab.retrieve('section');
		if (!sect) return this;

		if (sect.getStyle('display') != 'none') {
			this.lastHeight = sect.getSize().y;
			sect.setStyle('display', 'none');
			tab.swapClass(this.options.selectedClass, this.options.deselectedClass);
			this.fireEvent('onBackground', [idx, sect, tab]);
		}

/*
		if (sect.getStyle('position') == 'static' || sect.getStyle('position') == 'relative') {
			this.lastHeight = sect.getSize().y;
			sect.setStyles({
				'position': 'absolute',
				'z-index': 10,
				'left': '-9999px',
				'top' : '0px'
			});
			tab.swapClass(this.options.selectedClass, this.options.deselectedClass);
//			this.fireEvent('onBackground', [idx, sect, tab]);
			
		}
*/		
		return this;
	},

	showSection: function(idx)
	{
		var tab = this.tabs[idx];
		if (!tab) return this;
		var sect = tab.retrieve('section');
		if (!sect) return this;
		var smoothOk = this.options.smooth && !Browser.Engine.trident4;
		if (this.now != idx) {
			if (!tab.retrieve('tabFx')) 
				tab.store('tabFx', new Fx.Morph(sect, this.options.effectOptions));
			var overflow = sect.getStyle('overflow');
/*
			var start = {
				'position': 'static'
			};
*/
			var start = {
				display:'block',
				overflow: 'hidden'
			};
			if (smoothOk) start.opacity = 0;
			var effect = false;
			if (smoothOk) {
				effect = {opacity: 1};
			} else if (sect.getStyle('opacity').toInt() < 1) {
				sect.setStyle('opacity', 1);
				if (!this.options.smoothSize) this.fireEvent('onActiveAfterFx', [idx, sect, tab]);
			}
			if (this.options.smoothSize) {
				var size = sect.getDimensions().height;
				if ($chk(this.options.maxSize) && this.options.maxSize < size) 
					size = this.options.maxSize;
				if (!effect) effect = {};
				effect.height = size;
			}
			if (this.now != null) this.hideSection(this.now);
			if (this.options.smoothSize && this.lastHeight) start.height = this.lastHeight;
			sect.setStyles(start);
			if (effect) {
				tab.retrieve('tabFx').start(effect).chain(function(){
					this.fireEvent('onActiveAfterFx', [idx, sect, tab]);
					sect.setStyles({
						height: this.options.maxSize == effect.height ? this.options.maxSize : "auto",
						overflow: overflow
					});
					sect.getElements('input, textarea').setStyle('opacity', 1);
				}.bind(this));
			}
			this.now = idx;
			this.fireEvent('onActive', [idx, sect, tab]);
		}
		tab.swapClass(this.options.deselectedClass, this.options.selectedClass);
		return this;
	}
});
