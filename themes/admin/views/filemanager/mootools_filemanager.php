
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
//				baseURL: base_url,
				url: admin_url + 'media/filemanager',
//				directory: '/',
				URLpath4assets: theme_url + 'javascript/mootools-filemanager/Assets',
//				assetBasePath: theme_url + 'javascript/mootools-filemanager/Assets',
				language: Lang.get('current'),
				standalone: false,
				selectable: false,
//				thumbSmallSize: 120,
				createFolders: true,
				destroy: true,
				rename: true,
				move_or_copy: true,
				hideOnClick: false,
				hideOnSelect: false,
				parentContainer: 'mainPanel',
				propagateData: {'uploadAuthData': responseJSON.tokken},
				mkServerRequestURL: function(fm_obj, request_code, post_data)
				{
					return {
						url: fm_obj.options.url + '/' + request_code,
						data: post_data
					};
				}
			});
			
			var content = filemanager.show();
			content.inject($('mootools-filemanager'));
		}
	}
}).send();
	
</script>
