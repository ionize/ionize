<link rel="stylesheet" href="<ion:base_url />modules/Fancyupload/javascript/fancyupload/photoqueue/style.css" />

<script type="text/javascript" src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/Swiff.Uploader.js"></script>
<script type="text/javascript" src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/Fx.ProgressBar.js"></script>
<script type="text/javascript" src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/FancyUpload2.js"></script>

<form action="<ion:base_url /><ion:uri />/upload" method="post" enctype="multipart/form-data" id="upload-form">

	<input type="hidden" name="usrn" id="fancyupload-usrn" value="<ion:userdata item="username" />" />
	<input type="hidden" name="usre" id="fancyupload-usre" value="<ion:userdata item="email" />" />

	<fieldset id="fancyupload-fallback">
		<legend>File Upload</legend>
		<p>
			This form is just an example fallback for the unobtrusive behaviour of FancyUpload.
			If this part is not changed, something must be wrong with your code.
		</p>
		<label for="fancyupload-photoupload">
			Upload a Photo:
			<input type="file" name="Filedata" />
		</label>
	</fieldset>
	
	
	
	<div id="fancyupload-status" class="hide">
		<p>
			<a href="#" id="fancyupload-browse"><ion:translation term="module_fancyupload_browse" /></a> |
			<a href="#" id="fancyupload-clear"><ion:translation term="module_fancyupload_clear_list" /></a> |
			<a href="#" id="fancyupload-upload"><ion:translation term="module_fancyupload_start_upload" /></a>
		</p>
		<div>
			<strong class="overall-title"></strong><br />
			<img src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/assets/progress-bar/bar.gif" class="progress overall-progress" />
		</div>
		<div>
			<strong class="current-title"></strong><br />
			<img src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/assets/progress-bar/bar.gif" class="progress current-progress" />
		</div>
		<div class="current-text"></div>
	</div>

	<ul id="fancyupload-list"></ul>

</form>

<script type="text/javascript">
window.addEvent('domready', function() { // wait for the content

	// our uploader instance 
	
	var up = new FancyUpload2($('fancyupload-status'), $('fancyupload-list'),
	{
		verbose: false,
		
		url: $('upload-form').action,
		
		path: '<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/Swiff.Uploader.swf',
		
		typeFilter: {
			'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png',
			'Videos (*.flv, *.fv4, *.mpg, *.mpeg)': '*.flv; *.fv4; *.mpg; *.mpeg'
		},
		
		// browse button, *target* is overlayed with the Flash movie
		target: 'fancyupload-browse',
		
		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('fancyupload-status').removeClass('hide'); // we show the actual UI
			$('fancyupload-fallback').destroy(); // ... and hide the plain form
			
			// We relay the interactions with the overlayed flash to the link
			this.target.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});

			// Interactions for the 2 other buttons
			$('fancyupload-clear').addEvent('click', function() {
				up.remove(); // remove all files
				return false;
			});

			$('fancyupload-upload').addEvent('click', function() {
				up.start(); // start upload
				return false;
			});
		},
		
		// Edit the following lines, it is your custom event handling
		
		/**
		 * Called when files were not added, "files" is an array of invalid File classes.
		 * 
		 * This example creates a list of error elements directly in the file list, which
		 * hide on click.
		 */ 
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('FancyUpload', 'removeTitle'),
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).inject(this.list, 'top');
			}, this);
		},
		
		onBeforeStart: function() {
			up.setOptions({
				data: post_var=$('upload-form').toQueryString() + '&' + 'usrn=<ion:userdata item="username" url_encode="true" />' + '&' + 'usre=<ion:userdata item="email" url_encode="true" />'
			});
		},

		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
		 */
		onFileSuccess: function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});
			
			if (json.get('status') == '1') {
				file.element.addClass('file-success');
				file.info.set('html', '<strong><ion:translation term="module_fancyupload_file_uploaded" /></strong> ');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<strong><ion:translation term="module_fancyupload_error" /></strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
			}
		},
		
		/**
		 * onFail is called when the Flash movie got bashed by some browser plugin
		 * like Adblock or Flashblock.
		 */
		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
					break;
				case 'empty': // Oh oh, wrong path
					alert('A required file was not found, please be patient and we fix this.');
					break;
				case 'flash': // no flash 9+ :(
					alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
			}
		}
		
	});
	
});
</script>


