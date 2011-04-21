/**
 * Gives ability to an input to have the "droppable" class and to change class on focus / blur
 *
 */
ION.Droppable = new Class({

	Implements: [Events, Options],
	
	options: ION.options,

	initialize: function(element, options)
	{
		this.setOptions(options);
		
		var options = this.options;
		
		/* Add focus in/out and blur events only if input has not the ".nofocus" class
		 *
		 */
		if (element.hasClass('nofocus') == false)
		{
			element.addEvents(
			{
				'change': function(e)
				{
					var alt = this.getProperty('alt');
					var value = this.getProperty('value');
					var text = this.get('text');
					
					if (value == '')
					{
						this.addClass('empty').set('text', alt).setProperty('value', alt);
					}
					else
					{
						this.removeClass('empty');
					}
				},
				
				'click': function(e)
				{
					var alt = this.getProperty('alt');
					var value = this.getProperty('value');
					
					if (value == alt)
					{
						this.removeClass('empty').set('text', '').setProperty('value', '');
					}
				},
			
				'blur': function(e)
				{
					this.fireEvent('change');
				}
			});

			element.fireEvent('change');	
		}
		else
		{
			if (element.hasClass('empty') == true)
			{
				var alt = element.getProperty('alt');
				element.set('text', alt).setProperty('value', alt);
			}

			element.addEvents(
			{
				'focus': function(e)
				{
					this.blur();
				}
			});
			
		}
	}	
	
});

ION.append({

	/**
	 * Init the droppables input and textareas
	 *
	 */
	initDroppable: function()
	{
		$$('.droppable').each(function(item, idx)
		{
			new ION.Droppable(item);
		});
	}
});
