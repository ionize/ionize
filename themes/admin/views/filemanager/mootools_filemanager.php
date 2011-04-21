
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
	        var options = {
				baseURL: '<?= base_url() ;?>',
				url: '<?= admin_url() ?>media/filemanager',
				assetBasePath: '<?= theme_url() ?>javascript/mootools-filemanager/Assets',
				language: '<?php echo Settings::get_lang() ;?>',
				selectable: false,
				'uploadAuthData': responseJSON.tokken
			};

			filemanager = new FileManager(options);

			filemanager.showIn('mootools-filemanager');
		}
	}
}).send();
	
</script>
