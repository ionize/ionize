/**
 * Ionize implementation of Codemirror for views editing
 *
 *
 */

var viewmirror;


function ViewCodeMirror(place, options)
{
	// Container Div
	this.home = document.createElement("DIV");
	
	if (place.appendChild)
		place.appendChild(this.home);
	else
		place(this.home);
	
	var self = this;
	
	// Menu Div
	this.menu = document.createElement("DIV");
	this.menu.setAttribute('class', 'CodeMirror-menu');
	this.home.appendChild(this.menu);
	
	/**
	 * Create one menu button
	 *
	 */
	function makeEditorButton(action, className, name)
	{
		var a = document.createElement('A');
		a.setAttribute('class', 'cmButton ' + className);

		var span = document.createElement('SPAN');
		
		if (name != '') {
			var t = document.createTextNode(name);
			span.appendChild(t);
		}
		
		span.setAttribute('class', 'cmIcon ' + className);

		a.appendChild(span);

		self.menu.appendChild(a);
		a.onclick = function()
		{
			self[action].call(self);
		};
	}

	function makeSeparator()
	{
		var s = document.createElement('SPAN');
		s.setAttribute('class', 'cmSeparator');
		self.menu.appendChild(s);
	}
	
	
	// Define buttons
	makeEditorButton('tag_strong', 'cm_bold', '');
	makeEditorButton('tag_italic', 'cm_italic', '');
	makeSeparator();
	makeEditorButton('tag_h1', 'wide', 'h1');
	makeEditorButton('tag_h2', 'wide', 'h2');
	makeEditorButton('tag_h3', 'wide', 'h3');
	makeEditorButton('tag_h4', 'wide', 'h4');
	makeEditorButton('tag_h5', 'wide', 'h5');
	makeEditorButton('tag_h6', 'wide', 'h6');
	makeSeparator();
	makeEditorButton('tag_div', 'wide', 'div');
	makeEditorButton('tag_span', 'wide', 'span');
	makeSeparator();
	makeEditorButton('tag_a', 'cm_link', '');
	makeEditorButton('tag_table', 'cm_table', '');

	makeSeparator();
	makeEditorButton('tag_ion', 'wide', '<ion: />');
	
	this.mirror = new CodeMirror(this.home, options);
	
	codemirror = this.mirror;
}


/**
 * ViewMirror prototype
 *
 * Definition of menu buttons
 *
 */
ViewCodeMirror.prototype = 
{
	search: function() 
	{
		var text = prompt("Enter search term:", "");
		if (!text) return;
		
		var first = true;
		
		do
		{
			var cursor = this.mirror.getSearchCursor(text, first);
			first = false;
			while (cursor.findNext())
			{
				cursor.select();
				if (!confirm("Search again?"))
					return;
			}
		}
		while (confirm("End of document reached. Start over?"));
	},
	
	
	tag_strong: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<strong>' + str + '</strong>');
	},
	
	tag_italic: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<em>' + str + '</em>');
	},

	tag_h1: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<h1>' + str + '</h1>');
	},
	
	tag_h2: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<h2>' + str + '</h2>');
	},

	tag_h3: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<h3>' + str + '</h3>');
	},

	tag_h4: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<h4>' + str + '</h4>');
	},

	tag_h5: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<h5>' + str + '</h5>');
	},

	tag_h6: function()
	{
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<h6>' + str + '</h6>');
	},

	tag_div: function(){
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<div class="">' + str + '</div>');
	},

	tag_span: function(){
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<span class="">' + str + '</span>');
	},

	tag_a: function(){
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<a href="" class="">' + str + '</a>');
	},

	tag_table: function(){
		var str = this.mirror.selection();
		this.mirror.replaceSelection('<table>\n<caption></caption>\n<thead>\n<tr>\n<th></th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td></td>\n</tr>\n</tbody>\n</table>' + str );
		this.reindent();
	},
	
	tag_ion: function(){
		var str = this.mirror.selection();
	
		this.mirror.replaceSelection('<ion:name attr="" />' + str );
	},



	
	replace: function() 
	{
		// This is a replace-all, but it is possible to implement a
		// prompting replace.
		var from = prompt("Enter search string:", ""), to;
		if (from) to = prompt("What should it be replaced with?", "");
		if (to == null) return;
	
		var cursor = this.mirror.getSearchCursor(from, false);
		while (cursor.findNext())
		cursor.replace(to);
	},
	
	jump: function()
	{
		var line = prompt("Jump to line:", "");
		if (line && !isNaN(Number(line)))
		this.mirror.jumpToLine(Number(line));
	},
	
	
	macro: function()
	{
		var name = prompt("Name your constructor:", "");
		if (name)
		this.mirror.replaceSelection("function " + name + "() {\n  \n}\n\n" + name + ".prototype = {\n  \n};\n");
	},
	
	reindent: function()
	{
		this.mirror.reindent();
	}
};
