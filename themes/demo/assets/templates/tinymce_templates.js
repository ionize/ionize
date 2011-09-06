/**
 * Define tinyMCE templates
 * Templates should be placed in /themes/your_theme/javascript/tinymce_templates/
 *
 */

function getTinyTemplates(path_to_templates)
{
	var template_templates = new Array(
		{
			title : "Div 50%",
			src : path_to_templates + "div_50.html",
			description : "Div 50%"
		}
	);
	console.log(path_to_templates+ "div_50.html");
	
	return template_templates; 
}