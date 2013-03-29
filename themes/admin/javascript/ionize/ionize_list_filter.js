/*
Class: ION.ListFilter
	Init a filter form for an existing list
	
Syntax:
	(start code)
	new ION.ListFilter(element, options);
	(end)	

Arguments:
	element - The list element (UL, OL)
	options

Options:
	form - Searchform object
	input - Search input object
	reset - Reset button
	(nodelist - Element which contains the whole nodes list)

*/
ION.ListFilter = new Class({

	Implements: [Events, Options],
	
	options: ION.options,
	
	initialize: function(list, options)
	{
		this.setOptions(options);

		this.list = list;
		
		// filter on these classes
		this.filterItem = this.options.items;
		
		// Searched term
		this.term = '';
		
		// Reset button
		this.reset = this.options.reset;
		this.input = this.options.input;
		
		// All tree Nodes
		this.nodes = this.list.getElements('li');
		
		// Remove events from form
		(this.options.form).addEvent('submit', function(e)
		{
			e.stop();
			e.stopPropagation();
		});
		
		// Reset button Event
		this.reset.addEvent('click', function()
		{
			this.input.value = '';
			this.nodes.removeClass('result');
			this.nodes.fade('show').show();
	
		}.bind(this));
		
		// Input KeyUp event
		this.input.addEvent('keyup', function(e)
		{
			if (typeOf(this.timeoutID) == 'number')
			{
				clearTimeout(this.timeoutID);
			}
			this.timeoutID = this.start.delay(400, this);
			
		}.bind(this));

	},
	

	start: function()
	{
		this.input.addClass('spinner');

		var search = this.input.value;
		
		if (search.length > 2 && search != this.term)
		{
			this.term = search;
		
			var filtered = (this.nodes).filter(function(item, idx)
			{
				var data = (item.getFirst(this.filterItem).get('html')).toLowerCase();
				return data.contains(search.toLowerCase());
			}.bind(this));

			
			// Visual fun
			this.nodes.removeClass('result');
			this.nodes.fade('show').show();
			filtered.addClass('result');
	
			// Add parent folder of filetered items. Purpose : Open them to show the notes
			var results = filtered;
			
			// Array of unique IDs of filtered notes (notes + class)
			var notes_ids = new Array();
			
			results.each(function(item)
			{
				var rel = item.getProperty('data-id');
	
				if (notes_ids.contains(rel) == false)
				{
					notes_ids.push(rel);
				}
			});
	
			// The magical : All folder wich aren't in the notes_ids array can be hidden;
			this.nodes.each(function(item)
			{
				if ( ! notes_ids.contains(item.getProperty('data-id')))
					item.fade('out').hide();
			});
			
		}
	
		this.input.removeClass('spinner');
	}

});	
