<?php

/*
As AJAX calls cannot set cookies, we set up the session for the authentication demonstration right here; that way, the session cookie
will travel with every request.
*/
session_name('alt_session_name');
if (!session_start()) die('session_start() failed');

/*
set a 'secret' value to doublecheck the legality of the session: did it originate from here?
*/
$_SESSION['FileManager'] = 'DemoMagick';

/*
Note that for the sake of the demo, we simulate an UNauthorized user in the session.
*/
$_SESSION['UploadAuth'] = 'NO';


/* the remainder of the code does not need access to the session data. */
session_write_close();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="content-language" content="<?php echo $_GET['langCode']; ?>" />

	<title>MooTools FileManager CKEditor example</title>

	<!-- thirdparty/MooTools -->
	<script type="text/javascript" src="mootools-core.js"></script>
	<script type="text/javascript" src="mootools-more.js"></script>

	<!-- thirdparty/MooTools-FileManager -->
	<script type="text/javascript" src="../Source/FileManager.js"></script>
	<script type="text/javascript" src="../Source/Uploader/Fx.ProgressBar.js"></script>
	<script type="text/javascript" src="../Source/Uploader/Swiff.Uploader.js"></script>
	<script type="text/javascript" src="../Source/Uploader.js"></script>
	<script type="text/javascript" src="../Language/Language.<?php echo $_GET['langCode']; ?>.js"></script>

	<script type="text/javascript">
		/* <![CDATA[ */

		/*
		 * To use Mootools-FileManager with CKEditor you need set the following CKEDITOR.configs:
		 *
		 * CKEDITOR.config.filebrowserBrowseUrl      = 'path/to/this/CKEditor.php';
		 * CKEDITOR.config.filebrowserWindowWidth    = 1024; // optional
		 * CKEDITOR.config.filebrowserWindowHeight   = 700;  // optional
		 *
		 */

		function openFilemanager() {
			var complete = function(path, file) {
				window.opener.CKEDITOR.tools.callFunction("<?php echo $_GET['CKEditorFuncNum']; ?>", path);
				window.close();
			};

			var fileManager = new FileManager({
				url: 'manager.php',
				assetBasePath: '../Assets',
				language: "<?php echo $_GET['langCode']; ?>",
				destroy: true,
				upload: true,
				rename: true,
				download: true,
				createFolders: true,
				selectable: true,
				hideClose: true,
				hideOverlay: true,
				onComplete: complete,
				// zIndex: ???,
				styles: {
					'width': '95%',
					'height': '95%'
				}
			});
			//fileManager.filemanager.setStyle('width','100%');
			//fileManager.filemanager.setStyle('height','95%');

			fileManager.show();
		}

		window.addEvent('domready', function(){
			openFilemanager();
		});
		/* ]]> */
	</script>

	<link rel="stylesheet" href="demos.css" type="text/css" />

	<style type="text/css">
		body {
			overflow: hidden;
		}
	</style>
</head>
<body>
</body>
</html>