<div id="filemanager"></div>

<script type="text/javascript">	

ION.initToolbox();

	var filemanager = new Filemanager({
		url: admin_url + 'media/filemanager',
		assetsUrl: theme_url + 'javascript/filemanager/assets',
		language: Lang.current,
		createFolders: true,
		destroy: <?php echo (Authority::can('delete', 'admin/filemanager')) ? 'true' : 'false' ?>,
		rename: <?php echo (Authority::can('rename', 'admin/filemanager')) ? 'true' : 'false' ?>,
		upload: <?php echo (Authority::can('upload', 'admin/filemanager')) ? 'true' : 'false' ?>,
		move_or_copy: <?php echo (Authority::can('move', 'admin/filemanager')) ? 'true' : 'false' ?>,
		resizeOnUpload: '<?php echo Settings::get("resize_on_upload") ?>',
		uploadAutostart: '<?php echo Settings::get("upload_autostart") ?>',
		uploadMode: '<?php echo Settings::get("upload_mode") ?>',
		standalone: false,
		selectable: false,
		hideOnClick: false,
		hideOnSelect: false,
		parentContainer: 'mainPanel',
	//    propagateData: {'uploadTokken': responseJSON.tokken},
		mkServerRequestURL: function(fm_obj, request_code, post_data)
		{
<<<<<<< HEAD
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
=======
			return {
				url: fm_obj.options.url + '/' + request_code,
				data: post_data
			};
>>>>>>> 04f6c0825f3a528a0bbc1bc715d965182da80956
		}
	});

	var content = filemanager.show();
	content.inject($('filemanager'));

</script>
