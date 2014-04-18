/**
 * AjaxForm class
 *
 * @param formId		String. ID of the form
 * @constructor
 *
 */

var AjaxForm = Class.create(
{
	// By default the form is not set
	form: null,

	init: function(options)
	{
		if ($.type(options.name) != 'undefined')
		{
			this.name = options.name;
			this.form = $('form[name="' + options.name + '"]');
		}
		else
		{
			console.log('AjaxForm ERROR : Set the name attribute of your form. Eg : <form name="contact" >');
		}
	},

	validate: function()
	{
		console.log('validate');

/*
		$.post(this.checkURL,
			data,
			function(r){

				var callbacks = null;

				try
				{
					var callbacks = $.parseJSON(r);
				}
				catch(err)
				{
					//console.log(data);
				}

				if(callbacks != null)
				{
					$.each(callbacks, function(i, obj)
					{
						callFunctionByName(obj.func, obj.args);
					});
				}
			}
		);
*/

	}
});
