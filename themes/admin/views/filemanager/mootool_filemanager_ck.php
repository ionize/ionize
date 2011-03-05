<?php
/**
 * Mootools Filemanager from ckEditor
 *
 *
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-1.2.4-core-yc.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-1.2.4.4-more-yc.js"></script>


<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Core/Core.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Layout/Layout.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Layout/Dock.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Window/Window.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Window/Modal.js"></script>

<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/FileManager.js"></script>

<?php if (is_file(BASEPATH.'../'.Theme::get_theme_path().'javascript/mootools-filemanager/Language/Language.'.Settings::get_lang().'.js')) :?>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Language/Language.<?= Settings::get_lang() ?>.js"></script>
<?php else :?>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Language/Language.en.js"></script>
<?php endif ;?>	

<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Additions.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Uploader/Fx.ProgressBar.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Uploader/Swiff.Uploader.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Uploader.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Gallery.js"></script>


<link rel="stylesheet" media="all" type="text/css" href="<?= theme_url() ?>javascript/mootools-filemanager/Css/FileManager.css" />
<link rel="stylesheet" media="all" type="text/css" href="<?= theme_url() ?>javascript/mootools-filemanager/Css/Additions.css" />


<style>

body {
	margin:0;padding:0;
}
div.filemanager-infos {
	top:0;
}

</style>


<script type="text/javascript">	

	// filemanager must be set
	var filemanager = null;

	function openFilemanager()
	{
		var complete = function(path, file)
		{
			var url = '<?php echo base_url() ?>' + path;
			window.opener.CKEDITOR.tools.callFunction('<?php echo $CKEditorFuncNum ?>', url);
			window.close();
		};


		// Get the tokken, get the options...
		var xhr = new Request.JSON(
		{
			url: '<?= admin_url() ;?>media/get_tokken',
			method: 'post',
			onSuccess: function(responseJSON, responseText)
			{
				// Opens the filemanager if the tokken can be retrieved (auth checked by get_tokken() )
				if (responseJSON && responseJSON.tokken != '')
				{
			        var options = {
						baseURL: '<?= base_url() ;?>',
						url: '<?= admin_url() ;?>media/filemanager',
						assetBasePath: '<?= theme_url() ?>javascript/mootools-filemanager/Assets',
						language: '<?php echo Settings::get_lang() ;?>',
						selectable: true,
						'uploadAuthData': responseJSON.tokken,
            			onComplete: complete
					};

					filemanager = new FileManager(options);

					filemanager.showIn('mootools-filemanager-ck');
				}
			}
		}).send();
	}
	
	window.addEvent('load', function()
	{
		openFilemanager();
	});

</script>


</head>

<body>

<div id="mootools-filemanager-ck"></div>

</body>

</html>
