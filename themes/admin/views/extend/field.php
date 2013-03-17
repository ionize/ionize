<?php

/**
 * Modal window for extend field creation / edition
 *
 */

?>

<form name="extendfieldForm" id="extendfieldForm<?php echo $id_extend_field; ?>" action="<?php echo admin_url(); ?>extend_field/save">

	<!-- Hidden fields -->
	<input id="id_extend_field" name="id_extend_field" type="hidden" value="<?php echo $id_extend_field; ?>" />
	<input id="parent" name="parent" type="hidden" value="<?php echo $parent; ?>" />
	<input id="ordering" name="ordering" type="hidden" value="<?php echo $ordering; ?>" />


	<div class="summary">
		
		<!-- Parent -->
		<dl class="small">
			<dt>
				<label for="parent<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_parent'); ?>"><?php echo lang('ionize_label_extend_field_parent'); ?></label>
			</dt>
			<dd>
				<select id="parent<?php echo $id_extend_field; ?>" name="parent" class="select">
<!--				<option value="" <?php if ($parent=='') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_extend_field_for_all'); ?></option> -->
					<option value="page" <?php if ($parent=='page') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_extend_field_for_pages'); ?></option>
					<option value="article" <?php if ($parent=='article') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_extend_field_for_articles'); ?></option>
					<option value="media" <?php if ($parent=='media') :?> selected="selected" <?php endif ;?>><?php echo lang('ionize_label_extend_field_for_medias'); ?></option>
				</select>
			</dd>
			
		</dl>
		<dl class="small">
			<dt class="lite"><label><?php echo lang('ionize_label_label'); ?></label></dt>
			<dd>

				<!-- Tabs -->
				<div id="extendFieldTab<?php echo $id_extend_field; ?>" class="mainTabs mb5 mt0 ">
					
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
							<input id="label_<?php echo $lang; ?><?php echo $id_extend_field; ?>" name="label_<?php echo $lang; ?>" class="inputtext title" type="text" value="<?php echo ${$lang}['label']; ?>"/>
			
						</div>
					<?php endforeach ;?>
				</div>
			</dd>
		</dl>
		<!-- Global 
		<dl class="small">
			<dt>
				<label for="global<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_global'); ?>"><?php echo lang('ionize_label_extend_field_global'); ?></label>
			</dt>
			<dd>
				<input id="global<?php echo $id_extend_field; ?>" name="global" class="inputcheckbox" type="checkbox" value="1" <?php if ($global=='1') :?> checked="checked" <?php endif ;?> />
			</dd>
		</dl>
		-->

	</div>

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_name'); ?>"><?php echo lang('ionize_label_name'); ?></label>
		</dt>
		<dd>
			<input id="name<?php echo $id_extend_field; ?>" name="name" class="inputtext required" type="text" value="<?php echo $name; ?>" />
		</dd>
		
	</dl>


	<!-- Type -->
	<dl class="small">
		<dt>
			<label for="type<?php echo $id_extend_field; ?>"><?php echo lang('ionize_label_extend_field_type'); ?></label>
		</dt>
		<dd>
			<select name="type" id="type<?php echo $id_extend_field; ?>" class="select">
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
			<label for="value<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_values'); ?>"><?php echo lang('ionize_label_values'); ?></label>
		</dt>
		<dd>
			<textarea id="value<?php echo $id_extend_field; ?>" name="value" class="inputtext w200 h40" type="text"><?php echo $value; ?></textarea>
		</dd>
	</dl>
	
	<!-- default_value -->
	<dl id="default_value_block<?php echo $id_extend_field; ?>" class="small">
		<dt>
			<label for="default_value<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_default_value'); ?>"><?php echo lang('ionize_label_default_value'); ?></label>
		</dt>
		<dd>
			<textarea id="default_value<?php echo $id_extend_field; ?>" name="default_value" class="inputtext w200 h40" type="text"><?php echo $default_value; ?></textarea>
		</dd>
	</dl>

	<!-- description -->
	<dl class="small">
		<dt>
			<label for="description<?php echo $id_extend_field; ?>" title="<?php echo lang('ionize_help_ef_description'); ?>"><?php echo lang('ionize_label_description'); ?></label>
		</dt>
		<dd>
			<textarea id="description<?php echo $id_extend_field; ?>" name="description" class="inputtext w200 h40" type="text"><?php echo $description; ?></textarea>
		</dd>
	</dl>


</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSaveextendfield<?php echo $id_extend_field; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelextendfield<?php echo $id_extend_field; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	function display_value_block()
	{
		var id = '<?php echo $id_extend_field; ?>';
	
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
	
	$('type<?php echo $id_extend_field; ?>').addEvent('change', function()
	{
		display_value_block();
	});
	display_value_block();
	
	
	// Auto generates the name of the field
	ION.initCorrectUrl('label', 'name');

	/** 
	 * Lang tabs
	 */
	var extendFieldTab<?php echo $id_extend_field; ?> = new TabSwapper({tabsContainer: 'extendFieldTab<?php echo $id_extend_field; ?>', sectionsContainer: 'extendFieldTabContent<?php echo $id_extend_field; ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent'});

</script>

