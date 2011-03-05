<?php

/**
 * Modal window for extend field creation / edition
 *
 */
?>

<form name="extendfieldForm" id="extendfieldForm" action="<?= admin_url() ?>extend_field/save">

	<!-- Hidden fields -->
	<input id="id_extend_field" name="id_extend_field" type="hidden" value="<?= $id_extend_field ?>" />
	<input id="parent" name="parent" type="hidden" value="<?= $parent ?>" />
	<input id="id_parent" name="id_parent" type="hidden" value="<?= $id_parent ?>" />
	<input id="ordering" name="ordering" type="hidden" value="<?= $ordering ?>" />


	<!-- Contexte -->
	<dl class="small">
		<dt>
			<label for="name"><?=lang('ionize_label_extend_field_context')?></label>
		</dt>
		<dd><?= ucfirst($parent) ?></dd>
		
	</dl>

	<!-- Label -->
	<dl class="small">
		<dt>
			<label for="label" title="<?=lang('ionize_help_label_label')?>"><?=lang('ionize_label_label')?></label>
		</dt>
		<dd>
			<input id="label" name="label" class="inputtext required" type="text" value="<?= $label ?>" />
		</dd>
		
	</dl>

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name" title="<?=lang('ionize_help_ef_name') ?>"><?=lang('ionize_label_name')?></label>
		</dt>
		<dd>
			<input id="name" name="name" class="inputtext required" type="text" value="<?= $name ?>" />
		</dd>
		
	</dl>
	

	<!-- Type -->
	<dl class="small">
		<dt>
			<label for="label"><?=lang('ionize_label_extend_field_type')?></label>
		</dt>
		<dd>
			<select name="type" id="type" class="select">
				<option value="1" <?php if ($type=='1' OR $type=='') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_text')?></option>
				<option value="2" <?php if ($type=='2') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_textarea')?></option>
				<option value="3" <?php if ($type=='3') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_editor')?></option>
				<option value="4" <?php if ($type=='4') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_checkbox')?></option>
				<option value="5" <?php if ($type=='5') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_radio')?></option>
				<option value="6" <?php if ($type=='6') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_select')?></option>
				<option value="7" <?php if ($type=='7') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_datetime')?></option>
			</select>
			
		</dd>
		
	</dl>
	
	<!-- Traduisible -->
	<dl id="translate_block" class="small">
		<dt>
			<label for="translated"><?=lang('ionize_label_extend_field_translated')?></label>
		</dt>
		<dd>
			<input id="translated" name="translated" class="inputcheckbox" type="checkbox" value="1" <?php if ($translated=='1') :?> checked="checked" <?php endif ;?> />
		</dd>
	</dl>

	<!-- Values : For select, radio, checkboxes -->
	<dl id="value_block" class="small">
		<dt>
			<label for="value" title="<?=lang('ionize_help_ef_values') ?>"><?= lang('ionize_label_values') ?></label>
		</dt>
		<dd>
			<textarea id="value" name="value" class="inputtext w200 h40" type="text"><?= $value ?></textarea>
		</dd>
	</dl>
	
	<!-- default_value -->
	<dl id="default_value_block" class="small">
		<dt>
			<label for="default_value" title="<?=lang('ionize_help_ef_default_value') ?>"><?= lang('ionize_label_default_value') ?></label>
		</dt>
		<dd>
			<textarea id="default_value" name="default_value" class="inputtext w200 h40" type="text"><?= $default_value ?></textarea>
		</dd>
	</dl>

	<!-- description -->
	<dl class="small">
		<dt>
			<label for="description" title="<?=lang('ionize_help_ef_description') ?>"><?= lang('ionize_label_description') ?></label>
		</dt>
		<dd>
			<textarea id="description" name="description" class="inputtext w200 h40" type="text"><?= $description ?></textarea>
		</dd>
	</dl>


</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through MUI.formWindow()
--> 
<div class="buttons">
	<button id="bSaveextendfield" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelextendfield"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	/**
	 * Init help tips on label
	 *
	 */
	MUI.initLabelHelpLinks('#extendfieldForm');


	function display_value_block()
	{
		if ($('type').value == '7')
		{
			$('value_block').setStyle('display', 'none');
			$('default_value_block').setStyle('display', 'none');
			$('translate_block').setStyle('display', 'none');
		}
		else if ($('type').value < 4)
		{
			$('value_block').setStyle('display', 'none');
			
			if ($('default_value_block').getStyle('display') == 'none')
			{
				$('default_value_block').setStyle('display', 'block').highlight();
				$('translate_block').setStyle('display', 'block').highlight();
			}
		}
		else
		{
			if ($('value_block').getStyle('display') == 'none')
			{
				$('value_block').setStyle('display', 'block').highlight();
				$('default_value_block').setStyle('display', 'block').highlight();
				$('translate_block').setStyle('display', 'block').highlight();
			}
		}
		
	}
	
	$('type').addEvent('change', function()
	{
		display_value_block();
	});
	display_value_block();
	
	
	// Auto generates the name of the field
	ION.initCorrectUrl('label', 'name');


</script>

