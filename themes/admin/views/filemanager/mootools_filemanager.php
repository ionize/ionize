
<div id="mootools-filemanager" style="width: 100%; height: 100%; position: absolute; overflow: hidden;"></div>

<style>

div.filemanager div.filemanager-menu {
	background-color: #f2f2f2;
	bottom:0;
	top:0;
	right:12px;
}
div.filemanager-menu label  {
	padding-top:4px;
}
</style>


<script type="text/javascript">	

MUI.initToolbox();

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
