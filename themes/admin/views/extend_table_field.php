<?php

/**
 * Modal window for extend field creation / edition
 *
 */
?>

<form name="extendtableForm" id="extendtableForm" action="<?= admin_url() ?>extend_table/save_extend">

	<!-- Hidden fields -->
	<input id="table" name="table" type="hidden" value="<?= $table ?>" />


	<!-- Contexte -->
	<dl class="small">
		<dt>
			<label for="name"><?=lang('ionize_label_table')?></label>
		</dt>
		<dd><?= $table ?></dd>
		
	</dl>


	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name"><?=lang('ionize_label_name')?></label>
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
				<option value="VARCHAR" <?php if ($type=='VARCHAR' OR $type=='') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_text')?></option>
				<option value="TEXT" <?php if ($type=='TEXT') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_textarea')?></option>
				<option value="INT" <?php if ($type=='INT') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_checkbox')?></option>
				<option value="DATETIME" <?php if ($type=='DATETIME') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_type_datetime')?></option>
			</select>
			
		</dd>
	</dl>
	
	<!-- CONSTRAINT : Length (for INT and VARCHAR) -->
	<dl id="constraint_block" class="small">
		<dt>
			<label for="constraint"><?= lang('ionize_label_field_length') ?></label>
		</dt>
		<dd>
			<input type="text"t id="constraint" name="constraint" class="inputtext w60 required" value="<?= $constraint ?>" />
		</dd>
	</dl>


	<!-- NULL -->
	<dl class="small">
		<dt>
			<label for="null"><?= lang('ionize_label_field_null') ?></label>
		</dt>
		<dd>
			<input type="checkbox" id="null" name="unsigned" class="inputcheckbox" value="1" <?php if ($unsigned != '') :?>checked="checked"<?php endif ;?>/>
		</dd>
	</dl>

	<!-- UNSIGNED : for INT -->
	<dl id="unsigned_block" class="small">
		<dt>
			<label for="unsigned"><?= lang('ionize_label_field_unsigned') ?></label>
		</dt>
		<dd>
			<input type="checkbox" id="unsigned" name="unsigned" class="inputcheckbox" value="1"  <?php if ($unsigned != '') :?>checked="checked"<?php endif ;?> />
		</dd>
	</dl>

	<!-- AUTO_INCREMENT : for INT -->
	<dl id="auto_increment_block" class="small">
		<dt>
			<label for="auto_increment"><?= lang('ionize_label_field_auto_increment') ?></label>
		</dt>
		<dd>
			<input type="checkbox" id="auto_increment" name="auto_increment" class="inputcheckbox" value="1" <?php if ($auto_increment != '') :?>checked="checked"<?php endif ;?> />
		</dd>
	</dl>

	
	<!-- default_value -->
	<dl id="default_value_block" class="small">
		<dt>
			<label for="default"><?= lang('ionize_label_default_value') ?></label>
		</dt>
		<dd>
			<textarea id="default" name="default" class="inputtext w200 h40"><?= $default ?></textarea>
		</dd>
	</dl>

	<!-- description -->
	<dl class="small">
		<dt>
			<label for="description"><?= lang('ionize_label_description') ?></label>
		</dt>
		<dd>
			<textarea id="description" name="description" class="inputtext w200 h40"><?= $description ?></textarea>
		</dd>
	</dl>


</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSaveextendtable" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelextendtable"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#extendtableForm');

	function display_value_block()
	{
		$('unsigned_block').setStyle('display', 'none');
		$('auto_increment_block').setStyle('display', 'none');
		$('constraint_block').setStyle('display', 'none');
	
		if ($('type').value == 'VARCHAR' || $('type').value == 'INT')
		{
			$('constraint_block').setStyle('display', 'block');
		}
		
		if ($('type').value == 'INT')
		{
			$('unsigned_block').setStyle('display', 'block');
			$('auto_increment_block').setStyle('display', 'block');
		}
	}
	
	$('type').addEvent('change', function()
	{
		display_value_block();
	});
	display_value_block();
	
	// Auto generates the name of the field
//	ION.initCorrectUrl('label', 'name');


</script>

