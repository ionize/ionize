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
	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through MUI.formWindow()
--> 
<div class="buttons">
	<button id="bSavearticle_type<?= $id_type ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelarticle_type<?= $id_type ?>"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>


