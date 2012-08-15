
<?php
	
/**
 * Used to add an element to a container (page , article) on the Editor side
 * Called by element_definition/get_element_detail() when the user click on one element definition
 * When saving, creates an element instance through : element/save
 *
 */

	/*
	 * These values are empty when adding a new element
	 */
	$id_element = ( !empty($id_element)) ? $id_element : '';
	$parent = ( !empty($parent)) ? $parent : '';
	$id_parent = ( !empty($id_parent)) ? $id_parent : '';

?>

<?php if ($id_element == '') :?>
	<a id="elementAddBackButton" class="light button back">
		<i class="icon-back"></i><?= lang('ionize_label_back_to_element_list') ?>
	</a>
<?php endif ;?>

<div class="mt10">

	<dl class="small">
		<dt></dt>
		<dd><h2><?= $element_definition['title'] ?></h2></dd>
	</dl>


<form name="elementForm" id="elementForm<?= $id_element ?>" method="post">

	<input type="hidden" id="elementParent<?= $id_element ?>" name="parent" value="<?= $parent ?>" />
	<input type="hidden" id="elementIdParent<?= $id_element ?>" name="id_parent" value="<?= $id_parent ?>" />
	<input type="hidden" id="id_element<?= $id_element ?>" name="id_element" value="<?= $id_element ?>" />
	<input type="hidden" id="id_element_definition<?= $id_element ?>" name="id_element_definition" value="<?= $element_definition['id_element_definition'] ?>" />

	<!-- Ordering : First or last (or Element one if exists ) -->
	<?php if( empty($id_element)) :?>	
	<dl class="small mb10">
		<dt >
			<label for="ordering"><?= lang('ionize_label_ordering') ?></label>
		</dt>
		<dd>
			<select name="ordering" id="ordering<?= $id_element ?>" class="select">
				<?php if( ! empty($id_element)) :?>
					<option value="<?= $ordering ?>"><?= $ordering ?></option>
				<?php endif ;?>
				<option value="first"><?= lang('ionize_label_ordering_first') ?></option>
				<option value="last"><?= lang('ionize_label_ordering_last') ?></option>
			</select>
		</dd>
	</dl>
	<?php endif ;?>


<?php foreach($fields as $field) :?>

	<?php
	
		$id = $field['id_extend_field'];
	
	?>

	<?php if ($field['translated'] != '1') :?>
	
		<dl class="small">
			<dt>
				<?php
					$label = ( ! empty($field['langs'][Settings::get_lang('default')]['label'])) ? $field['langs'][Settings::get_lang('default')]['label'] : $field['name'];
				?>
				<label for="cf_<?= $id ?>" title="<?= $field['description'] ?>"><?= $label ?></label>
			</dt>
			<dd>
				<?php
					$field['content'] = (!empty($field['content'])) ? $field['content'] : $field['default_value'];
				?>
			
				<?php if ($field['type'] == '1') :?>
					<input id="cf_<?= $id ?>" class="inputtext w300 clear" type="text" name="cf_<?= $id ?>" value="<?= $field['content']  ?>" />
				<?php endif ;?>
				
				<?php if ($field['type'] == '2' OR $field['type'] == '3') :?>
					<textarea id="cf_<?= $id ?>" class="<?php if($field['type'] == '3'):?> tinyTextarea <?php endif ;?> inputtext h80" name="cf_<?= $id ?>"><?= $field['content'] ?></textarea>
				<?php endif ;?>

				<!-- Checkbox -->
				<?php if ($field['type'] == '4') :?>
					
					<?php
						$pos = 		explode("\n", $field['value']);
						$saved = 	explode(',', $field['content']);
					?>
					<?php
						$i = 0; 
						foreach($pos as $values)
						{
							$vl = explode(':', $values);
							$key = $vl[0];
							$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

							?>
								<input type="checkbox" id= "cf_<?= $id.$i ?>" name="cf_<?= $id ?>[]" value="<?= $key ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?= $id . $i ?>"><?= $value ?></label></input><br/>
							<?php
							$i++;
						}
					?>
				<?php endif ;?>
				
				<!-- Radio -->
				<?php if ($field['type'] == '5') :?>
					
					<?php
						$pos = explode("\n", $field['value']);
					?>
					<?php
						$i = 0; 
						foreach($pos as $values)
						{
							$vl = explode(':', $values);
							$key = $vl[0];
							$value = (!empty($vl[1])) ? $vl[1] : $vl[0];

							?>
								<input type="radio" id= "cf_<?= $id.$i ?>" name="cf_<?= $id ?>" value="<?= $key ?>" <?php if ($field['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?= $id . $i ?>"><?= $value ?></label></input><br/>
							<?php
							$i++;
						}
					?>
				<?php endif ;?>
				
				<!-- Selectbox -->
				<?php if ($field['type'] == '6' && !empty($field['value'])) :?>
					
					<?php									
						$pos = explode("\n", $field['value']);
						$saved = 	explode(',', $field['content']);
					?>
					<select name="cf_<?= $id?>">
					<?php
						$i = 0; 
						foreach($pos as $values)
						{
							$vl = explode(':', $values);
							$key = $vl[0];
							$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
							?>
							<option value="<?= $key ?>" <?php if (in_array($key, $saved)) :?> selected="selected" <?php endif ;?>><?= $value ?></option>
							<?php
							$i++;
						}
					?>
					</select>
				<?php endif ;?>

				<!-- Date & Time -->
				<?php if ($field['type'] == '7') :?>
				
					<input id="cf_<?= $id ?>" class="inputtext w120 date" type="text" name="cf_<?= $id ?>" value="<?= $field['content']  ?>" data-item="element" data-id="<?= $id ?>" />
					
				<?php endif ;?>
				
			</dd>
		</dl>	
			
	<?php endif ;?>
<?php endforeach ;?>


<?php if( ! empty($lang_fields)) :?>

	<!-- Tabs -->
	<div id="elementTab<?= $UNIQ ?>" class="mainTabs">
		<ul class="tab-menu">
		
			<?php foreach(Settings::get_languages() as $language) :?>
		
				<li class="tab-element<?= $UNIQ ?> <?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?= $language['lang'] ?>"><a><span><?= ucfirst($language['name']) ?></span></a></li>
		
			<?php endforeach ;?>
		
		</ul>
		<div class="clear"></div>
	</div>
	
	<div id="elementTabContent<?= $UNIQ ?>">
	
	<!-- Text block -->
	<?php foreach(Settings::get_languages() as $language) :?>

		<?php $lang = $language['lang']; ?>
		
		<div class="tabcontent<?= $UNIQ ?>">

			<p class="clear h15">
				<a class="right icon copy copyLang" rel="<?= $lang ?>" title="<?= lang('ionize_label_copy_to_other_languages') ?>"></a>
			</p>


			<?php foreach($lang_fields as $field) :?>
			
				<?php
				
					$id = $field['id_extend_field'];
				
				?>
	
				<dl class="small">
					<dt>
						<?php
							$label = ( ! empty($field['langs'][$lang]['label'])) ? $field['langs'][$lang]['label'] : $field['name'];
						?>
						<label for="cf_<?= $id ?>_<?= $lang ?>" title="<?= $field['description'] ?>"><?= $label ?></label>
					</dt>
					<dd>
						<?php
							$field[$lang]['content'] = (!empty($field[$lang]['content'])) ? $field[$lang]['content'] : $field['default_value'];
						?>
	
						<?php if ($field['type'] == '1') :?>
							<input id="cf_<?= $id ?>_<?= $lang ?>" class="inputtext" type="text" name="cf_<?= $id ?>_<?= $lang ?>" value="<?= $field[$lang]['content'] ?>" />
						<?php endif ;?>
						
						<?php if ($field['type'] == '2' || $field['type'] == '3') :?>
							<textarea id="cf_<?= $id ?>_<?= $lang ?>" class="inputtext h80 <?php if($field['type'] == '3'):?> tinyTextarea <?php endif ;?>" name="cf_<?= $id ?>_<?= $lang ?>" rel="<?= $lang ?>"><?= $field[$lang]['content'] ?></textarea>
						<?php endif ;?>
	
						<!-- Checkbox -->
						<?php if ($field['type'] == '4') :?>
							
							<?php
								$pos = 		explode("\n", $field['value']);
								$saved = 	explode(',', $field[$lang]['content']);
							?>
	
							<?php
								$i = 0; 
								foreach($pos as $values)
								{
									$vl = explode(':', $values);
									$key = $vl[0];
									$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
	
									?>
									<input type="checkbox" id= "cf_<?= $id.$i ?>_<?= $lang ?>" name="cf_<?= $id ?>_<?= $lang ?>[]" value="<?= $key ?>" <?php if (in_array($key, $saved)) :?>checked="checked" <?php endif ;?>><label for="cf_<?= $id . $i ?>_<?= $lang ?>"><?= $value ?></label></input><br/>
									<?php
									$i++;
								}
							?>
						<?php endif ;?>
						
						<!-- Radio -->
						<?php if ($field['type'] == '5') :?>
							
							<?php
								$pos = explode("\n", $field['value']);
							?>
							<?php
								$i = 0; 
								foreach($pos as $values)
								{
									$vl = explode(':', $values);
									$key = $vl[0];
									$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
	
									?>
									<input type="radio" id= "cf_<?= $id.$i ?>_<?= $lang ?>" name="cf_<?= $id ?>_<?= $lang ?>" value="<?= $key ?>" <?php if ($field[$lang]['content'] == $key) :?> checked="checked" <?php endif ;?>><label for="cf_<?= $id . $i ?>_<?= $lang ?>"><?= $value ?></label></input><br/>
									<?php
									$i++;
								}
							?>
						<?php endif ;?>
						
						<!-- Selectbox -->
						<?php if ($field['type'] == '6' && !empty($field['value'])) :?>
							
							<?php									
								$pos = explode("\n", $field['value']);
								$saved = 	explode(',', $field[$lang]['content']);
							?>
							<select name="cf_<?= $id?>_<?= $lang ?>">
							<?php
								$i = 0; 
								foreach($pos as $values)
								{
									$vl = explode(':', $values);
									$key = $vl[0];
									$value = (!empty($vl[1])) ? $vl[1] : $vl[0];
									?>
									<option value="<?= $key ?>" <?php if (in_array($key, $saved)) :?> selected="selected" <?php endif ;?>><?= $value ?></option>
									<?php
									$i++;
								}
							?>
							</select>
						<?php endif ;?>
	
					</dd>
				</dl>
		
			<?php endforeach ;?>
			
		</div>

	<?php endforeach ;?>
	
	</div>
	
<?php endif ;?>

</form>

</div>

<div class="buttons">
	<button id="saveElementFormSubmit<?= $id_element ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_element') ?></button>
</div>


<script type="text/javascript">

/** 
 * Back button
 *
 */
if ($('elementAddBackButton'))
{
	$('elementAddBackButton').addEvent('click', function(el)
	{
		ION.HTML('element_definition/get_element_list', {'parent': '<?= $parent?>', 'id_parent': '<?= $id_parent?>'}, {'update': 'elementAddContainer' });
	});
}


/**
 * Save button
 *
 */
$('saveElementFormSubmit<?= $id_element ?>').addEvent('click', function(e)
{
	e.stop();
	
	// Show spinner
	MUI.showSpinner();
	
	// New Element : Add current opened parent / parent_id to the form
	if ($('element') && $('id_element<?= $id_element ?>').value == '')
	{
		var parent = $('element').value;
		var id_parent = $('id_' + parent).value;
		
		if (parent && id_parent)
		{
			$('elementParent<?= $id_element ?>').value = parent;
			$('elementIdParent<?= $id_element ?>').value = id_parent;
		}
	}
	
	if ($('elementParent<?= $id_element ?>').value !='' && $('elementIdParent<?= $id_element ?>').value != '')
	{
		// tinyMCE and CKEditor trigerSave
		// mandatory for text save. See how to externalize without make it too complex.
		if (typeof tinyMCE != "undefined")
			tinyMCE.triggerSave();
		if (typeof CKEDITOR != "undefined")
		{
			for (instance in CKEDITOR.instances)
				CKEDITOR.instances[instance].updateElement();
		}
		
		// Get the form
		var options = ION.getJSONRequestOptions('element/save', $('elementForm<?= $id_element ?>'), {'onSuccess': function(){ION.closeWindow($('wcontentElement<?= $id_element ?>'))}});
		
		var r = new Request.JSON(options);
		
		r.send();
	}
	else
	{
		ION.notification('error', Lang.get('ionize_message_element_cannot_be_added_to_parent'));
	}
});



/** 
 * Lang tabs
 *
 */
new TabSwapper({tabsContainer: 'elementTab<?= $UNIQ ?>', sectionsContainer: 'elementTabContent<?= $UNIQ ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent<?= $UNIQ ?>' });


/**
 * Copy Lang data to other languages dynamically
 *
 */
var elements = Array();
<?php foreach($lang_fields as $field) :?>
	<?php if ($field['type'] == '1' || $field['type'] == '2' || $field['type'] == '3') :?>
		elements.push('cf_<?= $field['id_extend_field'] ?>');
	<?php endif ;?>
<?php endforeach ;?>
ION.initCopyLang('.copyLang', elements);



/**
 * Text editor
 *
 */
ION.initTinyEditors('.tab-element<?= $UNIQ ?>', '#elementTabContent<?= $UNIQ ?> .tinyTextarea', 'small', {height:120});


/**
 * Calendars init
 *
 */
ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');


/** 
 * Resize
 *
 */
ION.windowResize('contentElement<?= $id_element ?>', {'width':500});



</script>
