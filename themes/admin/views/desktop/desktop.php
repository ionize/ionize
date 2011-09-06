<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= Settings::get('site_title') ? Settings::get('site_title').' | ' : '' ?>Administration</title>
<meta http-equiv="imagetoolbar" content="no" />
<link rel="shortcut icon" href="<?= theme_url() ?>images/favicon.ico" type="image/x-icon" />

<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/core.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/menu.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/desktop.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/window.css" />

<!-- To be loaded if controls aren't defined through the pluginGroups 
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/taskbar.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/toolbar.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mochaui/Themes/ionize/css/accordion.css" />
-->

<link rel="stylesheet" href="<?= theme_url() ?>css/form.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/content.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/tree.css" type="text/css" />

<!--[if IE 7]><link rel="stylesheet" href="<?= theme_url() ?>css/ie7.css" /><![endif]-->
<!--[if IE 8]><link rel="stylesheet" href="<?= theme_url() ?>css/ie8.css" /><![endif]-->
<!--[if IE 9]><link rel="stylesheet" href="<?= theme_url() ?>css/ie9.css" /><![endif]-->
<!--[if lt IE 9]><script type="text/javascript" src="<?= theme_url() ?>javascript/excanvas_r43_compressed.js"></script><![endif]-->

<!-- Mootools 1.3.1 -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-core-1.3.2-full-nocompat.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-more-1.3.2.1-yc.js"></script>


<script type="text/javascript">

	if (Browser.ie)
	{
		// override mootools global default setting for fade effects:
		Fx.prototype.options.fps = 10;				// 50
		//Fx.prototype.options.unit = false;
		Fx.prototype.options.duration = 5;			// 500
		//Fx.prototype.options.frames = 1000;
		//Fx.prototype.options.frameSkip = true;
		//Fx.prototype.options.link = 'ignore';
		//Fx.prototype.frameInterval;
		Fx.Durations['short'] = 5;					// 250
		Fx.Durations['normal'] = 5;					// 500
		Fx.Durations['long'] = 5;					// 750
	}

</script>


<!-- Drag Clone -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/drag.clone.js"></script>

<!-- Date Picker -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-datepicker/datepicker.js"></script>
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/mootools-datepicker/datepicker_dashboard/datepicker_dashboard.css" />

<!-- Tab Swapper -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/TabSwapper.js"></script>

<!-- Sortable Table -->
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/SortableTable/SortableTable.css" />
<script type="text/javascript" src="<?= theme_url() ?>javascript/SortableTable/SortableTable.js"></script>

<!-- CwCrop -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/cwcrop/ysr-crop.js"></script>
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/cwcrop/ysr-crop.css" />

<!-- swfObject -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/swfobject.js"></script>

<!-- CodeMirror -->
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/codemirror/css/codemirror.css" />
<script type="text/javascript" src="<?= theme_url() ?>javascript/codemirror/js/codemirror.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/codemirror/codemirror.views.js"></script>

<!-- Base URL & languages translations available for javascript -->
<script type="text/javascript">
	
	/** 
	 * Global base_url value.
	 * Used by mocha-init and should be used by any javascript class or method which needs to access to resources
	 */
	var base_url = '<?= base_url() ?>';
	var theme_url = '<?= theme_url() ?>';
	var site_theme_url = '<?= base_url() . 'themes/' . Settings::get('theme') .'/' ?>';
	var admin_url = '<?= base_url().Settings::get_lang('current') ?>/<?=config_item('admin_url')?>/';
	var date_format = '<?= Settings::get('date_format'); ?>';

	/** 
	 * Show help tips.
	 * Used by mocha init-content
	 */
	var show_help_tips = '<?= Settings::get('show_help_tips') ?>';

	/** 
	 * Gets all the Ionize lang items and put them into a Lang hash object
	 * To get an item : Lang.get('php_lang_item_key');
	 */
	<?php $this->load->view('javascript_lang');	?>

</script>

<!-- Mocha UI-->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Core/core.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Core/create.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Core/require.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Core/canvas.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Core/content.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Core/persist.js"></script>

<!-- To be loaded if controls aren't defined through the pluginGroups 
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/accordion/accordion.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/desktop/desktop.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/column/column.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/panel/panel.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/dock/dock.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/dockhtml/dockhtml.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/menu/menu.js"></script>
-->

<!-- Normal load -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/taskbar/taskbar.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/toolbar/toolbar.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/window/window.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/window/modal.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/Controls/spinner/spinner.js"></script>

<!-- UI initialization -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mochaui/init.js"></script>


<!-- Ionize -->
<!-- In a production environment, these files should be grouped and compressed -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_core.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_window.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_request.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_content.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_droppable.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_forms.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_mediamanager.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_itemsmanager.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_tinymce.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize/ionize_tree_xhr.js"></script>


<!-- Mootools Filemanager -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/FileManager.js"></script>
<?php if (is_file(BASEPATH.'../'.Theme::get_theme_path().'javascript/mootools-filemanager/Language/Language.'.Settings::get_lang().'.js')) :?>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Language/Language.<?= Settings::get_lang() ?>.js"></script>
<?php else :?>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Language/Language.en.js"></script>
<?php endif ;?>	

<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Uploader/Fx.ProgressBar.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Uploader/Swiff.Uploader.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Uploader.js"></script>

<!-- Flash test. No Flash means the NoFlash MTFM Uploader needs to be loaded -->
<!-- If the player < 9, no Flash upload -->
<script type="text/javascript">

	var upload_mode = '<?= Settings::get('media_upload_mode'); ?>';
	
	if(swfobject.ua.pv[0] < 9 || upload_mode=='single')
	{
		Asset.javascript('<?= theme_url() ?>javascript/mootools-filemanager/Source/NoFlash.Uploader.js');
	}

</script>
<!--
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/NoFlash.Uploader.js"></script>
-->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/Gallery.js"></script>
<link rel="stylesheet" media="all" type="text/css" href="<?= theme_url() ?>javascript/mootools-filemanager/Assets/Css/FileManager_ionize.css" />
<!--[if IE 7]><link rel="stylesheet" href="<?= theme_url() ?>javascript/mootools-filemanager/Assets/Css/FileManager_ie7.css" /><![endif]-->


<!-- TinyMCE -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/tinymce/jscripts/tiny_mce/tiny_mce_src.js"></script>


<!-- If users templates, add them to the init object -->
<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/templates/tinymce_templates.js' )) :?>
	<script type="text/javascript" src="<?= base_url() ?>themes/<?= Settings::get('theme') ?>/assets/templates/tinymce_templates.js"></script>
<?php else :?>
	<script type="text/javascript">
		var getTinyTemplates = false;
	</script>
<?php endif ;?>

<script type="text/javascript">
	/**
	 * Global filemanager
	 *
	 */
	var filemanager = '';

	/** 
	 * Global MediaManager
	 *
	 */
	var mediaManager = new IonizeMediaManager(
	{
		baseUrl: base_url,
		adminUrl: admin_url,
		pictureContainer:'pictureContainer', 
		musicContainer:'musicContainer', 
		videoContainer:'videoContainer',
		fileContainer:'fileContainer',
		fileButton:'.fmButton',
		wait:'waitPicture',
		mode:'<?= Settings::get('filemanager') ?>',
		thumbSize: <?= (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120 ;?>,		
		pictureArray:Array('<?= implode("','", Settings::get_allowed_extensions('picture')) ?>'),
		musicArray:Array('<?= implode("','", Settings::get_allowed_extensions('music')) ?>'),
		videoArray:Array('<?= implode("','", Settings::get_allowed_extensions('video')) ?>'),
		fileArray:Array('<?= implode("','", Settings::get_allowed_extensions('file')) ?>')
	});

	/* If user's theme has a tinyMCE.css content CSS file, load it.
	 * else, load the standard tinyMCE content CSS file
	 *
	 */

	<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/css/tinyMCE.css' )) :?>
		var tinyCSS = '<?= base_url().'themes/'.Settings::get('theme').'/assets/css/tinyMCE.css' ?>';
	<?php else :?>
		var tinyCSS = '<?= theme_url().'css/tinyMCE.css' ?>';
	<?php endif ;?>

	var tinyButtons1 = '<?= Settings::get('tinybuttons1'); ?>';
	var tinyButtons2 = '<?= Settings::get('tinybuttons2'); ?>';
	var tinyButtons3 = '<?= Settings::get('tinybuttons3'); ?>';
	var tinyBlockFormats = '<?= Settings::get('tinyblockformats'); ?>';


</script>


</head>
<body>

<div id="desktop" class="desktop"></div>

</body>
</html>

