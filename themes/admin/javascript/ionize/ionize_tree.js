ION.SimpleTree = new Class({

	Implements: [Events, Options],

	options:
	{
		'key': 'id',
		'label': null,
		'data' : []				// field of each item to add as data-x attribute
	},

	/**
	 *
	 * @param container
	 * @param items     JSON object
	 * @param options      object
	 *
	 */
	initialize:function(container, items, options)
	{
		this.container = $(container);
		this.setOptions(options);

		this.items = items;
		this.tree = this.buildTree(this.items, 0, 0);

		this.container.adopt(this.tree);
	},

	buildTree:function(items, id_parent, level)
	{
		var key = this.options.key;
		var label = this.options.label;     // item key used as label

		// var mlClass = 'ml' + (parseInt(level) * 20);
		var mlClass = 'ml16';

		var ul = new Element('ul', {'class':'tree', 'data-id':id_parent});
		var self = this;

		Array.each(items, function(item)
		{
			var element = new Element('li',{'class':'f-open','data-id':item[key]});

			// Left margin regarding parent
			if (level > 0)
				element.addClass(mlClass);

			// Title
			if (this.options.label)
				new Element('a', {'class':'title', text:item[label]}).inject(item);

			// Add data-x attributes
			Array.each(this.options.data, function(dataKey){
				element.setAttribute('data-'+dataKey.as, item[dataKey.key]);
			});

			ul.adopt(element);

			if (item.children)
				element.adopt(this.buildTree(item.children, item[key], level + 1));

			// plus / minus icon
			var pm = new Element('div', {'class': 'tree-img plus'})
				.addEvent('click', self.openclose.bind(self))
				.inject(element, 'top');

			self.close(element);

		}.bind(this));

		return ul;
	},

	openclose: function(e)
	{
		if (typeOf(e.stop) == 'function') e.stop();
		var el = e.target;

		var li = el.getParent('li');

		// Is the folder Open ? Yes ? Close it (Hide the content)
		if (li.hasClass('f-open'))
		{
			this.close(li);
		}
		else
		{
			this.open(li);
		}
	},

	open: function(element)
	{
		if ( ! element.hasClass('f-open'))
		{
			// All childrens UL
			var elementContents = element.getChildren('ul');

			var pmIcon = element.getFirst('div.tree-img.plus');
			if (typeOf(pmIcon) !=  'null')
				pmIcon.addClass('minus').removeClass('plus');

			elementContents.each(function(ul){ ul.setStyle('display', 'block'); });
			element.addClass('f-open');
		}
	},

	close: function(element)
	{
		if (element.hasClass('f-open'))
		{
			// All childrens UL
			var elementContents = element.getChildren('ul');

			var pmIcon = element.getFirst('div.tree-img.minus');
			if (pmIcon)
				pmIcon.addClass('plus').removeClass('minus');

			elementContents.each(function(ul){ ul.setStyle('display', 'none');});
			element.removeClass('f-open');
		}
	}
});




ION.PermissionTree = new Class({

	Extends: ION.SimpleTree,

	options:
	{
		cb_name: 'rules[]',
		onCheck: null
	},

	initialize:function(container, items, options)
	{
		// console.log(options);

		this.parent(container, items, options);
		this.rules = this.get_rules_array();

		if (typeOf(options.onCheck) != 'null')
			this.options.onCheck = options.onCheck;

		this.enhanceTree();
	},

	enhanceTree:function()
	{
		var lis = this.tree.getElements('li');

		lis.each(function(li)
		{
			this.create_resource_li(li);

			var actions = li.getAttribute('data-actions');

			if (actions != '')
				this.create_actions_li(li, actions);

		}.bind(this));

		// Replace +/- by space if no children
		lis = this.tree.getElements('li');
		lis.each(function(li)
		{
			if (li.getChildren('ul').length == 0)
			{
				var pm = li.getElement('div.plus');
				if (pm)
				{
					new Element('div', {'class':'tree-img line node'}).inject(pm, 'before');
					pm.destroy();
				}
			}
			// Get LI children to add "partial rights" if some aren't checked
			var cb = li.getElement('input[type=checkbox]');
			if (cb.getProperty('checked') == true)
			{
				var cbs = li.getElements('input[type=checkbox]');
				var partial = false;
				cbs.each(function(cb)
				{
					if (partial == false)
					{
						if (cb.getProperty('checked') != true)
						{
							var a = li.getElement('label a');
							new Element('span', {'class':'lite'}).set('text', ' (' + Lang.get('ionize_label_partial_permission') + ')').inject(a, 'bottom');
							partial = true;
						}
					}
				});
			}

		});
	},

	create_resource_li:function(container)
	{
		var id = container.getAttribute('data-id');

		var a = new Element('a', {'text': container.getAttribute('data-title'), 'title':'action:access, resource:' + container.getAttribute('data-resource')});

		var label = new Element('label', {
			'for': this.options.cb_name + id
		}).adopt(a).inject(container, 'top');

		// Because label is injected on top, the +/- icon should be injected (moved) at top again.
		var pmIcon = container.getElement('.tree-img');
		if (pmIcon) pmIcon.inject(container, 'top');

		this.inject_checkbox(label, container.getAttribute('data-resource'));
	},

	inject_checkbox:function(container, value)
	{
		var cb = new Element('input', {
			'type':'checkbox',
			'name': this.options.cb_name,
			'value': value,
			'id': container.getAttribute('for'),
			'class':'mr5'
		});
		cb.inject(container, 'top');

		// Set checked
		if (this.rules.contains(value))
			cb.setAttribute('checked', true);

		// Event
		this.set_checkbox_event(cb);
	},

	create_actions_li:function(container, actions)
	{
		actions = actions.split(',');
		var id = container.getAttribute('data-id');
		var ul = new Element('ul', {'class':'tree', 'data-id':id, 'style':'display:none;'});

		actions.each(function(action)
		{
			var li = new Element('li',{'data-id':this.options.cb_name + '-' + action + id, 'class':'ml16'});
			var spIcon = new Element('div', {'class':'tree-img line node'});
			action = String.from(action).trim();
			var a = new Element('a', {'text': action.replace('_', ' ').capitalize(), 'title':'action:' + action + ', resource:' + container.getAttribute('data-resource')});

			var label = new Element('label', {
				'for':this.options.cb_name + '-' + action + id
			}).adopt(a).inject(li, 'top');

			spIcon.inject(li, 'top');

			li.inject(ul);
			this.inject_checkbox(label, container.getAttribute('data-resource') + ':' + action);

		}.bind(this));

		ul.inject(container);

	},

	set_checkbox_event:function(cb)
	{
		var self = this;
		cb.addEvent('change', function(evt)
		{
			var li = evt.target.getParent('li');
			var childLis = li.getElements('li');
			var checked = evt.target.getProperty('checked');

			childLis.each(function(item){
				item.getElement('input').setProperty('checked', checked);
			});
			if (typeOf(self.options.onCheck) == 'function')
				self.options.onCheck(self);
		});
	},

	get_rules_array:function()
	{
		var data = new Array();

		if (this.options.rules)
		{
			this.options.rules.each(function(rule)
			{
				data.push(rule.resource);
				if (rule.actions != '')
				{
					var actions = rule.actions.split(',');
					actions.each(function(action){
						data.push(rule.resource + ':' + action);
					});
				}
			});
		}
		return data;
	}
});


