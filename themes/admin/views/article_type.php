<?php

/**
 * Modal window for Type creation / edition
 *
 */
?>

<form name="article_typeForm<?= $id_type ?>" id="article_typeForm<?= $id_type ?>" action="<?= admin_url() ?>article_type/save">

	<!-- Hidden fields -->
	<input id="id_type" name="id_type" type="hidden" value="<?= $id_type ?>" />
	<input id="parent" name="parent" type="hidden" value="<?= $parent ?>" />
	<input id="id_parent" name="id_parent" type="hidden" value="<?= $id_parent ?>" />

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="type"><?=lang('ionize_label_type')?></label>
		</dt>
		<dd>
			<input id="type" name="type" class="inputtext required" type="text" value="<?= $type ?>" />
		</dd>
	</dl>
	
	<!-- Flag -->
	<dl class="small">
		<dt>
			<label for="flag0<?= $UNIQ ?>" title="<?= lang('ionize_help_flag') ?>"><?= lang('ionize_label_flag') ?></label>
		</dt>
			<dd>
				<label class="flag flag0"><input id="flag0<?= $UNIQ ?>" name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 0):?> checked="checked" <?php endif;?> value="0" /></label>
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
			<label for="description<?= $UNIQ ?>"><?=lang('ionize_label_description')?></label>
		</dt>
		<dd>
			<textarea id="description<?= $UNIQ ?>" name="description" class="tinyType<?= $UNIQ ?> w240 h120"><?= $description ?></textarea>
		</dd>
	</dl>
	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSavearticle_type<?= $id_type ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelarticle_type<?= $id_type ?>"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	ION.windowResize('article_type<?= $id_type ?>', {width:450, height:230});

	tinyMCE.init(ION.tinyMceSettings('tinyType<?= $UNIQ ?>', 240, 120, 'small'));


</script>

