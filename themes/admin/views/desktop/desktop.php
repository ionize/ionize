<?php
$v = Settings::get('ionize_version');
$c = '?v='.$v;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title><?php echo lang('ionize_administration') . ' | ' . (Settings::get('site_title') ? Settings::get('site_title') : ''); ?></title>
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<link rel="shortcut icon" href="<?php echo admin_style_url(); ?>images/favicon.ico" type="image/x-icon" />

<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/core.css<?php echo $c ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/menu.css<?php echo $c ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/desktop.css<?php echo $c ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mochaui/Themes/ionize/css/window.css<?php echo $c ?>" />

<link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/form.css<?php echo $c ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/content.css<?php echo $c ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/tree.css<?php echo $c ?>" type="text/css" />

<!--[if IE 7]><link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/ie7.css<?php echo $c ?>" /><![endif]-->
<!--[if IE 8]><link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/ie8.css<?php echo $c ?>" /><![endif]-->
<!--[if IE 9]><link rel="stylesheet" href="<?php echo admin_style_url(); ?>css/ie9.css<?php echo $c ?>" /><![endif]-->
<!--[if lt IE 9]><script type="text/javascript" src="<?php echo theme_url(); ?>javascript/excanvas_r43_compressed.js<?php echo $c ?>"></script><![endif]-->

<!-- Mootools 1.4.5  -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-core-1.5.0-full-nocompat-yc.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-more-1.5.0-yc.js"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools_locale/eu.js"></script>

<?php if (
	Settings::get('dashboard_google') == '1'
	&& Settings::get('google_analytics_profile_id') !=''
	&& Settings::get('google_analytics_email') !=''
	&& Settings::get('google_analytics_password') !=''
) :?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"], 'language': '<?php echo Settings::get_lang() ?>'});
</script>
<?php endif ;?>

<!-- Upload -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Request.File.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Form.MultipleFileInput.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Form.Upload.js<?php echo $c ?>"></script>

<!-- TextboxList -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TextboxList/TextboxList.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TextboxList/TextboxList.Autocomplete.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TextboxList/TextboxList.Autocomplete.Binary.js<?php echo $c ?>"></script>

<!-- Drag Clone -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/drag.clone.js<?php echo $c ?>"></script>

<!-- Date Picker -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-datepicker/datepicker.js<?php echo $c ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/mootools-datepicker/datepicker_dashboard/datepicker_dashboard.css<?php echo $c ?>" />

<!-- Tab Swapper -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/TabSwapper.js<?php echo $c ?>"></script>

<!-- Sortable Table -->
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/SortableTable/SortableTable.css<?php echo $c ?>" />
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/SortableTable/SortableTable.js<?php echo $c ?>"></script>

<!-- CwCrop -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/cwcrop/ysr-crop.js<?php echo $c ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/cwcrop/ysr-crop.css<?php echo $c ?>" />

<!-- Mootools Extra -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mootools-class-extras.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Form.AutoGrow.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/Fx.ProgressBar.js<?php echo $c ?>"></script>

<!-- swfObject -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/swfobject.js<?php echo $c ?>"></script>

<!-- CodeMirror -->
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/codemirror/css/codemirror.css<?php echo $c ?>" />
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/codemirror/js/codemirror.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/codemirror/codemirror.views.js<?php echo $c ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo theme_url(); ?>javascript/soundmanager/style/flashblock.css<?php echo $c ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo theme_url(); ?>javascript/soundmanager/style/360player.css<?php echo $c ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo theme_url(); ?>javascript/soundmanager/style/360player-visualization.css<?php echo $c ?>" />

<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/soundmanager/script/berniecode-animator.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/soundmanager/script/soundmanager2-jsmin.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/soundmanager/script/360player.js<?php echo $c ?>"></script>
<script type="text/javascript">
	soundManager.setup({
		url: '<?php echo theme_url() ?>javascript/soundmanager/swf/',
		preferFlash: false,
		allowScriptAccess: 'always'
	});
	if (window.location.href.match(/html5/i)) {
		// for testing IE 9, etc.
		soundManager.useHTML5Audio = true;
	}
</script>

<!-- Base URL & languages translations available for javascript -->
<script type="text/javascript">
	
	/** 
	 * Global JS variables.
	 * Used by mocha-init and should be used by any javascript class or method which needs to access to resources
	 */
	var base_url = '<?php echo base_url(); ?>';
	var theme_url = '<?php echo theme_url(); ?>';
	var site_theme_url = '<?php echo base_url() . 'themes/' . Settings::get('theme') .'/'; ?>';
	var modules_url = '<?php echo base_url(); ?>modules/';
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
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/core.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/create.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/require.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/canvas.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/content.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Core/persist.js<?php echo $c ?>"></script>

<!-- Normal load -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/taskbar/taskbar.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/toolbar/toolbar.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/window/window.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/window/modal.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/Controls/spinner/spinner.js<?php echo $c ?>"></script>

<!-- UI initialization -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/mochaui/init.js<?php echo $c ?>"></script>

<!-- Ionize -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_core.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_panels.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_window.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_request.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_content.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_droppable.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_forms.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_list.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_mediamanager.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_extendmanager.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_extendlinkmanager.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_extendmediamanager.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_staticitemmanager.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_itemsmanager.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tinymce.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tree.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tree_xhr.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_list_filter.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_notify.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_user.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tracker.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_select.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_button.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_tabs.js<?php echo $c ?>"></script>

<!-- Authority -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/ionize/ionize_authority.js<?php echo $c ?>"></script>

<!-- DropZone -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/Request.Blob.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/DropZone.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/DropZone.HTML5.js<?php echo $c ?>"></script>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/dropzone/DropZone.HTML4.js<?php echo $c ?>"></script>

<!-- Ionize Filemanager -->
<link type="text/css" rel="stylesheet" href="<?php echo theme_url(); ?>javascript/filemanager/assets/css/filemanager.css" />
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/filemanager/filemanager.js<?php echo $c ?>"></script>
<?php
	if (is_file(BASEPATH.'../'.Theme::get_theme_path().'javascript/filemanager/language/Language.'.Settings::get_lang().'.js'))
		$filemanager_lang = Settings::get_lang();
	else
		$filemanager_lang = 'en';
?>
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/filemanager/language/Language.<?php echo $filemanager_lang ?>.js<?php echo $c ?>"></script>

<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo theme_url(); ?>javascript/tinymce/jscripts/tiny_mce/tiny_mce.js<?php echo $c ?>"></script>

<!-- If users templates, add them to the init object -->
<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/templates/tinymce_templates.js' )) :?>
	<script type="text/javascript" src="<?php echo base_url(); ?>themes/<?php echo Settings::get('theme'); ?>/assets/templates/tinymce_templates.js<?php echo $c ?>"></script>
<?php else :?>
	<script type="text/javascript">
		var getTinyTemplates = false;
	</script>
<?php endif ;?>

<!-- TinyMCE user's Setup addon -->
<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/javascript/tinyMCE.js' )) :?>
	<script type="text/javascript" src="<?php echo base_url(); ?>themes/<?php echo Settings::get('theme'); ?>/assets/javascript/tinyMCE.js<?php echo $c ?>"></script>
<?php endif ;?>

<script type="text/javascript">

	// Global MediaManager
	var mediaManager = new IonizeMediaManager(
	{
		baseUrl: base_url,
		adminUrl: admin_url,
		container:'mediaContainer',
		fileButton:'.fmButton',
		wait:'waitPicture',
        resizeOnUpload: '<?php echo Settings::get('resize_on_upload'); ?>',
        uploadAutostart: '<?php echo Settings::get('upload_autostart'); ?>',
        uploadMode: '<?php echo Settings::get('upload_mode'); ?>',
		thumbSize: <?php echo (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120 ;?>
	});

	var extendManager =  new ION.ExtendManager();
//	var extendLinkManager =  new ION.ExtendLinkManager();
	var staticItemManager =  new ION.StaticItemManager();

	// If user's theme has a tinyMCE.css content CSS file, load it.
	// else, load the standard tinyMCE content CSS file
	<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/css/tinyMCE.css' )) :?>
		var tinyCSS = '<?php echo base_url().'themes/'.Settings::get('theme').'/assets/css/tinyMCE.css'; ?>';
	<?php else :?>
		var tinyCSS = '<?php echo admin_style_url() .'css/tinyMCE.css'; ?>';
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
		<link rel="stylesheet" href="<?php echo base_url(); ?>modules/<?php echo $module ;?>/assets/css/admin.css<?php echo $c ?>" type="text/css" />
	<?php endif;?>
	<?php if (file_exists(MODPATH.$module.'/assets/javascript/admin.js')): ?>
		<script type="text/javascript" src="<?php echo base_url(); ?>modules/<?php echo $module ;?>/assets/javascript/admin.js<?php echo $c ?>"></script>
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

