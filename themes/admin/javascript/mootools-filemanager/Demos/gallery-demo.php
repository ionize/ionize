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


			/* Gallery Example */
			var global = this;
			var example4 = $('myGallery');
			var gallery_json = {
				"/rant_yellow.gif":"",
				"/towers 46p 1v/00005.jpg":"texan hat #0005",
				"/items with issues/20091103114923_untitled-8.jpg":"mosquito on blue",
				"/items with issues/atomic-bomb-explosion [DesktopNexus.com].jpg":"",
				"/items with issues/en porte-jarretelles - nukes EXIF dump in backend.jpg":"",
				"/items with issues/Konachan_com_67642_long_hair_ribbons_suzumiya_haruhi_suzumiya_ha_1.png":"",
				"/items with issues/manager-9.php.jpg":"",
				"/items with issues/fixme_gap_yuu_plasm.png":""
			};
			var gallery_json_metadata = {};
			var imgs_root_dir = null;

			example4.set('value', JSON.encode(gallery_json));

			/*
			 * Oh, BY THE WAY:
			 *
			 * Since we need to calc margins an' all anyway to center the thumbnails vertically (at least for vertical centering you need such a thing),
			 * we can go real fancy and rescale the images (thumbnails) shown: simply set a different 'thumb_side_length' value!
			 */
			var thumb_side_length = 100;

			var manager4 = new FileManager.Gallery({
				url: 'selectImage.php?exhibit=A', // 'manager.php', but with a bogus query parameter included: latest FM can cope with such an URI
				assetBasePath: '../Assets',
				filter: 'image',
				hideOnClick: true,
				// uploadAuthData is deprecated; use propagateData instead. The session cookie(s) are passed through Flash automatically, these days...
				propagateData: {
					origin: 'demo-Gallery'
				},
				upload: true,
				download: true,
				destroy: true,
				rename: true,
				move_or_copy: true,
				createFolders: true,
				// selectable: false,
				hideQonDelete: false,     // DO ask 'are you sure' when the user hits the 'delete' button
				verbose: true,            // log a lot of activity to console (when it exists)
				onShow: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('GALLERY.onShow: ', mgr);
					var obj;
					Function.attempt(function(){
						var gallist = example4.get('value');
						if (typeof console !== 'undefined' && console.log) console.log('GALLERY list: ', gallist);
						obj = JSON.decode(gallist);
					});
					this.populate(obj, false);          // as we have the data in 'clean vanilla' form in a JSON object, we do NOT want the (default) URL decode process to be performed: no %20 --> ' ', etc. transform!
				},
				onComplete: function(serialized, files, legal_root_dir, mgr){
					if (typeof console !== 'undefined' && console.log) console.log('GALLERY.onComplete: ', serialized, ', files metadata: ', files, ', legal root: ', legal_root_dir, ', mgr: ', mgr);

					example4.set('value', JSON.encode(serialized));

					gallery_json_metadata = files;
					imgs_root_dir = legal_root_dir;

					// To show how to use the metadata and the serialized data, we render a series of thumbnails
					// in this page and when you click on those, milkbox will kick in showing them as a gallery.
					var container_el = $('gallery-tn-container');
					if (container_el)
					{
						container_el.empty();

						Object.each(serialized, function(caption, key)
						{
							var metadata = files[key];

							// make sure the full path starts with a '/' (legal_root_dir does NOT!); also normalize out the trailing/leading slashes in both path section strings
							var full_path = mgr.normalize('/' + legal_root_dir + key);    // eqv. to: normalize('/' + legal_root_dir + metadata.path) as key === metadata.path

							if (typeof console !== 'undefined' && console.log) console.log('GALLERY.print loop: ', full_path, ', metadata: ', metadata);

							var input2html = function(str)
							{
								return (''+str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
							};

							var tnimg = metadata.thumb250;
							var iw = metadata.thumb250_width;
							var ih = metadata.thumb250_height;
							// if thumbnail is not available (can happen for quite a few file types! And for overlarge images too! Plus for other errors!) pcik the icon48 instead:
							if (!tnimg)
							{
								tnimg = metadata.icon48;
								iw = 48;
								ih = 48;
							}
							var ratio;
							if (iw > thumb_side_length)
							{
								ratio = thumb_side_length / iw;
								iw = thumb_side_length;
								ih *= ratio;
							}
							if (ih > thumb_side_length)
							{
								ratio = thumb_side_length / ih;
								ih = thumb_side_length;
								iw *= ratio;
							}
							iw = Math.round(iw);
							ih = Math.round(ih);

							// as HTML/CSS is notoriously bad at centering vertically, we employ the handy image w/h meta info to enforce vertical centered images by tweaking their margin:
							var mt = Math.round((thumb_side_length - ih) / 2);
							var mb = thumb_side_length - ih - mt;
							// CSS can take care of the horizontal centering at ease...

							var el = new Element('div').adopt(
								new Element('a', {
									href: mgr.escapeRFC3986(full_path),
									title: input2html(caption),             // encode as HTML, suitable for attribute values
									'data-milkbox': 'gall1',
									'data-milkbox-size': 'width: ' + metadata.width + ', height: ' + metadata.height,
									styles: {
										width: thumb_side_length,
										height: thumb_side_length
									}
								}).adopt(
									new Element('img', {
										src: tnimg,
										alt: '',
										styles: {
											width: iw,
											height: ih,
											'margin-top': mt,
											'margin-bottom': mb
										}
									})
								));

							// and remember the 'key' so we can travel from <div> back to metadata in the slider onChange handler further below:
							el.store('key', key);

							el.inject(container_el);
						});

						// now that we have the HTML generated, kick milkbox into (re)scanning:
						if (milkbox)
						{
							milkbox.reloadPageGalleries();
						}
					}
				},
				onModify: function(file, json, mode, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onModify: ', mode, file, json, mgr);
				},
				onHide: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onHide: ', mgr);
				},
				onScroll: function(e, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onScroll: ', e, mgr);
				},
				onPreview: function(src, mgr, el) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onPreview: ', src, el, mgr);
				},
				onDetails: function(json, mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onDetails: ', json, mgr);
				},
				onHidePreview: function(mgr) {
					if (typeof console !== 'undefined' && console.log) console.log('MFM.onHidePreview: ', mgr);
				}
			});
			$('example4').addEvent('click', manager4.show.bind(manager4));



			var slider = $('slider');

			new Slider(slider, slider.getElement('.knob'), {
				range: [20, 250.1],         // '250' doesn't deliver 250 but 249   :-(
				initialStep: thumb_side_length,
				steps: 300 - 16,
				onChange: function(value)
				{
					value = Math.round(value);
					$('setThumbSize').set('text', value);

					thumb_side_length = value;

					// adjust the thumbs:
					var container_el = $('gallery-tn-container');
					if (container_el)
					{
						var thumbs = container_el.getChildren('div');

						thumbs.each(function(el)
						{
							var key = el.retrieve('key');

							var metadata = gallery_json_metadata[key];

							var tnimg = metadata.thumb250;
							var iw = metadata.thumb250_width;
							var ih = metadata.thumb250_height;
							// if thumbnail is not available (can happen for quite a few file types! And for overlarge images too! Plus for other errors!) pcik the icon48 instead:
							if (!tnimg)
							{
								tnimg = metadata.icon48;
								iw = 48;
								ih = 48;
							}
							var ratio;
							if (iw > thumb_side_length)
							{
								ratio = thumb_side_length / iw;
								iw = thumb_side_length;
								ih *= ratio;
							}
							if (ih > thumb_side_length)
							{
								ratio = thumb_side_length / ih;
								ih = thumb_side_length;
								iw *= ratio;
							}
							iw = Math.round(iw);
							ih = Math.round(ih);

							// as HTML/CSS is notoriously bad at centering vertically, we employ the handy image w/h meta info to enforce vertical centered images by tweaking their margin:
							var mt = Math.round((thumb_side_length - ih) / 2);
							var mb = thumb_side_length - ih - mt;
							// CSS can take care of the horizontal centering at ease...

							var a = el.getChildren('a')[0];
							a.setStyles({
											width: thumb_side_length,
											height: thumb_side_length
										});
							var img = a.getChildren('img')[0];
							img.setStyles({
											width: iw,
											height: ih,
											'margin-top': mt,
											'margin-bottom': mb
										  });
						});
					}
				}
			});

			// and set the inital value
			$('setThumbSize').set('text', thumb_side_length);
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
		<button id="example4">Create a Gallery</button>
		<input name="BrowseExample4" type="text" id="myGallery" value="Gallery output will be stored in here" style="width: 550px;" />

		<p>When you've selected a series of images, the thumbnails of those will be rendered below. Click on any of the thumbnails to see the images appear in a milkbox gallery show.</p>

		<div id="slider" class="slider">
			<div class="knob"></div>
		</div>
		<p>Drag the slider above to change the thumbnails' display size.</p>

		<p>Thumbnail display dimension: <span id="setThumbSize"></span>px</p>

		<div id="gallery-tn-container">
		</div>
	</div>

	<div style="clear: both;"></div>

</div>
</body>
</html>