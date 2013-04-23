<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title><?php echo lang('ionize_administration') . ' | ' . (Settings::get('site_title') ? Settings::get('site_title') : ''); ?></title>
<meta http-equiv="imagetoolbar" content="no" />
<link rel="shortcut icon" href="<?php echo theme_url(); ?>images/favicon.ico" type="image/x-icon" />

<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/core.css" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/menu.css" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/desktop.css" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/window.css" />

<!-- To be loaded if controls aren't defined through the pluginGroups 
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/taskbar.css" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/toolbar.css" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/accordion.css" />
-->

<link rel="stylesheet" href="<?php echo theme_url(); ?>css/form.css" type="text/css" />
<link rel="stylesheet" href="<?php echo theme_url(); ?>css/content.css" type="text/css" />
<link rel="stylesheet" href="<?php echo theme_url(); ?>css/tree.css" type="text/css" />

<!--[if IE 7]><link rel="stylesheet" href="<?php echo theme_url(); ?>css/ie7.css" /><![endif]-->
<!--[if IE 8]><link rel="stylesheet" href="<?php echo theme_url(); ?>css/ie8.css" /><![endif]-->
<!--[if IE 9]><link rel="stylesheet" href="<?php echo theme_url(); ?>css/ie9.css" /><![endif]-->
<!--[if lt IE 9]><script type="text/javascript" src="<?php echo theme_url(); ?>javascript/excanvas_r43_compressed.js"></script><![endif]-->

<!-- Mootools 1.4.5  -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-core-1.4.5-full-nocompat-yc.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-more-1.4.0.1.js"></script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]});
</script>


<!-- Upload -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Request.File.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Form.MultipleFileInput.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Form.Upload.js"></script>

<!-- TextboxList -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TextboxList/TextboxList.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TextboxList/TextboxList.Autocomplete.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TextboxList/TextboxList.Autocomplete.Binary.js"></script>

<!-- Drag Clone -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/drag.clone.js"></script>

<!-- Date Picker -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-datepicker/datepicker.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mootools-datepicker/datepicker_dashboard/datepicker_dashboard.css" />

<!-- Tab Swapper -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TabSwapper.js"></script>

<!-- Sortable Table -->
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/SortableTable/SortableTable.css" />
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/SortableTable/SortableTable.js"></script>

<!-- CwCrop -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/cwcrop/ysr-crop.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/cwcrop/ysr-crop.css" />

<!-- Mootools Extra -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-class-extras.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Form.AutoGrow.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Fx.ProgressBar.js"></script>

<!-- swfObject -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/swfobject.js"></script>

<!-- CodeMirror -->
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/codemirror/css/codemirror.css" />
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/codemirror/js/codemirror.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/codemirror/codemirror.views.js"></script>

<!-- Base URL & languages translations available for javascript -->
<script type="text/javascript">
	
	/** 
	 * Global JS variables.
	 * Used by mocha-init and should be used by any javascript class or method which needs to access to resources
	 */
	var base_url = '<?php echo base_url(); ?>';
	var theme_url = '<?php echo theme_url(); ?>';
	var site_theme_url = '<?php echo base_url() . 'themes/' . Settings::get('theme') .'/'; ?>';
	var admin_url = '<?php echo base_url().Settings::get_lang('current'); ?>/<?php echo config_item('admin_url'); ?>/';
	var date_format = '<?php echo Settings::get('date_format'); ?>';

	/**
	 * Show help tips.
	 * Used by mocha init-content
	 */
	var show_help_tips = '<?php echo Settings::get('show_help_tips'); ?>';

	/** 
	 * Gets all the Ionize lang items and put them into a Lang hash object
	 * To get an item : Lang.get('php_lang_item_key');
	 */
	<?php $this->load->view('desktop/javascript_lang');	?>
	<?php $this->load->view('desktop/javascript_settings');	?>

</script>

<!-- Mocha UI-->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/core.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/create.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/require.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/canvas.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/content.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/persist.js"></script>

<!-- To be loaded if controls aren't defined through the pluginGroups 
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/accordion/accordion.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/desktop/desktop.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/column/column.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/panel/panel.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/dock/dock.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/dockhtml/dockhtml.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/menu/menu.js"></script>
-->

<!-- Normal load -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/taskbar/taskbar.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/toolbar/toolbar.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/window/window.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/window/modal.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/spinner/spinner.js"></script>

<!-- UI initialization -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/init.js"></script>

<!-- Ionize -->
<!-- In a production environment, these files should be grouped and compressed -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_core.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_panels.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_window.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_request.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_content.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_droppable.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_forms.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_mediamanager.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_itemsmanager.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tinymce.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tree.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tree_xhr.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_list_filter.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_notify.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_user.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tracker.js"></script>


<!-- Authority -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_authority.js"></script>
<script type="text/javascript">
	ION.Authority.initialize();
</script>

<!-- DropZone -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/Request.Blob.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/DropZone.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/DropZone.HTML5.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/DropZone.HTML4.js"></script>


<!-- Ionize Filemanager -->
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/filemanager/assets/css/filemanager.css" />
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/filemanager/filemanager.js"></script>
<?php
	if (is_file(BASEPATH.'../'.Theme::get_theme_path().'javascript/filemanager/language/Language.'.Settings::get_lang().'.js'))
		$filemanager_lang = Settings::get_lang();
	else
		$filemanager_lang = 'en';
?>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/filemanager/language/Language.<?php echo $filemanager_lang ?>.js"></script>


<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/tinymce/jscripts/tiny_mce/tiny_mce_src.js"></script>


<!-- If users templates, add them to the init object -->
<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/templates/tinymce_templates.js' )) :?>
	<script type="text/javascript" src="<?php echo base_url(); ?>themes/<?php echo Settings::get('theme'); ?>/assets/templates/tinymce_templates.js"></script>
<?php else :?>
	<script type="text/javascript">
		var getTinyTemplates = false;
	</script>
<?php endif ;?>

<!-- TinyMCE user's Setup addon -->
<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/javascript/tinyMCE.js' )) :?>
	<script type="text/javascript" src="<?php echo base_url(); ?>themes/<?php echo Settings::get('theme'); ?>/assets/javascript/tinyMCE.js"></script>
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
		mode:'<?php echo Settings::get('filemanager'); ?>',
        resizeOnUpload: '<?php echo Settings::get('resize_on_upload'); ?>',
        uploadAutostart: '<?php echo Settings::get('upload_autostart'); ?>',
        uploadMode: '<?php echo Settings::get('upload_mode'); ?>',
		thumbSize: <?php echo (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120 ;?>,
		pictureArray:Array('<?php echo implode("','", Settings::get_allowed_extensions('picture')); ?>'),
		musicArray:Array('<?php echo implode("','", Settings::get_allowed_extensions('music')); ?>'),
		videoArray:Array('<?php echo implode("','", Settings::get_allowed_extensions('video')); ?>'),
		fileArray:Array('<?php echo implode("','", Settings::get_allowed_extensions('file')); ?>')
	});

	/* If user's theme has a tinyMCE.css content CSS file, load it.
	 * else, load the standard tinyMCE content CSS file
	 *
	 */

	<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/css/tinyMCE.css' )) :?>
		var tinyCSS = '<?php echo base_url().'themes/'.Settings::get('theme').'/assets/css/tinyMCE.css'; ?>';
	<?php else :?>
		var tinyCSS = '<?php echo theme_url().'css/tinyMCE.css'; ?>';
	<?php endif ;?>

	var tinyButtons1 = '<?php echo Settings::get('tinybuttons1'); ?>';
	var tinyButtons2 = '<?php echo Settings::get('tinybuttons2'); ?>';
	var tinyButtons3 = '<?php echo Settings::get('tinybuttons3'); ?>';
	var smallTinyButtons1 = '<?php echo Settings::get('smalltinybuttons1'); ?>';
	var smallTinyButtons2 = '<?php echo Settings::get('smalltinybuttons2'); ?>';
	var smallTinyButtons3 = '<?php echo Settings::get('smalltinybuttons3'); ?>';
	var tinyBlockFormats = '<?php echo Settings::get('tinyblockformats'); ?>';

</script>

<!-- Module's CSS / JS files -->
<?php foreach($modules as $module):?>
	<?php if (file_exists(MODPATH.$module.'/assets/css/admin.css')): ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>modules/<?php echo $module ;?>/assets/css/admin.css" type="text/css" />
	<?php endif;?>
	<?php if (file_exists(MODPATH.$module.'/assets/javascript/admin.js')): ?>
		<script type="text/javascript" src="<?php echo base_url(); ?>modules/<?php echo $module ;?>/assets/javascript/admin.js"></script>
	<?php endif;?>
<?php endforeach; ?>


</head>
<body>

<div id="desktop" class="desktop"></div>

<?php if (Settings::get('enable_backend_tracker') == '1') :?>
	<script type="text/javascript">
		Ionize.Tracker.initialize({
			'parent':'desktop',
			'updateDelay':10000
		});
		Ionize.Tracker.startTracking();
	</script>
<?php endif; ?>
</body>
</html>

