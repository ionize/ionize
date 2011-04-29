
<div id="mootools-filemanager"></div>

<script type="text/javascript">	

ION.initToolbox();

// Get the tokken, get the options...
var xhr = new Request.JSON(
{
	url: '<?= admin_url() ?>media/get_tokken',
	method: 'post',
	onSuccess: function(responseJSON, responseText)
	{
		// Opens the filemanager if the tokken can be retrieved (auth checked by get_tokken() )
		if (responseJSON && responseJSON.tokken != '')
		{
			var filemanager = new FileManager({
				baseURL: base_url,
				url: admin_url + 'media/filemanager',
				assetBasePath: theme_url + 'javascript/mootools-filemanager/Assets',
				language: Lang.get('current'),
				selectable: false,
				hideOnClick: false,
				'uploadAuthData': responseJSON.tokken,
				parentContainer: 'mainPanel'
			});
			
			var content = filemanager.show();
			content.inject($('mootools-filemanager'));
		}
	}
}).send();
	
</script>
