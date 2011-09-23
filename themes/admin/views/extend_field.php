<?php

/**
 * Modal window for extend field creation / edition
 *
 */
?>

<form name="extendfieldForm" id="extendfieldForm<?= $id_extend_field ?>" action="<?= admin_url() ?>extend_field/save">

	<!-- Hidden fields -->
	<input id="id_extend_field" name="id_extend_field" type="hidden" value="<?= $id_extend_field ?>" />
	<input id="parent" name="parent" type="hidden" value="<?= $parent ?>" />
	<input id="ordering" name="ordering" type="hidden" value="<?= $ordering ?>" />


	<div class="summary">
		
		<!-- Parent -->
		<dl class="small">
			<dt>
				<label for="parent<?= $id_extend_field ?>" title="<?=lang('ionize_help_ef_parent')?>"><?=lang('ionize_label_extend_field_parent')?></label>
			</dt>
			<dd>
				<select id="parent<?= $id_extend_field ?>" name="parent" class="select">
<!--				<option value="" <?php if ($parent=='') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_extend_field_for_all')?></option> -->
					<option value="page" <?php if ($parent=='page') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_extend_field_for_pages')?></option>
					<option value="article" <?php if ($parent=='article') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_extend_field_for_articles')?></option>
					<option value="media" <?php if ($parent=='media') :?> selected="selected" <?php endif ;?>><?= lang('ionize_label_extend_field_for_medias')?></option>
				</select>
			</dd>
			
		</dl>
		<dl class="small">
			<dt class="lite"><label><?=lang('ionize_label_label')?></label></dt>
			<dd>

				<!-- Tabs -->
				<div id="extendFieldTab<?= $id_extend_field ?>" class="mainTabs gray mb5 mt0 ">
					
					<ul class="tab-menu">
						
						<?php foreach(Settings::get_languages() as $language) :?>
						
							<li class="tab_extend<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?= $language['lang'] ?>"><a><?= ucfirst($language['name']) ?></a></li>
						
						<?php endforeach ;?>
			
					</ul>
					<div class="clear"></div>
				
				</div>
				
				<div id="extendFieldTabContent<?= $id_extend_field ?>">
					
					<?php foreach(Settings::get_languages() as $language) :?>
						
						<?php $lang = $language['lang']; ?>
			
						<div class="tabcontent <?= $lang ?>">
					
							<!-- Label -->
							<input id="label_<?= $lang ?><?= $id_extend_field ?>" name="label_<?= $lang ?>" class="inputtext title" type="text" value="<?= ${$lang}['label'] ?>"/>
			
						</div>
					<?php endforeach ;?>
				</div>
			</dd>
		</dl>
		<!-- Global 
		<dl class="small">
			<dt>
				<label for="global<?= $id_extend_field ?>" title="<?=lang('ionize_help_ef_global')?>"><?=lang('ionize_label_extend_field_global')?></label>
			</dt>
			<dd>
				<input id="global<?= $id_extend_field ?>" name="global" class="inputcheckbox" type="checkbox" value="1" <?php if ($global=='1') :?> checked="checked" <?php endif ;?> />
			</dd>
		</dl>
		-->

	</div>

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name<?= $id_extend_field ?>" title="<?=lang('ionize_help_ef_name') ?>"><?=lang('ionize_label_name')?></label>
		</dt>
		<dd>
			<input id="name<?= $id_extend_field ?>" name="name" class="inputtext required" type="text" value="<?= $name ?>" />
		</dd>
		
	</dl>



	<!-- Label 
	<dl class="small">
		<dt>
			<label for="label<?= $id_extend_field ?>" title="<?=lang('ionize_help_label_label')?>"><?=lang('ionize_label_label')?></label>
		</dt>
		<dd>
			<input id="label<?= $id_extend_field ?>" name="label" class="inputtext required" type="text" value="<?= $label ?>" />
		</dd>
		
	</dl>
	-->
	
	

	<!-- Type -->
	<dl class="small">
		<dt>
			<label for="type<?= $id_extend_field ?>"><?=lang('ionize_label_extend_field_type')?></label>
		</dt>
		<dd>
			<select name="type" id="type<?= $id_extend_field ?>" class="select">
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
	
	<!-- Can be translated ? -->
	<dl id="translate_block<?= $id_extend_field ?>" class="small">
		<dt>
			<label for="translated<?= $id_extend_field ?>"><?=lang('ionize_label_extend_field_translated')?></label>
		</dt>
		<dd>
			<input id="translated<?= $id_extend_field ?>" name="translated" class="inputcheckbox" type="checkbox" value="1" <?php if ($translated=='1') :?> checked="checked" <?php endif ;?> />
		</dd>
	</dl>

	<!-- Values : For select, radio, checkboxes -->
	<dl id="value_block<?= $id_extend_field ?>" class="small">
		<dt>
			<label for="value<?= $id_extend_field ?>" title="<?=lang('ionize_help_ef_values') ?>"><?= lang('ionize_label_values') ?></label>
		</dt>
		<dd>
			<textarea id="value<?= $id_extend_field ?>" name="value" class="inputtext w200 h40" type="text"><?= $value ?></textarea>
		</dd>
	</dl>
	
	<!-- default_value -->
	<dl id="default_value_block<?= $id_extend_field ?>" class="small">
		<dt>
			<label for="default_value<?= $id_extend_field ?>" title="<?=lang('ionize_help_ef_default_value') ?>"><?= lang('ionize_label_default_value') ?></label>
		</dt>
		<dd>
			<textarea id="default_value<?= $id_extend_field ?>" name="default_value" class="inputtext w200 h40" type="text"><?= $default_value ?></textarea>
		</dd>
	</dl>

	<!-- description -->
	<dl class="small">
		<dt>
			<label for="description<?= $id_extend_field ?>" title="<?=lang('ionize_help_ef_description') ?>"><?= lang('ionize_label_description') ?></label>
		</dt>
		<dd>
			<textarea id="description<?= $id_extend_field ?>" name="description" class="inputtext w200 h40" type="text"><?= $description ?></textarea>
		</dd>
	</dl>


</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSaveextendfield<?= $id_extend_field ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelextendfield<?= $id_extend_field ?>"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#extendfieldForm<?= $id_extend_field ?>');

	function display_value_block()
	{
		var id = '<?= $id_extend_field ?>';
	
		if ($('type' + id).value == '7')
		{
			$('value_block' + id).setStyle('display', 'none');
			$('default_value_block' + id).setStyle('display', 'none');
			$('translate_block' + id).setStyle('display', 'none');
		}
		else if ($('type' + id).value < 4)
		{
			$('value_block' + id).setStyle('display', 'none');
			
			if ($('default_value_block' + id).getStyle('display') == 'none')
			{
				$('default_value_block' + id).setStyle('display', 'block').highlight();
				$('translate_block' + id).setStyle('display', 'block').highlight();
			}
		}
		else
		{
			if ($('value_block' + id).getStyle('display') == 'none')
			{
				$('value_block' + id).setStyle('display', 'block').highlight();
				$('default_value_block' + id).setStyle('display', 'block').highlight();
				$('translate_block' + id).setStyle('display', 'block').highlight();
			}
		}
		
		// Window Resize
		ION.windowResize('extendfield' + id, {'width':410});
	}
	
	$('type<?= $id_extend_field ?>').addEvent('change', function()
	{
		display_value_block();
	});
	display_value_block();
	
	
	// Auto generates the name of the field
	ION.initCorrectUrl('label', 'name');

	/** 
	 * Lang tabs
	 */
	var extendFieldTab<?= $id_extend_field ?> = new TabSwapper({tabsContainer: 'extendFieldTab<?= $id_extend_field ?>', sectionsContainer: 'extendFieldTabContent<?= $id_extend_field ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent'});

</script>

