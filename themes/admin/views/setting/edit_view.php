<?php

	$id = str_replace('/', '', $path).$view;

?>
<form method="post" name="formView<?php echo $id; ?>" id="formView<?php echo $id; ?>" action="<?php echo admin_url(); ?>setting/save_view">

	<input type="hidden" id="path_<?php echo $id; ?>" name="path" value="<?php echo $path; ?>" />
	<input type="hidden" id="view_<?php echo $id; ?>" name="view" value="<?php echo $view; ?>" />
	<input type="hidden" id="contentview_<?php echo $id; ?>" name="content" value="" />

	<textarea id="editview_<?php echo $id; ?>"  style="height: 420px; width: 100%; display:none;"><?php echo $content; ?></textarea>

</form>

<div class="buttons">
	<button id="bSave<?php echo $id; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save'); ?></button>
	<button id="bCancel<?php echo $id; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	ION.addFormSaveEvent('bSave<?php echo $id; ?>');

</script>

