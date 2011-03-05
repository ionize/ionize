/**
 * Define tinyMCE templates
 * Templates should be placed in /themes/your_theme/javascript/tinymce_templates/
 *
 */

function getTinyTemplates(path_to_templates)
{
	var template_templates = new Array(
		{
			title : "Table with 5 columns",
			src : path_to_templates + "table_5_columns.html",
			description : "Table of 5 columns"
		}
		,{
			title : "UL with link style",
			src : path_to_templates + "ul_link_style.html",
			description : "UL with link style"
		}
	);
	
	return template_templates; 
}