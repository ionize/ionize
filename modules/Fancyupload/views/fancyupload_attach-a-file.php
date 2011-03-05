<link rel="stylesheet" href="<ion:base_url />modules/Fancyupload/javascript/fancyupload/attach-a-file/style.css" />

<script type="text/javascript" src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/Swiff.Uploader.js"></script>
<script type="text/javascript" src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/Fx.ProgressBar.js"></script>
<script type="text/javascript" src="<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/FancyUpload3.Attach.js"></script>


<div id="fancyupload-box">

	<a href="#" id="fancyupload-attach"><ion:translation term="module_fancyupload_label_attach_file" /></a>

	<ul id="fancyupload-list"></ul>

	<a href="#" id="fancyupload-attach-2" style="display: none;"><ion:translation term="module_fancyupload_label_attach_file2" /></a>		
</div>



<script type="text/javascript">
//<![CDATA[

/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

window.addEvent('domready', function() {

	/**
	 * Uploader instance
	 */
	var up = new FancyUpload3.Attach('fancyupload-list', '#fancyupload-attach, #fancyupload-attach-2', {
		path: '<ion:base_url />modules/Fancyupload/javascript/fancyupload/source/Swiff.Uploader.swf',
		url: '<ion:base_url /><ion:uri />/upload',
		fileSizeMax: <ion:post_max_size />,
		
		verbose: false,
		
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'file-invalid',
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).adopt(
					new Element('span', {html: file.validationErrorMessage || file.validationError})
				).inject(this.list, 'bottom');
			}, this);	
		},
		
		onFileSuccess: function(file) {
			new Element('input', {type: 'checkbox', 'checked': true}).inject(file.ui.element, 'top');
			file.ui.element.highlight('#e6efc2');
		},
		
		onFileError: function(file) {
			file.ui.cancel.set('html', 'Retry').removeEvents().addEvent('click', function() {
				file.requeue();
				return false;
			});
			
			new Element('span', {
				html: file.errorMessage,
				'class': 'file-error'
			}).inject(file.ui.cancel, 'after');
		},
		
		onFileRequeue: function(file) {
			file.ui.element.getElement('.file-error').destroy();
			
			file.ui.cancel.set('html', 'Cancel').removeEvents().addEvent('click', function() {
				file.remove();
				return false;
			});
			
			this.start();
		},
		
		onBeforeStart: function() {
			up.setOptions({
				data: post_var= '?' + 'usrn=<ion:userdata item="username" url_encode="true" />' + '&' + 'usre=<ion:userdata item="email" url_encode="true" />'
			});
		}
	});
});

//]]>
</script>


