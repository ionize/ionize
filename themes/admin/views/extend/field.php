<?php
/**
 * Modal window for extend field creation / edition
 *
 * @receives :
 * 		- Extend definitions fields in case of existing extend definition edition
 * 		- $parents : List of potential extend parents
 *
 */

?>

<?php if (Authority::can('delete', 'admin/extend') && $id_extend_field != '') :?>
	<a id="bDeleteextendfield<?php echo $id_extend_field; ?>" class="button red right" ><?php echo lang('ionize_button_delete'); ?></a>
<?php endif ;?>

<h2 id="mainTitleExtend<?php echo $id_extend_field ?>" class="main extends">
	<?php echo lang('ionize_title_extend_field') ?>
</h2>

<div class="main subtitle">
	<p>

		<?php if ($id_extend_field) :?>
			<span class="lite"><?php echo lang('ionize_label_id') ?> : </span>
			<?php echo $id_extend_field; ?>
		<?php endif ;?>
		<?php if ($id_extend_field != '' && $limit_to_parent) :?>
			<span class="lite"> | </span>
		<?php endif ;?>
		<?php if ($limit_to_parent) :?>
			<span class="lite"><?php echo lang('ionize_label_extend_field_parent') ?> : </span>
			<?php echo ucfirst($limit_to_parent) ?>
			<?php if ($id_parent) :?>
				<span class="lite"> | <?php echo lang('ionize_label_parent_id') ?> : </span>
				<?php echo $id_parent ?>
			<?php endif ;?>
		<?php endif ;?>
		<?php if ( ! empty($context)) :?>
			<span class="lite"> | <?php echo ucfirst($context) ?></span>
			<?php if ( ! empty($id_context)) :?>
				<span class="lite"> : </span>
				<?php echo $id_context ?>
			<?php endif ;?>
		<?php endif ;?>
	</p>
</div>


<form name="extendfieldForm" id="extendfieldForm<?php echo $id_extend_field; ?>" action="<?php echo admin_url(); ?>extend_field/save">

	<!-- Hidden fields -->
	<input id="id_extend_field" name="id_extend_field" type="hidden" value="<?php echo $id_extend_field; ?>" />
	<input id="ordering" name="ordering" type="hidden" value="<?php echo $ordering; ?>" />
	<input type="hidden" name="id_parent" value="<?php echo $id_parent ?>" />
	<?php if ( ! empty($context)) :?>
		<input type="hidden" name="context" value="<?php echo $context ?>" />
	<?php endif ;?>
	<?php if ( ! empty($id_context)) :?>
		<input type="hidden" name="id_context" value="<?php echo $id_context ?>" />
	<?php endif ;?>


	<!-- Parent -->
	<?php if ($limit_to_parent) :?>
		<input type="hidden" name="parent" value="<?php echo $limit_to_parent ?>" />
	<?php else :?>
		<dl class="small">
			<dt>
				<label for="parent<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_parent'); ?>"><?php echo lang('ionize_label_extend_field_parent'); ?></label>
			</dt>
			<dd>
				<select id="parent<?php echo $id_extend_field; ?>" name="parent" class="select">
					<?php foreach ($parents as $_parent) :?>
						<option value="<?php echo $_parent ?>" <?php if ($parent==$_parent) :?> selected="selected" <?php endif ;?>><?php echo ucfirst($_parent); ?></option>
					<?php endforeach; ?>
				</select>
			</dd>
		</dl>
	<?php endif ;?>

	<!-- Label -->
	<dl class="small">
		<dt class="mt10"><label><?php echo lang('ionize_label_label'); ?></label></dt>
		<dd>

			<!-- Tabs -->
			<div id="extendFieldTab<?php echo $id_extend_field; ?>" class="mainTabs small">

				<ul class="tab-menu">

					<?php foreach(Settings::get_languages() as $language) :?>

						<li class="tab_extend<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['name']); ?></a></li>

					<?php endforeach ;?>

				</ul>
				<div class="clear"></div>

			</div>

			<div id="extendFieldTabContent<?php echo $id_extend_field; ?>">

				<?php foreach(Settings::get_languages() as $language) :?>

					<?php $lang = $language['lang']; ?>

					<div class="tabcontent <?php echo $lang; ?>">

						<!-- Label -->
						<input id="label_<?php echo $lang; ?><?php echo $id_extend_field; ?>" name="label_<?php echo $lang; ?>" class="inputtext title w96p" type="text" value="<?php echo $languages[$lang]['label']; ?>"/>

					</div>
				<?php endforeach ;?>
			</div>
		</dd>
	</dl>

	<!-- Key -->
	<dl class="small">
		<dt>
			<label for="nameExtend<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_name'); ?>"><?php echo lang('ionize_label_key'); ?></label>
		</dt>
		<dd>
			<input id="nameExtend<?php echo $id_extend_field; ?>" name="name" class="inputtext required w240" type="text" value="<?php echo $name; ?>" />
		</dd>
	</dl>

	<!-- description -->
	<dl class="small">
		<dt>
			<label for="description<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_description'); ?>"><?php echo lang('ionize_label_description'); ?></label>
		</dt>
		<dd>
			<textarea id="description<?php echo $id_extend_field; ?>" name="description" class="inputtext autogrow" type="text"><?php echo $description; ?></textarea>
		</dd>
	</dl>

	<h3 class="mt20 extend<?php echo $id_extend_field; ?>"><?php echo lang('ionize_label_extend_field_definition') ?></h3>

	<div class="element extend<?php echo $id_extend_field; ?>">

		<!-- Type -->
		<dl class="small">
			<dt>
				<label for="type<?php echo $id_extend_field; ?>"><?php echo lang('ionize_label_extend_field_type'); ?></label>
			</dt>
			<dd>
				<?php echo $type_select ?>
			</dd>
		</dl>

		<!-- Can be translated ? -->
		<dl id="translate_block<?php echo $id_extend_field; ?>" class="small">
			<dt>
				<label for="translated<?php echo $id_extend_field; ?>"><?php echo lang('ionize_label_extend_field_translated'); ?></label>
			</dt>
			<dd>
				<input id="translated<?php echo $id_extend_field; ?>" name="translated" class="inputcheckbox" type="checkbox" value="1" <?php if ($translated=='1') :?> checked="checked" <?php endif ;?> />
			</dd>
		</dl>

		<!-- Values : For select, radio, checkboxes -->
		<dl id="value_block<?php echo $id_extend_field; ?>" class="small">
			<dt>
				<label for="value<?php echo $id_extend_field; ?>"><?php echo lang('ionize_label_values'); ?></label>
			</dt>
			<dd>
				<textarea id="value<?php echo $id_extend_field; ?>" name="value" class="inputtext autogrow warn-checkListFormat" type="text"><?php echo $value; ?></textarea>
				<p class="lite"><?php echo lang('ionize_help_ef_values'); ?></p>
			</dd>
		</dl>

		<!-- default_value -->
		<dl id="default_value_block<?php echo $id_extend_field; ?>" class="small">
			<dt>
				<label for="default_value<?php echo $id_extend_field; ?>"><?php echo lang('ionize_label_default_value'); ?></label>
			</dt>
			<dd>
				<textarea id="default_value<?php echo $id_extend_field; ?>" name="default_value" class="inputtext autogrow" type="text"><?php echo $default_value; ?></textarea>
				<p id="efListHelp" class="lite"><?php echo lang('ionize_help_ef_default_value') ?></p>
			</dd>
		</dl>
	</div>


	<?php if ($limit_to_parent && TRUE == FALSE) :?>

		<h3 class="toggler extend<?php echo $id_extend_field; ?>"><?php echo lang('ionize_title_advanced') ?></h3>

		<div class="element extend<?php echo $id_extend_field; ?>">

			<dl class="small">
				<dt>
					<label for="copy_in<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_copy_in'); ?>"><?php echo lang('ionize_label_extend_field_copy_in'); ?></label>
				</dt>
				<dd>
					<input id="copy_in<?php echo $id_extend_field; ?>" name="copy_in" class="inputtext w96p" type="text" value="<?php echo $copy_in; ?>" />
				</dd>
			</dl>

			<dl class="small">
				<dt>
					<label for="copy_in<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_copy_in_pk'); ?>"><?php echo lang('ionize_label_extend_field_copy_in_pk'); ?></label>
				</dt>
				<dd>
					<input id="copy_in_pk<?php echo $id_extend_field; ?>" name="copy_in_pk" class="inputtext w96p" type="text" value="<?php echo $copy_in_pk; ?>" />
				</dd>
			</dl>
		</div>

	<?php endif ;?>


</form>

<div class="buttons">
	<button id="bSaveextendfield<?php echo $id_extend_field; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelextendfield<?php echo $id_extend_field; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	var id = '<?php echo $id_extend_field; ?>';
	var id_type =  '<?php echo $type; ?>'
	var extend_types =  JSON.decode('<?php echo $extend_types; ?>', false);
	var default_lang_code  = '<?php echo Settings::get_lang("default") ?>';


	function get_extend_type(id_type)
	{
		var type = null;

		Array.each(extend_types, function(item)
		{
			if (item.id_extend_field_type == id_type)
			{
				type = item;
			}
		});
		return type;
	}


	function display_value_block(id_type)
	{
		var type = get_extend_type(id_type);

		var value_block = $('value_block' + id);
		var default_value_block = $('default_value_block' + id);
		var translate_block = $('translate_block' + id);

		if (type.values == 1) value_block.show(); else value_block.hide();
		if (type.default_values == 1) default_value_block.show(); else default_value_block.hide();
		if (['radio','checkbox','select','select-multiple'].contains(type.html_element_type)) $('efListHelp').show(); else $('efListHelp').hide();
		if (type.translated == 1) translate_block.show(); else translate_block.hide();
	}
	

	$('type' + id).addEvent('change', function()
	{
		var id_type = this.get('value');
		display_value_block(id_type);
	});
	display_value_block($('type' + id).value);


	// Form Validation
	Form.Validator.add('checkListFormat', {
		errorMsg: Lang.get('ionize_form_validator_warning_format_corrected'),
		test: function(element)
		{
			var v = element.value,
				newVal = '',
				passed = true
			;

			// v = v.replace(/ /g, '');
			v = v.trim();
			v = v.split(/\r\n|\r|\n/g);

			Object.each(v, function(row)
			{
				var kv = row.split(':');
				if (Object.getLength(kv) == 2)
				{
					row = kv[0].charTrim(' ,;') + ':' + kv[1].trim(' ,;');
					newVal += newVal == '' ? row : '\n' + row;
				}
				else passed = false;
			});

			element.setProperty('value', newVal);

			return passed;
		}
	});

	// Auto-generate Main title
	$('nameExtend' + id).addEvent('keyup', function()
	{
		$('mainTitleExtend' + id).set('text', this.value);
	});
	if (id) $('nameExtend' + id).fireEvent('keyup');

	ION.initFormAutoGrow('extendfieldForm' + id);

	// Auto generates the name of the field
	if (id == '')
		ION.initCorrectUrl('label_' + default_lang_code + id, 'nameExtend' + id);

	// Tabs
	var extendFieldTab<?php echo $id_extend_field; ?> = new TabSwapper({
		tabsContainer: 'extendFieldTab<?php echo $id_extend_field; ?>',
		sectionsContainer: 'extendFieldTabContent<?php echo $id_extend_field; ?>',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent'
	});

	// Delete Action
	if (typeOf($('bDeleteextendfield' + id)) != 'null')
	{
		$('bDeleteextendfield' + id).addEvent('click', function()
		{
			ION.confirmation(
				'wConfirmDelete' + id,
				function()
				{
					ION.JSON(
						ION.adminUrl + 'extend_field/delete/' + id,
						{},
						{
							onSuccess: function()
							{
								$('bCancelextendfield' + id).click();
							}
						}
					);
				},
				Lang.get('ionize_confirm_extend_delete')
			);
		});
	}

</script>

