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

$_SESSION['UploadAuth'] = 'yes';

$params = session_get_cookie_params();

/* the remainder of the code does not need access to the session data. */
session_write_close();

if (0)
{
	// and add a couple other, slightly malicious cookies to check whether Flash will crash on it, or not.
	setcookie("ASP.NET_SessionId", 'ASP.NET: b0rk b0rk b0rk & ... b0rk!', time() + 600,
		$params['path'], $params['domain'],
		$params['secure'], $params['httponly']
	);
	setcookie('.1!#$%20X', 'b0rk b0rk b0rk & ... b0rk!', time() + 600,
		$params['path'], $params['domain'],
		$params['secure'], $params['httponly']
	);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>MooTools FileManager Testground</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="demos.css" type="text/css" />

	<script type="text/javascript" src="mootools-core.js"></script>
	<script type="text/javascript" src="mootools-more.js"></script>

	<script type="text/javascript" src="../Source/FileManager.js"></script>
	<script type="text/javascript" src="../Source/Gallery.js"></script>
	<script type="text/javascript" src="../Source/Uploader/Fx.ProgressBar.js"></script>
	<script type="text/javascript" src="../Source/Uploader/Swiff.Uploader.js"></script>
	<script type="text/javascript" src="../Source/Uploader.js"></script>
	<script type="text/javascript" src="../Language/Language.en.js"></script>
	<script type="text/javascript" src="../Language/Language.de.js"></script>
	<script type="text/javascript" src="dev_support.js"></script>

	<!-- extra, for viewing the gallery and selected picture: -->
	<script type="text/javascript" src="../Assets/js/milkbox/milkbox.js"></script>

	<script type="text/javascript">
		window.addEvent('domready', function() {

			//
			if (0)
			{
				// override mootools global default setting for fade effects:
				Fx.prototype.options.fps = 10;
				//Fx.prototype.options.unit = false;
				Fx.prototype.options.duration = 5;
				//Fx.prototype.options.frames = 1000;
				//Fx.prototype.options.frameSkip = true;
				//Fx.prototype.options.link = 'ignore';
				//Fx.prototype.frameInterval;
				Fx.Durations['short'] = 5;
				Fx.Durations['normal'] = 5;
				Fx.Durations['long'] = 5;
			}


			/* Simple Example */
			var manager1 = new FileManager({
				url: 'manager.php',
				language: 'en',
				hideOnClick: true,
				assetBasePath: '../Assets',
				// uploadAuthData is deprecated; use propagateData instead. The session cookie(s) are passed through Flash automatically, these days...
				uploadAuthData: {
					session: 'MySessionData'
				},
				upload: true,
				download: true,
				destroy: true,
				rename: true,
				move_or_copy: true,
				createFolders: true,
				// selectable: true,
				hideQonDelete: false,     // DO ask 'are you sure' when the user hits the 'delete' button
				onComplete: function(path, file, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onComplete: ', path, ', file: ', file, ', mgr: ', mgr);
				},
				onModify: function(file, json, mode, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onModify: ', mode, ', file: ', file, ', json: ', json, ', mgr: ', mgr);
				},
				onShow: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onShow: ', mgr);
				},
				onHide: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onHide: ', mgr);
				},
				onScroll: function(e, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onScroll: ', e, ', mgr: ', mgr);
				},
				onPreview: function(src, mgr, el) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onPreview: ', src, ', el: ', el, ', mgr: ', mgr);
				},
				onDetails: function(json, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onDetails: ', json, ', mgr: ', mgr);
				},
				onHidePreview: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onHidePreview: ', mgr);
				},
				// and a couple of extra user defined parameters sent with EVERY request:
				propagateData: {
					origin: 'demo-FM-1'
				}
			});
			$('example1').addEvent('click', manager1.show.bind(manager1));

			/* Select a file */
			var el = $('example2');
			var div, manager2;
			var complete = function(path, file, mgr) {
				el.set('value', path);
				if (div) {
					div.destroy();
				}
				var icon = new Asset.image(
					mgr.assetBasePath+'Images/cancel.png',
					{
						'class': 'file-cancel',
						title: 'deselect'
					}).addEvent('click', function(e){
						e.stop();
						el.set('value', '');
						var self = this;
						div.fade(0).get('tween').chain(function(){
							div.destroy();
							mgr.tips.hide();
							mgr.tips.detach(self);
						});
					});
				mgr.tips.attach(icon);

				var img = null;
				var mimetype = file.mime;
				if (mimetype && mimetype.contains('image/'))
				{
					img = new Element('div', {
							'text': 'Click on the thumbnail to view the image/file in a lightbox (milkbox)'
						}).adopt(
							new Element('br'),
							new Element('a', {
									'data-milkbox': 'single',
									'title': file.name,
									'href': path  // no need to URLencode the path as FM does it already: encodeURI(path)  // see also:  http://www.javascripter.net/faq/escape.htm
								}).adopt(new Element('img', {
									'src': (file.thumb250 ? file.thumb250 : file.icon),
									'class': 'preview',
									'alt': 'preview (picked)'
								}))
						);
				}

				div = new Element('div', {'class': 'selected-file', text: 'Selected file: '}).adopt(
					new Asset.image(file.icon, {'class': 'mime-icon'}),
					new Element('span', {text: file.name}),
					icon,
					img
				).inject(el, 'after');

				if (img && typeof milkbox != 'undefined')
				{
					milkbox.reloadPageGalleries();
				}
			};

			manager2 = new FileManager({
				url: 'selectImage.php',
				language: 'en',
				filter: 'image',
				hideOnClick: true,
				assetBasePath: '../Assets',
				// uploadAuthData is deprecated; use propagateData instead. The session cookie(s) are passed through Flash automatically, these days...
				uploadAuthData: {
					session: 'MySessionData'
				},
				selectable: true,
				upload: true,
				destroy: true,
				rename: true,
				move_or_copy: true,
				createFolders: true,
				onComplete: complete,
				// and a couple of extra user defined parameters sent with EVERY request:
				propagateData: {
					origin: 'demo-selectFile'
				}
			});

			el.setStyle('display', 'none');
			var val = el.get('value');
			if (val) {
				var file_ext = val.split('.').getLast();
				complete.apply(manager2, [val, {
					name: val.split('/').getLast(),
					mime: 'image/' + file_ext,
					icon: '../Assets/Images/Icons/'+file_ext+'.png'
				}, manager2]);
			}

			new Element('button', {'class': 'browser', text: 'Select an image'}).addEvent('click', manager2.show.bind(manager2)).inject(el, 'before');

			/* Localized Example */
			var manager3 = new FileManager({
				url: 'manager.php',
				language: 'de',
				hideOnClick: true,
				assetBasePath: '../Assets',
				// uploadAuthData is deprecated; use propagateData instead. The session cookie(s) are passed through Flash automatically, these days...
				uploadAuthData: {
					session: 'MySessionData'
				},
				upload: true,
				destroy: true,
				rename: true,
				move_or_copy: true,
				createFolders: true,
				// and a couple of extra user defined parameters sent with EVERY request:
				propagateData: {
					origin: 'demo-clickedLink'
				}
			});
			$('example3').addEvent('click', manager3.show.bind(manager3));


			/* Gallery Example */
			var global = this;
			var example4 = $('myGallery');
			var gallery_json = {
				"/rant_yellow.gif":"",
				"/towers 46p 1v/00005.jpg":""
			};
			
			example4.set('value', JSON.encode(gallery_json));

			var manager4 = new FileManager.Gallery({
				url: 'selectImage.php?exhibit=A', // 'manager.php', but with a bogus query parameter included: latest FM can cope with such an URI
				assetBasePath: '../Assets',
				filter: 'image',
				hideOnClick: true,
				// uploadAuthData is deprecated; use propagateData instead. The session cookie(s) are passed through Flash automatically, these days...
				uploadAuthData: {
					session: 'MySessionData'
				},
				propagateData: {
					origin: 'demo-Gallery'
				},
				onShow: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('GALLERY.onShow: ', mgr);
					var obj;
					Function.attempt(function(){
						var gallist = example4.get('value');
						if (typeof console !== 'undefined' && console.log) console.log('GALLERY list: ', gallist);
						obj = JSON.decode(gallist);
					});
					this.populate(obj);
				},
				onComplete: function(serialized, files, legal_root_dir, mgr){
					if (typeof console !== 'undefined' && console.log) console.log('GALLERY.onComplete: ' + serialized, ', files: ', files, ', legal root: ', legal_root_dir, ', mgr: ', mgr);

					example4.set('value', JSON.encode(serialized));
				}
			});
			$('example4').addEvent('click', manager4.show.bind(manager4));
		});
	</script>
</head>
<body>
<div id="content" class="content">
	<div class="go_home">
		<a href="index.php" title="Go to the Demo index page"><img src="home_16x16.png"> </a>
	</div>

	<h1>FileManager Demo</h1>
	<div class="example">
		<button id="example1" class="BrowseExample">Open File-Manager</button>
	</div>
	<div class="example">
		<input name="BrowseExample2" type="text" id="example2" value="Smile.gif" />
	</div>
	<div class="example">
		<a href="#" id="example3" class="BrowseExample">Open File-Manager from a link (German)</a>
	</div>

	<div class="example">
		<a href="tinymce.php">Open File-Manager from TinyMCE (editor) - separate test page</a>
	</div>

	<div class="example">
		<button id="example4">Create a Gallery</button>
		<input name="BrowseExample4" type="text" id="myGallery" value="Gallery output will be stored in here" style="width: 550px;" />
	</div>

	<div style="clear: both;"></div>

</div>
</body>
</html>