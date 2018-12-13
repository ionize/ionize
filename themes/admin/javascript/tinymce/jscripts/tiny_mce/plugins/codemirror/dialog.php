<?php // $Id: dialog.php,v 1.1 2010/03/17 16:48:12 drifter Exp $ ?>
<html>
  <head>
    <title>{#codemirror_dlg.title}</title>
    <?php
      # see http://tinymce.moxiecode.com/punbb/viewtopic.php?id=19334
      $path = $_GET["path"];
      print " <script type=\"text/javascript\" src=\"$path/tiny_mce_popup.js\"></script>";
    ?>
    <script type="text/javascript" src="js/dialog.js"></script>
    
    <script src="js/codemirror/js/codemirror.js" type="text/javascript"></script>

	<link type="text/css" rel="stylesheet" href="../../../../../codemirror/css/codemirror.css" />

  </head>

  <body>
    <form name="source" onsubmit="CodeDialog.insert();return false;">
      <textarea id="code" name="code" cols="100" rows="30"></textarea>
    </form>

    <div class="mceActionPanel">
      <div style="float: left">
        <input type="button" id="insert" name="insert" value="{#insert}" onclick="CodeDialog.insert();" />
      </div>

      <div style="float: right">
        <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
      </div>
    </div>

  </body>
</html>
