<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8" />
  <meta http-equiv="content-language" content="<?php echo $_GET['langCode']; ?>" />
  
  <title>MooTools FileManager CKEditor example</title>
  
  <!-- thirdparty/MooTools -->
  <script type="text/javascript" src="mootools-core-1.3.js"></script>
  <script type="text/javascript" src="mootools-more.js"></script>
  
  <!-- thirdparty/MooTools-FileManager -->
  <script type="text/javascript" src="../Source/FileManager.js"></script>
  <script type="text/javascript" src="../Source/Uploader/Fx.ProgressBar.js"></script>
  <script type="text/javascript" src="../Source/Uploader/Swiff.Uploader.js"></script>
  <script type="text/javascript" src="../Source/Uploader.js"></script>
  <script type="text/javascript" src="../Language/Language.<?= $_GET["langCode"]; ?>.js"></script>

  <script type="text/javascript">
  /* <![CDATA[ */
    
   /* To use Mootools-FileManager with CKEditor you need set the following CKEDITOR.configs:
    *
    * CKEDITOR.config.filebrowserBrowseUrl      = 'path/to/this/CKEditor.php';
    * CKEDITOR.config.filebrowserWindowWidth    = 1024; // optional
    * CKEDITOR.config.filebrowserWindowHeight   = 700;  // optional
    *
    */
  
    function openFilemanager() {
      var complete = function(path, file){
        window.opener.CKEDITOR.tools.callFunction('<?= $_GET["CKEditorFuncNum"]; ?>', path);
        window.close();
      };

      var fileManager = new FileManager({
          url: 'manager.php',
          assetBasePath: '../Assets',
          language: '<?= $_GET["langCode"]; ?>',
          destroy: true,
          upload: true,
          rename: true,
          download: true,
          createFolders: true,
          selectable: true,
          hideClose: true,
          hideOverlay: true,
          onComplete: complete
      });
      fileManager.filemanager.setStyle('width','100%');
      fileManager.filemanager.setStyle('height','95%');
      
      fileManager.show();
    }
    
    window.addEvent('domready', function(){
      openFilemanager();
    });
  /* ]]> */
  </script>
  
  <style type="text/css">
  body {
    overflow: hidden;
  }
  </style>
</head>
<body>
</body>
</html>