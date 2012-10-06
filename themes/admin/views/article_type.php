<?php

/**
 * Modal window for Type creation / edition
 *
 */

?>

<form name="article_typeForm<?php echo $id_type; ?>" id="article_typeForm<?php echo $id_type; ?>" action="<?php echo admin_url(); ?>article_type/save">

	<!-- Hidden fields -->
	<input id="id_type" name="id_type" type="hidden" value="<?php echo $id_type; ?>" />
	<input id="parent" name="parent" type="hidden" value="<?php echo $parent; ?>" />
	<input id="id_parent" name="id_parent" type="hidden" value="<?php echo $id_parent; ?>" />

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="type"><?php echo lang('ionize_label_type'); ?></label>
		</dt>
		<dd>
			<input id="type" name="type" class="inputtext required" type="text" value="<?php echo $type; ?>" />
		</dd>
	</dl>
	
	<!-- Flag -->
	<dl class="small">
		<dt>
			<label for="flag0<?php echo $UNIQ; ?>" title="<?php echo lang('ionize_help_flag'); ?>"><?php echo lang('ionize_label_flag'); ?></label>
		</dt>
			<dd>
				<label class="flag flag0"><input id="flag0<?php echo $UNIQ; ?>" name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 0):?> checked="checked" <?php endif;?> value="0" /></label>
				<label class="flag flag1"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 1):?> checked="checked" <?php endif;?> value="1" /></label>
				<label class="flag flag2"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 2):?> checked="checked" <?php endif;?> value="2" /></label>
				<label class="flag flag3"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 3):?> checked="checked" <?php endif;?> value="3" /></label>
				<label class="flag flag4"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 4):?> checked="checked" <?php endif;?> value="4" /></label>
				<label class="flag flag5"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 5):?> checked="checked" <?php endif;?> value="5" /></label>
				<label class="flag flag6"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 6):?> checked="checked" <?php endif;?> value="6" /></label>
			</dd>
		</dt>
	</dl>

	<!-- Description -->
	<dl class="small">
		<dt>
			<label for="description<?php echo $UNIQ; ?>"><?php echo lang('ionize_label_description'); ?></label>
		</dt>
		<dd>
			<textarea id="description<?php echo $UNIQ; ?>" name="description" class="tinyType<?php echo $UNIQ; ?> w240 h120"><?php echo $description; ?></textarea>
		</dd>
	</dl>
	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSavearticle_type<?php echo $id_type; ?>" type="button" class="button yes right mr40"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelarticle_type<?php echo $id_type; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	ION.windowResize('article_type<?php echo $id_type; ?>', {width:450, height:230});

	tinyMCE.init(ION.tinyMceSettings('tinyType<?php echo $UNIQ; ?>', 240, 120, 'small'));


</script>

