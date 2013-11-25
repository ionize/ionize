/**
 * Ionize StepSwapper Class
 * @type {Class}
 *
 * requires: [Core/Class, Core/Object, Core/Element.Event]
 *
 * provides: [ION.StepSwapper]
 *
 * authors:
 * - Michel-Ange Kuntz
 *
 *
 * options {
 * 		stepsContainer:		    String. ID of the Container of the steps menu items
 * 		sectionsContainer:		String. ID of the Container of the steps content items
 * 		selectClass:	        String. CSS class to use for the current step item
 * 		steps:	                String. HTML tag of the steps menu items
 *		sections:	            String. Selector for the steps content elements
 * 		showStep: 	            Int. Starts at 1. Step to display at starting
 * }
 *
 */

ION.StepSwapper = new Class({

	Implements: [Options, Events],

	options: {
		stepsContainer: null,
		sectionsContainer: null,
		selectClass: 'active',
		steps: 'li',
		sections: 'div.step-content',
		showStep: 1,
		openClass: 'open'
	},

	initialize: function(options)
	{
		// Options
		this.setOptions(options);

		this.stepsContainer = $(this.options.stepsContainer);
		this.sectionsContainer = $(this.options.sectionsContainer);
		this.currentStep = this.options.showStep;
		this.initMenuStyle();
		this.setActive(this.options.showStep);
	},

	initMenuStyle: function()
	{
		var elements = this.stepsContainer.getElements('li');
		var nb = elements.length;
		elements.each(function(item){
			item.setStyle('width', Math.floor(98/nb) + '%');
		});
	},

	/**
	 * Makes one menu available to the user
	 *
	 * @param index
	 */
	initMenu: function(index)
	{
		var self = this;
		var menuItems = this.stepsContainer.getElements('li a');

		menuItems.each(function(item, idx)
		{
			item.removeEvents();
			item.removeClass(self.options.openClass);

			if (idx <= (index-1))
			{
				var toset = parseInt(idx + 1)
				item.addEvent('click', function(){
					self.click(toset);
				});
				item.addClass(self.options.openClass);
			}
		});
	},

	reset:function()
	{
		this.setActive(this.options.showStep);
	},

	addClickEvent: function(index, fn)
	{
		var menuItems = this.stepsContainer.getElements('li');

		if (typeOf(menuItems[index-1]) != 'null')
		{
			menuItems[index-1].addEvent('click', function(){
				fn();
			});
		}
	},

	click: function(index)
	{
		var self = this;
		var menuItems = this.stepsContainer.getElements('li');
		var contentItems = this.sectionsContainer.getElements(this.options.sections);

		// Only do it if the index exists
		if (typeOf(menuItems[index-1]) != 'null')
		{
			// Remove active class
			menuItems.each(function(item, idx){
				item.removeClass(self.options.selectClass);
			});

			// Remove active class
			contentItems.each(function(item, idx){
				item.hide();
			});

			menuItems[index-1].addClass(this.options.selectClass);
			contentItems[index-1].show();
		}
	},

	setActive: function(index)
	{
		this.click(index);

		this.currentStep = index;

		this.initMenu(this.currentStep);
	},

	isDisplayed: function(index)
	{
		var self = this;
		var menuItems = this.stepsContainer.getElements('li');

		// Remove active class
		if (typeOf(menuItems[index-1]) != 'null')
		{
			var menu = menuItems[index-1];
			if (menu.hasClass(self.options.selectClass))
				return true;
		}
		return false;
	},

	isActive: function(index)
	{
		return (index == this.currentStep);
	},

	isOpen: function(index)
	{
		var self = this;
		var menuItems = this.stepsContainer.getElements('li');

		if (typeOf(menuItems[index-1]) != 'null')
		{
			var menu = menuItems[index-1];
			return menu.hasClass(self.options.openClass);
		}
		return false;

	}
});
