<?php

/**
 * Modal window for element field creation / edition
 *
 */
    log_message('error', 'View File Loaded : element_field.php');

$id = $id_extend_field;

?>

<form name="elementfieldForm<?php echo $id; ?>" id="elementfieldForm<?php echo $id; ?>" action="<?php echo admin_url(); ?>element_field/save">

	<!-- Hidden fields -->
	<input id="id_element_definition" name="id_element_definition" type="hidden" value="<?php echo $id_element_definition; ?>" />
	<input id="id_extend_field" name="id_extend_field" type="hidden" value="<?php echo $id; ?>" />
	<input id="parent" name="parent" type="hidden" value="" />
	<input id="global" name="global" type="hidden" value="0" />
	<input id="ordering" name="ordering" type="hidden" value="<?php echo $ordering; ?>" />


	<!-- Contexte -->
	<div class="summary">
		<dl class="small">
			<dt class="lite">
				<label for="name"><?php echo lang('ionize_label_content_element'); ?></label>
			</dt>
			<dd><?php echo $element['name']; ?></dd>
			
		</dl>
		<dl class="small">
			<dt class="lite"><label><?php echo lang('ionize_label_label'); ?></label></dt>
			<dd>
	
				<!-- Tabs -->
				<div id="elementFieldTab<?php echo $id; ?>" class="mainTabs transparent mt0 mb5">
					
					<ul class="tab-menu">
						
						<?php foreach(Settings::get_languages() as $language) :?>
						
							<li class="tab_element<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['name']); ?></a></li>
						
						<?php endforeach ;?>
			
					</ul>
					<div class="clear"></div>
				
				</div>
				
				<div id="elementFieldTabContent<?php echo $id; ?>">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						
						<?php $lang = $language['lang']; ?>
			
						<div class="tabcontent <?php echo $lang; ?>">
					
							<!-- Label -->
							<input id="label_<?php echo $lang; ?><?php echo $id; ?>" name="label_<?php echo $lang; ?>" class="inputtext title" type="text" value="<?php echo ${$lang}['label']; ?>"/>
			
						</div>
					<?php endforeach ;?>
				</div>
			
			</dd>
		</dl>
	
	</div>


	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name<?php echo $id; ?>" title="<?php echo lang('ionize_help_ef_name'); ?>"><?php echo lang('ionize_label_name'); ?></label>
		</dt>
		<dd>
			<input id="name<?php echo $id; ?>" name="name" class="inputtext required" type="text" value="<?php echo $name; ?>" />
		</dd>
		
	</dl>
	

	<!-- Type -->
	<dl class="small">
		<dt>
			<label for="type<?php echo $id; ?>"><?php echo lang('ionize_label_extend_field_type'); ?></label>
		</dt>
		<dd>
			<select name="type" id="type<?php echo $id; ?>" class="select">
				<option value="1" <?php if ($type=='1' OR $type=='') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_text'); ?></option>
				<option value="2" <?php if ($type=='2') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_textarea'); ?></option>
				<option value="3" <?php if ($type=='3') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_editor'); ?></option>
				<option value="4" <?php if ($type=='4') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_checkbox'); ?></option>
				<option value="5" <?php if ($type=='5') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_radio'); ?></option>
				<option value="6" <?php if ($type=='6') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_select'); ?></option>
				<option value="7" <?php if ($type=='7') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_type_datetime'); ?></option>
			</select>
			
		</dd>
		
	</dl>
	
	<!-- Traduisible -->
	<dl id="translate_block<?php echo $id; ?>" class="small">
		<dt>
			<label for="translated<?php echo $id; ?>"><?php echo lang('ionize_label_extend_field_translated'); ?></label>
		</dt>
		<dd>
			<input id="translated<?php echo $id; ?>" name="translated" class="inputcheckbox" type="checkbox" value="1" <?php if ($translated=='1') :?> checked="checked" <?php endif ;?> />
		</dd>
	</dl>

	<!-- Values : For select, radio, checkboxes -->
	<dl id="value_block<?php echo $id; ?>" class="small">
		<dt>
			<label for="value<?php echo $id; ?>" title="<?php echo lang('ionize_help_ef_values'); ?>"><?php echo lang('ionize_label_values'); ?></label>
		</dt>
		<dd>
			<textarea id="value<?php echo $id; ?>" name="value" class="inputtext w200 h40" type="text"><?php echo $value; ?></textarea>
		</dd>
	</dl>
	
	<!-- default_value -->
	<dl id="default_value_block<?php echo $id; ?>" class="small">
		<dt>
			<label for="default_value<?php echo $id; ?>" title="<?php echo lang('ionize_help_ef_default_value'); ?>"><?php echo lang('ionize_label_default_value'); ?></label>
		</dt>
		<dd>
			<textarea id="default_value<?php echo $id; ?>" name="default_value" class="inputtext w200 h40" type="text"><?php echo $default_value; ?></textarea>
		</dd>
	</dl>

	<!-- description -->
	<dl class="small">
		<dt>
			<label for="description<?php echo $id; ?>" title="<?php echo lang('ionize_help_ef_description'); ?>"><?php echo lang('ionize_label_description'); ?></label>
		</dt>
		<dd>
			<textarea id="description<?php echo $id; ?>" name="description" class="inputtext w200 h40" type="text"><?php echo $description; ?></textarea>
		</dd>
	</dl>


</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSaveelementfield<?php echo $id; ?>" type="button" class="button yes right mr40"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelelementfield<?php echo $id; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#elementfieldForm<?php echo $id; ?>');

	var windowEl = $('welementfield<?php echo $id; ?>');
	var contentEl = $('welementfield<?php echo $id; ?>_content');

	function display_value_block()
	{
		if ($('type<?php echo $id; ?>').value == '7')
		{
			$('value_block<?php echo $id; ?>').setStyle('display', 'none');
			$('default_value_block<?php echo $id; ?>').setStyle('display', 'none');
			$('translate_block<?php echo $id; ?>').setStyle('display', 'none');
		}
		else if ($('type<?php echo $id; ?>').value < 4)
		{
			$('value_block<?php echo $id; ?>').setStyle('display', 'none');
			
			if ($('default_value_block<?php echo $id; ?>').getStyle('display') == 'none')
			{
				$('default_value_block<?php echo $id; ?>').setStyle('display', 'block').highlight();
				$('translate_block<?php echo $id; ?>').setStyle('display', 'block').highlight();
			}
		}
		else
		{
			if ($('value_block<?php echo $id; ?>').getStyle('display') == 'none')
			{
				$('value_block<?php echo $id; ?>').setStyle('display', 'block').highlight();
				$('default_value_block<?php echo $id; ?>').setStyle('display', 'block').highlight();
				$('translate_block<?php echo $id; ?>').setStyle('display', 'block').highlight();
			}
		}
		ION.windowResize('elementfield<?php echo $id; ?>', {'width':410});
	}
	
	$('type<?php echo $id; ?>').addEvent('change', function()
	{
		display_value_block();
	});
	display_value_block();
	
	
	// Auto generates the name of the field
	ION.initCorrectUrl('label<?php echo $id; ?>', 'name<?php echo $id; ?>');

	/** 
	 * Lang tabs
	 */
	var elementFieldTab<?php echo $id; ?> = new TabSwapper({tabsContainer: 'elementFieldTab<?php echo $id; ?>', sectionsContainer: 'elementFieldTabContent<?php echo $id; ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent'});

</script>

