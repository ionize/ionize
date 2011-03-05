<?php

	$id = str_replace('/', '', $path).$view;

?>
<form method="post" name="formView<?= $id ?>" id="formView<?= $id ?>" action="<?= admin_url() ?>setting/save_view">

	<input type="hidden" id="path_<?= $id ?>" name="path" value="<?= $path ?>" />
	<input type="hidden" id="view_<?= $id ?>" name="view" value="<?= $view ?>" />
	<input type="hidden" id="contentview_<?= $id ?>" name="content" value="" />

	<textarea id="editview_<?= $id ?>"  style="height: 420px; width: 100%; display:none;"><?= $content ?></textarea>

</form>

<div class="buttons">
	<button id="bSave<?= $id ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save') ?></button>
	<button id="bCancel<?= $id ?>"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	MUI.addFormSaveEvent('bSave<?= $id ?>');

</script>

