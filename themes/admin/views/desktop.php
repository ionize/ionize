<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= Settings::get('site_name') ? Settings::get('site_name').' | ' : '' ?>Administration</title>
<meta http-equiv="imagetoolbar" content="no" />
<link rel="shortcut icon" href="<?= theme_url() ?>images/favicon.ico" type="image/x-icon" />

<link rel="stylesheet" href="<?= theme_url() ?>css/content.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/ui.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/form.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/content-addon.css" type="text/css" />
<!--
<link rel="stylesheet" href="<?= theme_url() ?>css/mocha_test/Core.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/mocha_test/Dock.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/mocha_test/Layout.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/mocha_test/Tab.css" type="text/css" />
<link rel="stylesheet" href="<?= theme_url() ?>css/mocha_test/Window.css" type="text/css" />
-->


<!--[if IE 7]><link rel="stylesheet" href="<?= theme_url() ?>css/ui_ie7.css" /><![endif]-->

<!-- External librairies CSS -->
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/datepicker/css/dashboard/datepicker_dashboard.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/SortableTable/SortableTable.css" />
<link type="text/css" rel="stylesheet" href="<?= theme_url() ?>javascript/codemirror/css/codemirror.css" />

<!--[if IE]>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/excanvas_r43_compressed.js"></script>		
<![endif]-->

<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-1.2.4-core-nc.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-1.2.4.4-more-yc.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/drag.clone.js"></script>


<!-- Base URL & languages translations available for javascript -->
<script type="text/javascript">
	
	/** 
	 * Global base_url value.
	 * Used by mocha-init and should be used by any javascript class or method which needs to access to resources
	 */
	var base_url = '<?= base_url() ?>';
	var theme_url = '<?= theme_url() ?>';
	var admin_url = '<?= base_url().Settings::get_lang('current') ?>/<?=config_item('admin_url')?>/';

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

<!--
	mocha.js.php is for development. It is not recommended for production.
	For production it is recommended that you used a compressed version of either
	the output from mocha.js.php or mocha.js. You could also list the
	necessary source files individually here in the header though that will
	create a lot more http requests than a single concatenated file.
		

<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Core/Core.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Layout/Layout.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Layout/Dock.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Window/Window.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/Window/Modal.js"></script>
-->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/mocha.js"></script>


<!-- Ionize scripts -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/ionize.js"></script>

<!-- External librairies -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/swfobject.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/datepicker/datepicker.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/SortableTable/SortableTable.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/codemirror/js/codemirror.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/codemirror/codemirror.views.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/TabSwapper.js"></script>

<!-- Text editor -->
<?php if( Settings::get('texteditor') == '' || Settings::get('texteditor') == 'tinymce' ) :?>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/tinymce/jscripts/tiny_mce/tiny_mce_src.js"></script>
<!--	<script type="text/javascript" src="<?= theme_url() ?>javascript/tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>-->
<?php elseif( Settings::get('texteditor') == 'ckeditor' ) :?>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/ckeditor/ckeditor.js"></script>
<?php endif ;?>

<!-- FileManager & ImageManager -->
<?php if( Settings::get('filemanager') == 'filemanager' ) :?>

	<script type="text/javascript" src="<?= theme_url() ?>javascript/tinymce/jscripts/tiny_mce/plugins/imagemanager/js/mcimagemanager.js"></script>
	<script type="text/javascript" src="<?= theme_url() ?>javascript/tinymce/jscripts/tiny_mce/plugins/filemanager/js/mcfilemanager.js"></script>

<?php elseif( Settings::get('filemanager') == 'mootools-filemanager' ) :?>

	<!-- Mootools Filemanager -->
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




	
	<script type="text/javascript">	
	
		// filemanager must be set
		var filemanager = '';
	</script>

<?php endif ;?>

<script type="text/javascript">

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
		pictureArray:Array('<?= str_replace(',', "','", Settings::get('media_type_picture')) ?>'),
		musicArray:Array('<?= str_replace(',', "','", Settings::get('media_type_music')) ?>'),
		videoArray:Array('<?= str_replace(',', "','", Settings::get('media_type_video')) ?>'),
		fileArray:Array('<?= str_replace(',', "','", Settings::get('media_type_file')) ?>')
	});

</script>


<!-- Text editor initialization -->
<!--
	TODO: Decide whether to load tinyMCE or CKEditor depending on the settings.
	However, notice that in this case you have to somehow automatically load the resources
	and configuration after the text editor is changed in the settings menu
 -->

<!-- tinyMCE -->
<?php if( Settings::get('texteditor') == '' || Settings::get('texteditor') == 'tinymce' ) :?>
	
	<?php if( Settings::get('filemanager') == 'mootools-filemanager' ) :?>

		<script type="text/javascript" src="<?= theme_url() ?>javascript/mootools-filemanager/Source/FileManager.TinyMCE.js"></script>
	
	<?php endif ;?>
	
	<!-- If users templates, add them to the init object -->
	<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/templates/tinymce_templates.js' )) :?>
		<script type="text/javascript" src="<?= base_url() ?>themes/<?= Settings::get('theme') ?>/assets/templates/tinymce_templates.js"></script>
	<?php else :?>
		<script type="text/javascript">
			var getTinyTemplates = false;
		</script>
	<?php endif ;?>

	<script type="text/javascript">

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

		/*
		 * TinyMCE openFilemanager callback
		 *
		 */
		<?php if( Settings::get('filemanager') == 'mootools-filemanager' ) :?>

			function openFilemanager(field, url, type, win)
			{
				var options = false;
				
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
							options = {
								baseURL: '<?= base_url() ;?>',
								url: '<?= admin_url() ;?>media/filemanager',
								assetBasePath: '<?= theme_url() ?>javascript/mootools-filemanager/Assets',
								language: '<?php echo Settings::get_lang() ;?>',
								selectable: true,
								hideOnClick: true,
								'uploadAuthData': responseJSON.tokken,
								'thumbSize': <?= (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120 ;?>,
								indexLevel: 350000		// tinyMCE modals are stored at 300000, Dialogs at 400000
							};
							
							// Close existing instance of fileManager
							var instance = MUI.Windows.instances.get('filemanagerWindow');
							if (instance)
							{
								instance.close();
							}
							
							// Init FM
							filemanager = new FileManager($extend(
							{
								onComplete: function(path)
								{
									if (!win.document) return;
									win.document.getElementById(field).value = '<?= base_url() ;?>' + path;
									if (win.ImageDialog) win.ImageDialog.showPreviewImage('<?= base_url() ;?>' + path, 1);
									
									// CLose filemanager after insert
									filemanager.close();
								}
							}, options));
							
							filemanager.show();
							
							return filemanager;
						}
						else
						{
							MUI.notification('error', Lang.get('ionize_session_expired'));
							return false;
						}
					}
				}).send();
			}

		
		<?php endif ;?>
		
		


		<?php if( Settings::get('filemanager') == 'kcfinder' ) :?>
			function openKCFinder(field_name, url, type, win) {
				//alert("Field_Name: " + field_name + "\nURL: " + url + "\nType: " + type + "\nWin: " + win); // debug/testing
				if (type == 'image')
					var kcurl = '<?= theme_url() ?>javascript/kcfinder/browse.php?type=pictures&noselect=1&lng=<?php Settings::get_lang('current') ?>&opener=custom';
				else
					var kcurl = '<?= theme_url() ?>javascript/kcfinder/browse.php?type=files&noselect=0&lng=<?php Settings::get_lang('current') ?>&opener=custom';
				var xPos = (window.screen.availWidth/2) - (w/2);
				var yPos = 60; 
				var config = 'width=670, height=400, left='+xPos+', top='+yPos+', toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no';
				window.KCFinder = {}; 
				window.KCFinder.win = win;
				window.KCFinder.type = type;
				window.KCFinder.inp = win.document.getElementById(field_name);
				window.KCFinder.callBack = function(kcurl) {
					window.KCFinder.inp.value = kcurl;
					if (typeof(window.KCFinder.win.ImageDialog) != "undefined")
					{
						if (window.KCFinder.win.ImageDialog.getImageData)
							window.KCFinder.win.ImageDialog.getImageData();
						if (window.KCFinder.win.ImageDialog.showPreviewImage)
							window.KCFinder.win.ImageDialog.showPreviewImage(kcurl);
					}
					window.KCFinder = null;
				};  
				var w = window.open(kcurl, 'kcfinder', config);
				w.focus();
				return false;
			}
		<?php endif ;?>

		// If users uses tiny HTML templates, add them to the init object.
		if (getTinyTemplates != false)
		{
//			tinyMCEParam.template_templates = getTinyTemplates('<?= base_url() ?>themes/<?= Settings::get('theme') ?>/assets/templates/');
		}
		
//		tinyMCE.init();

	</script>
<?php elseif( Settings::get('texteditor') == 'ckeditor' ) :?>
	<!-- CKEditor -->
	<script type="text/javascript">
		CKEDITOR.config.skin = 'ionize';
		CKEDITOR.config.width = '99%';
		CKEDITOR.config.height = 250;
		CKEDITOR.config.resize_minWidth = 350;
		CKEDITOR.config.resize_minHeight = 250;
		CKEDITOR.config.language = '<?php Settings::get_lang('current') ?>';
		
		<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/assets/css/ckeditor.css' )) :?>
			CKEDITOR.config.contentsCss = '<?= theme_url() ?>assets/css/ckeditor.css';
		<?php else :?>
			CKEDITOR.config.contentsCss = '<?= theme_url() ?>css/ckeditor.css';
		<?php endif ;?>

		<?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/javascript/ckeditor.js' )) :?>
			CKEDITOR.config.stylesCombo_stylesSet = 'my_styles:<?= theme_url() ?>javascript/ckeditor.js';
		<?php endif ;?>
		
        CKEDITOR.config.toolbar = 
        [
            ['Source','Maximize','ShowBlocks'],
            ['Undo','Redo'],
            ['Cut','Copy','Paste','PasteText','PasteFromWord'],
            ['Link','Unlink'],
            ['Image','Flash','Table','HorizontalRule','SpecialChar'],
            '/',
            ['JustifyLeft','JustifyCenter','JustifyRight'],
            ['Bold','Italic','Underline','Strike'],
            ['NumberedList','BulletedList','-','Outdent','Indent'],
            ['Format',
            <?php if (is_file(FCPATH.'themes/'.Settings::get('theme').'/javascript/ckeditor.js' )) :?>
            'Styles',
            <?php endif ;?>
            'RemoveFormat'],
        ];

		<?php if( Settings::get('filemanager') == 'mootools-filemanager' ) :?>
		
			CKEDITOR.config.filebrowserBrowseUrl = '<?= admin_url()?>media/ckfilemanager/';
			CKEDITOR.config.filebrowserImageWindowWidth = '810';
			CKEDITOR.config.filebrowserImageWindowHeight = '450';

// http://stackoverflow.com/questions/1498628/how-can-you-integrate-a-custom-file-browser-uploader-with-ckeditor		

/*
			function openFilemanager(){
			    var complete = function(path, file){
			      var url = '/res/' + path;
				window.opener.CKEDITOR.tools.callFunction('<?php echo $CKEditorFuncNum ?>', url);
			  window.close();
			    };
			
			    var manager = new FileManager({
			        url: '/app/filemanager/manager.php',
			        assetBasePath: '/app/filemanager/Assets',
			        language: 'pl',
			        uploadAuthData: {session: 'MySessionId'},
			        selectable: true,
			        onComplete: complete
			    });
			
			$('filemanager_open').set('html', manager.show());
			}
			window.addEvent('domready', function(){
			  openFilemanager();
			});
		
		
			CKEDITOR.config.filebrowserBrowseUrl = '<?= admin_url()?>media/filemanager/';
*/


		<?php endif ;?>
		<?php if( Settings::get('filemanager') == 'kcfinder' ) :?>
			CKEDITOR.config.filebrowserBrowseUrl ='<?= theme_url() ?>javascript/kcfinder/browse.php?type=files&lng=<?php Settings::get_lang('current') ?>';
			CKEDITOR.config.filebrowserImageBrowseUrl = '<?= theme_url() ?>javascript/kcfinder/browse.php?type=pictures&noselect=1&lng=<?php Settings::get_lang('current') ?>';
			CKEDITOR.config.filebrowserFlashBrowseUrl ='<?= theme_url() ?>javascript/kcfinder/browse.php?type=files&noselect=1&lng=<?php Settings::get_lang('current') ?>';
			CKEDITOR.config.filebrowserUploadUrl = '<?= theme_url() ?>javascript/kcfinder/upload.php?type=files';
			CKEDITOR.config.filebrowserImageUploadUrl = '<?= theme_url() ?>javascript/kcfinder/upload.php?type=pictures';
			CKEDITOR.config.filebrowserFlashUploadUrl = '<?= theme_url() ?>javascript/kcfinder/upload.php?type=files';
		<?php endif ;?>
        CKEDITOR.config.filebrowserWindowWidth = '670';
        CKEDITOR.config.filebrowserWindowHeight = '400';

	</script>
<?php endif ;?>





<!-- UI initialization -->
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/init-columns.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/init-windows.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/init-menu.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/init-forms.js"></script>
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/source/init-content.js"></script>

<!--
<script type="text/javascript" src="<?= theme_url() ?>javascript/mocha/init-ionize.js"></script>
-->
<script type="text/javascript">

	// Initialize MUI when the DOM is ready
	window.addEvent('load', function(){ //using load instead of domready for IE8
	
		MUI.myChain = new Chain();
		MUI.myChain.chain(
			function(){MUI.Desktop.initialize();},
			function(){MUI.Dock.initialize();},
			function(){initializeContent();},	
			function(){initializeForms();},	
			function(){initializeMenu();},		
			function(){initializeWindows();},
			function(){initializeColumns();}	
		).callChain();	
	});

</script>


<script type="text/javascript">
	/** 
	 * Calendars
	 *
	 */
	var datePicker = new DatePicker('.date', {pickerClass: 'datepicker_dashboard', timePicker:true, format: 'd.m.Y H:i:s', inputOutputFormat:'d.m.Y H:i:s', allowEmpty:true, useFadeInOut:false, positionOffset: {x:-10,y:0}});

</script>

</head>
<body>

<div id="desktop">

	<div id="desktopHeader" class="hide">
		<div id="desktopBar">
			<div id="desktopTitlebarWrapper">
				<div id="desktopTitlebar">
					
					<h1 class="applicationTitle">ionize <?php echo($this->config->item('version')) ;?></h1>
					<a id="logoAnchor"></a>
					
					<div id="topNav">
						<ul class="menu-right">
							<li><?= lang('ionize_logged_as') ?> : <?= $current_user['screen_name'] ?></li>
							<li><a href="<?= base_url() ?>" target="_blank"><?= lang('ionize_website') ?></a></li>
							<li><a href="<?= admin_url() ?>user/logout"><?= lang('ionize_logout') ?></a></li>
							<li>
								<?php foreach(Settings::get('displayed_admin_languages') as $lang) :?>
									<a href="<?= base_url().$lang ?>/<?= config_item('admin_url')?>"><img src="<?= theme_url() ?>images/world_flags/flag_<?= $lang ?>.gif" alt="<?= $lang ?>" /></a>
								<?php endforeach ;?>
							</li>
						</ul>
					</div>
				</div><!-- /desktopTitlebar -->
			</div>


			<div id="desktopNavbar">
				<ul>
					<li><a id="dashboardLink">Dashboard</a></li>
					<li><a class="returnFalse" href=""><?= lang('ionize_menu_content') ?></a>	
						<ul>
							<?php if($this->connect->is('super-admins')) :?>
								<li><a id="menuLink" href=""><?=lang('ionize_menu_menu')?></a></li>
							<?php endif ;?>
							<li><a id="newPageLink" href="<?= admin_url() . 'page/create/0' ?>"><?= lang('ionize_menu_page') ?></a></li>
							<li><a id="articlesLink" href="<?= admin_url() . 'article/list_articles' ?>"><?= lang('ionize_menu_articles') ?></a></li>
							<li><a id="translationLink" href=""><?= lang('ionize_menu_translation') ?></a></li>
							<li class="divider"><a id="mediaManagerLink" href=""><?= lang('ionize_menu_media_manager') ?></a></li>
							<?php if ($this->connect->is('super-admins')) :?>
								<li class="divider"><a id="elementsLink" href=""><?= lang('ionize_menu_content_elements') ?></a></li>
							<?php endif ;?>
							<?php if ($this->connect->is('super-admins') ) :?>
								<li><a id="extendfieldsLink" href=""><?= lang('ionize_menu_extend_fields') ?></a></li>
							<?php endif ;?>
						</ul>
					</li>
					<?php if($this->connect->is('editors')) :?>
					<li><a class="returnFalse" href=""><?= lang('ionize_menu_modules') ?></a>
						<ul>
							<!-- Module Admin controllers links -->
							<?php foreach($modules as $uri => $module) :?>
								<?php if($this->connect->is($module['access_group'])) :?>
									<li><a class="modules" id="<?= $uri ?>ModuleLink" href="<?= admin_url() ?>module/<?= $uri ?>/<?= $uri ?>/index"><?= $module['name'] ?></a></li>
								<?php endif ;?>								
							<?php endforeach ;?>
							<?php if($this->connect->is('admins')) :?>
								<li class="divider"><a id="modulesLink" href=""><?=lang('ionize_menu_modules_admin')?></a></li>
							<?php endif ;?>
						</ul>
					</li>
					<?php endif ;?>
					<li><a class="returnFalse" href=""><?= lang('ionize_menu_tools') ?></a>
						<ul>
							<li><a id="googleAnalyticsLink" href="https://www.google.com/analytics/reporting/login" target="_blank">Google Analytics</a></li>
						</ul>
					</li>

					<li><a class="returnFalse" href=""><?=lang('ionize_menu_settings')?></a>
						<ul>
							<li><a id="ionizeSettingLink" href=""><?=lang('ionize_menu_ionize_settings')?></a></li>
							<li><a id="languagesLink" href=""><?=lang('ionize_menu_languages')?></a></li>
							<?php if($this->connect->is('admins')) :?>
								<li><a id="usersLink" href=""><?=lang('ionize_menu_users')?></a></li>
							<?php endif ;?>
							<?php if($this->connect->is('super-admins')) :?>
								<li><a id="themesLink"><?=lang('ionize_menu_theme')?></a></li>
							<?php endif ;?>
							<li class="divider"><a id="settingLink" href=""><?=lang('ionize_menu_site_settings')?></a></li>
							<?php if($this->connect->is('super-admins')) :?>
								<li><a id="technicalSettingLink" href=""><?=lang('ionize_menu_technical_settings')?></a></li>
							<?php endif ;?>
						</ul>
					</li>
					<li><a class="returnFalse" href=""><?= lang('ionize_menu_help') ?></a>
						<ul>
							<?php if (is_dir(realpath(APPPATH.'../user-guide'))) :?>
								<li><a id="docLink" href="../user-guide/index.html" target="_blank"><?= lang('ionize_menu_documentation') ?></a></li>								
							<?php endif; ?>
							<li<?php if (is_dir(realpath(APPPATH.'../user-guide'))) :?> class="divider"<?php endif; ?>><a id="aboutLink" href="<?= theme_url() ?>views/about.html"><?= lang('ionize_menu_about') ?></a></li>
						</ul>
					</li>
				</ul>	
				<div class="toolbox">
					<div id="spinnerWrapper"><div id="spinner"></div></div>		
				</div>

				
			</div><!-- /desktopNavbar -->

		</div>



	</div><!-- /desktopHeader -->

	<div id="dockWrapper">
		<div id="dock">
			<div id="dockPlacement"></div>
			<div id="dockAutoHide"></div>
			<div id="dockSort"><div id="dockClear" class="clear"></div></div>
		</div>
	</div>

	<!-- Mocha page content -->
	<div id="pageWrapper"></div>



</div><!-- /desktop -->


<script type="text/javascript">

	/*
	 * Add modules links events
	 */
	$$('.modules').each(function(item, idx) 
	{
		item.addEvent('click', function(e)
		{
			var e = new Event(e).stop();
			
			MUI.updateContent({
				element: $('mainPanel'),
				title: item.get('text'),
				url : item.getProperty('href')
			});
		});
	});


</script>


</body>


</html>

