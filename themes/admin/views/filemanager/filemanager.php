<div id="filemanager"></div>

<script type="text/javascript">	

ION.initToolbox();

// Get the tokken, get the options...
var xhr = new Request.JSON(
{
	url: '<?php echo admin_url(); ?>media/get_tokken',
	method: 'post',
	onSuccess: function(responseJSON, responseText)
	{
		// Opens the filemanager if the tokken can be retrieved (auth checked by get_tokken() )
		if (responseJSON && responseJSON.tokken != '')
		{
			var filemanager = new Filemanager({
                url: admin_url + 'media/filemanager',
                assetsUrl: theme_url + 'javascript/filemanager/assets',
                language: Lang.current,
                createFolders: true,
                destroy: true,
                rename: true,
                upload: true,
                move_or_copy: true,
                resizeOnUpload: '<?php echo Settings::get("resize_on_upload") ?>',
                uploadAutostart: '<?php echo Settings::get("upload_autostart") ?>',
                uploadMode: '<?php echo Settings::get("upload_mode") ?>',
				standalone: false,
				selectable: false,
				hideOnClick: false,
				hideOnSelect: false,
				parentContainer: 'mainPanel',
				propagateData: {'uploadTokken': responseJSON.tokken},
				mkServerRequestURL: function(fm_obj, request_code, post_data)
				{
					return {
						url: fm_obj.options.url + '/' + request_code,
						data: post_data
					};
				}
			});
			
			var content = filemanager.show();
			content.inject($('filemanager'));
		}
	}
}).send();
	
</script>
