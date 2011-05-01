
// couple of development support functions:
//
// (c) Copyright 2011, Ger Hobbelt ( http://hebbut.net/ )

if (typeof window.debug === 'undefined')
{
	window.debug = {};
}

/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL (default: 0)
 *    max_depth - OPTIONAL (default: 1) maximum recursive dump level; how deep to dig into the object/array
 *    max_lines - OPTIONAL (default: 50) maximum # of lines of text to produce as 'dump' text (handy, for example, when you feed it to alert())
 *    no_show   - OPTIONAL (default: null) which types of info NOT to show (accepts a comma-separated set of types, 'string:empty' is a special one!)
 * Returns  : The textual representation of the array/object.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 * Patched by Ger Hobbelt (max_depth, max_lines, limited_text(), function dump v.s. array/object dump)
 */
debug.dump = function(arr, level, max_depth, max_lines, no_show)
{
	var limited_text = function(text, limit)
	{
		var data = ''+text; // convert anything to string
		if (!limit || limit < 0) limit = 128;
		var datalen = data.length;
		if (datalen > limit)
		{
			data = data.substring(0, limit - 1) + " (more...)";
		}
		return data;
	}
	var dumped_text = "";
	if (!level) level = 0;
	if (!max_depth) max_depth = 1;
	if (!max_lines || max_lines < 0) max_lines = 50;
	if (!no_show) no_show = '';

	//The padding given at the beginning of the line.
	var level_padding = (level == 0 ? "" : "\n");
	for(var j = 1; j < level + 1; j++)
		level_padding += "    ";

	try
	{
		switch (typeof(arr))
		{
		case 'object':
			if (level >= max_depth)
			{
				dumped_text = "("+typeof(arr)+") ("+limited_text(arr, 60)+")\n";
			}
			else
			{
				//Array/Hashes/Objects
				var shown_any = false;

				dumped_text = level_padding + "("+typeof(arr)+") => [\n";
				for(var item in arr)
				{
					var value = arr[item];
					var show = ((','+no_show+',').indexOf(','+typeof(value)+',') < 0);
					if (!show) continue;
					var show_nonempty = ((','+no_show+',').indexOf(','+typeof(value)+':empty,') >= 0);
					if (show_nonempty)
					{
						//alert('nonempty! '+item+", "+limited_text(value, 60));
						switch (typeof(value))
						{
						case 'string':
							show = (value.length > 0);
							break;

						case 'undefined':
							show = false;
							break;

						case 'number':
							show = (value != 0);
							break;

						case 'boolean':
							show = value;
							break;
						}
						if (!show) continue;
					}

					dumped_text += level_padding + "  '" + item + "' => ";
					dumped_text += debug.dump(value,level+1,max_depth,max_lines,no_show);

					shown_any = true;
				}
				dumped_text += level_padding + "]\n";

				if (!shown_any)
				{
					dumped_text = level_padding + "("+typeof(arr)+"):null\n";
				}
			}
			break;

		case 'function':
			{
				var funcname = ''+arr;
				var idx = funcname.indexOf('{');
				if (idx > 0)
				{
					funcname = funcname.substring(0, idx - 1);
				}
				dumped_text = funcname+"\n";
			}
			break;

		case 'undefined':
			dumped_text = "("+typeof(arr)+")\n";
			break;

		default:
			//Strings/Chars/Numbers etc.
			dumped_text = "("+typeof(arr)+")=='"+limited_text(arr)+"'\n";
			break;
		}
	}
	catch(e)
	{
		dumped_text = "==FAILURE==("+typeof(arr)+")\n";
	}

	var nl_index = 0;
	for ( ; max_lines > 0; max_lines--, nl_index++)
	{
		nl_index = dumped_text.indexOf("\n", nl_index);
		if (nl_index < 0)
		{
			nl_index = dumped_text.length;
			break;
		}
	}
	var dumped_continued = (dumped_text.length > nl_index ? "(continued...)" : "");
	return dumped_text.substring(0, nl_index) + dumped_continued;
};

debug.log = function()
{

};

debug.makehtml = function(text)
{
	var textneu = text.replace(/&/g,"&amp;");
	textneu = textneu.replace(/</g,"&lt;");
	textneu = textneu.replace(/>/g,"&gt;");
	textneu = textneu.replace(/\r\n/g,"<br>");
	textneu = textneu.replace(/\n/g,"<br>");
	textneu = textneu.replace(/\r/g,"<br>");
	return(textneu);
};



