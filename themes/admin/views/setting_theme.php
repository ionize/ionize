
<div id="sidecolumn" class="close">
	
	<!- Informations -->
	<div class="info">

		<dl class="small compact">
			<dt><label><?=lang('ionize_label_current_theme')?></label></dt>
			<dd><?= Settings::get('theme');?></dd>
		</dl>

	</div>


	<div id="options">
		
		<!-- Themes -->
		<h3 class="toggler"><?=lang('ionize_title_themes')?></h3>

		<div class="element">

			<form name="themesForm" id="themesForm" method="post" action="<?= admin_url() ?>setting/save_themes">

				<!-- Theme -->
				<dl class="small">
					<dt>
						<label for="theme"><?=lang('ionize_label_theme')?></label>
					</dt>
					<dd>
						<select class="select" name="theme">
							<?php foreach($themes as $theme) :?>
								<option value="<?= $theme ?>" <?php if($theme == Settings::get('theme') ) :?>selected="selected"<?php endif; ?>><?= $theme ?></option>
							<?php endforeach ;?>
						</select>
					</dd>
				</dl>

				<!-- Theme Admin -->
				<dl class="small">
					<dt>
						<label for="theme_admin"><?=lang('ionize_label_theme_admin')?></label>
					</dt>
					<dd>
						<select class="select" name="theme_admin">
							<?php foreach($themes_admin as $theme) :?>
								<option value="<?= $theme ?>" <?php if($theme == Settings::get('theme_admin') ) :?>selected="selected"<?php endif; ?>><?= $theme ?></option>
							<?php endforeach ;?>
						</select>
					</dd>
				</dl>

				<!-- Submit button  -->
				<dl class="small">
					<dt>&#160;</dt>
					<dd>
						<input id="themesFormSubmit" type="submit" class="submit" value="<?= lang('ionize_button_save_themes') ?>" />
					</dd>
				</dl>
				<br/>

			</form>

		</div> <!-- /element -->
				
			
	</div> <!-- /togglers -->

</div> <!-- /sidecolumn -->

	

<!-- Main Column -->
<div id="maincolumn">

		
	<h2 class="main themes" id="main-title"><?= lang('ionize_title_themes') ?></h2>

	<!-- Views list -->
	<h3 class="mt20"><?=lang('ionize_title_views_list')?> : <?= Settings::get('theme') ?></h3>

	<!--<div class="element">-->

		<form name="viewsForm" id="viewsForm" method="post" action="<?= admin_url() ?>setting/save_views">

		<div id="viewsTableContainer">

			<!-- Views table list -->
			<table class="list" id="viewsTable">

				<thead>
					<tr>
						<th axis="string" style="width:20px;"></th>
						<th axis="string"><?= lang('ionize_label_view_filename') ?></th>
						<th axis="string"><?= lang('ionize_label_view_folder') ?></th>
						<th axis="string"><?= lang('ionize_label_view_name') ?></th>
						<th axis="string"><?= lang('ionize_label_view_type') ?></th>				
					</tr>
				</thead>

				<tbody>
				
				<?php foreach($files as $file) :?>
					
					<?php
						$rel = $file->path . $file->name;
					?>
					
					<tr>
						<td><a class="icon edit viewEdit" rel="<?= $rel ?>"></a></td>
						<td><a class="viewEdit" rel="<?= $rel ?>"><?= $file->name ?></a></td>
						<td><?= $file->path ?> </td>
						<td>
							<input type="text" class="inputtext w160" name="viewdefinition_<?= $rel ?>" value="<?= $file->definition ?>" />
						</td>	
						<td>
							<select class="select" name="viewtype_<?= $rel ?>">
								<option value=""><?= lang('ionize_select_no_type') ?></option>
								<option <?php if($file->type == 'page') :?> selected="selected" <?php endif ;?> value="page">Page</option>
								<option <?php if($file->type == 'article') :?> selected="selected" <?php endif ;?> value="article">Article</option>
							</select>
						</td>
					</tr>

				<?php endforeach ;?>
				
				</tbody>

			</table>

		</div>

	</form>
	
	<br />
	<br />
	
	<!--</div>-->
	

</div> <!-- /maincolumn -->


<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('setting_theme_toolbox');


	/**
	 * Options Accordion
	 *
	 */
	ION.initAccordion('.toggler', 'div.element');


	/**
	 * Views Edit links
	 *
	 */
	$$('.viewEdit').each(function(item)
	{
		var rel = item.getProperty('rel');
		
		var id = rel.replace(/\//gi, '');

		var form = 'formView' + id;

		item.addEvent('click', function(e)
		{
			e.stop();
			
			var self = this;
			
			this.resizeCodeMirror = function(w)
			{
				var mfw = w.getElement('.CodeMirror-wrapping');
				mfw.setStyle('height', w.getSize().y - 130);
			}
			
			var wOptions = 
			{
				id: 'w' + id,
				title: Lang.get('ionize_title_view_edit') + ' : ' + rel,
				loadMethod: 'xhr',
				contentURL: admin_url + 'setting/edit_view/' + rel,
				y: 80,
				width:800, 
				height:450,
				padding: { top: 12, right: 12, bottom: 10, left: 12 },
				maximizable: true,
				contentBgColor: '#fff',		
				
				onContentLoaded: function(w)
				{
					// CodeMirror settings
					var c = $('editview_' + id).value;

					var mirrorFrame = new ViewCodeMirror(CodeMirror.replace($('editview_' + id)), 
					{
						height: "360px",
						width: "95%",
						content: c,
						tabMode: 'shift',
						parserfile: ['parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js', 'tokenizephp.js', 'parsephp.js', 'parsephphtmlmixed.js'],
						stylesheet: [
							'<?= theme_url() ?>javascript/codemirror/css/basic.css',
							'<?= theme_url() ?>javascript/codemirror/css/xmlcolors.css',
							'<?= theme_url() ?>javascript/codemirror/css/jscolors.css',
							'<?= theme_url() ?>javascript/codemirror/css/csscolors.css',
							'<?= theme_url() ?>javascript/codemirror/css/phpcolors.css'
						],
						path: '<?= theme_url() ?>javascript/codemirror/js/',
						lineNumbers: true
					});
					
					// Set height of CodeMirror
					self.resizeCodeMirror(w);

					var form = 'formView' + id;

					// Get the form action URL and adds 'true' so the transport is set to XHR
					var formUrl = $(form).getProperty('action');
	
					// Add the cancel event if cancel button exists
					if (bCancel = $('bCancel' + id))
					{
						bCancel.addEvent('click', function(e)
						{
							var e = new Event(e).stop();
							ION.closeWindow($('w' + id));
						});
					}
					
					// Event on save button
					if (bSave = $('bSave' + id))
					{
						bSave.addEvent('click', function(e)
						{
							e.stop();
							
							// Get the CodeMirror Code
							$('contentview_' + id).value = mirrorFrame.mirror.getCode();
							
							// Get the form
							var options = ION.getFormObject(formUrl, $(form));
							
							var r = new Request.JSON(options);
							
							r.send();
						});
					}
				},
				onResize: function(w) { self.resizeCodeMirror(w); },
				onMaximize: function(w) { self.resizeCodeMirror(w);	},
				onRestore: function(w) { self.resizeCodeMirror(w);	}
			};
			
			// Window creation
			new MUI.Window(wOptions);
		});
	});


	/**
	 * Database form action
	 * see ionize-form.js for more information about this method
	 */
	ION.setFormSubmit('themesForm', 'themesFormSubmit', 'setting/save_themes');

</script>