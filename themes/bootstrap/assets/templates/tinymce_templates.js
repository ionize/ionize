/**
 * Define tinyMCE templates
 * Templates should be placed in /themes/your_theme/javascript/tinymce_templates/
 *
 */

function getTinyTemplates(path_to_templates)
{
	var template_templates = new Array(
		{
			title : "Starndart Table Style",
			src : path_to_templates + "table_standart.html",
			description : "Standart Table 5 Columns"
		},{
			title : "Striped Table Style",
			src : path_to_templates + "table_striped.html",
			description : "Striped Table 5 Columns"
		}
		,{
			title : "Bordered Table Style",
			src : path_to_templates + "table_bordered.html",
			description : "Bordered Table 5 Columns"
		},{
			title : "Condensed Table Style",
			src : path_to_templates + "table_condensed.html",
			description : "Condensed Table 5 Columns"
		},{
			title : "Combine them all Table Styles",
			src : path_to_templates + "table_combine_them_all.html",
			description : "Combine them all Table Styles 5 Columns"
		},{
			title : "Unordered List Style",
			src : path_to_templates + "lists_unordered.html",
			description : "Unordered List Style"
		},{
			title : "Unstyled List Style",
			src : path_to_templates + "lists_unstyled.html",
			description : "Unstyled List Style"
		},{
			title : "Ordered List Style",
			src : path_to_templates + "lists_ordered.html",
			description : "Ordered List Style"
		},{
			title : "Description List Style",
			src : path_to_templates + "lists_description.html",
			description : "Description List Style"
		},{
            title : "Home Page Content",
            src : path_to_templates + "homepage_content.html",
            description : "Home Page Content Template"
        }
	);
	
	return template_templates; 
}