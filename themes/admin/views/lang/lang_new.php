
<form name="langForm" id="langForm" method="post" action="<?php echo admin_url(); ?>lang/save">

	<!-- Lang Code -->
	<dl class="small">
		<dt>
			<label for="lang_new"><?php echo lang('ionize_label_code'); ?></label>
		</dt>
		<dd>
			<input id="lang_new" name="lang_new" class="inputtext w40" type="text" value="" />
		</dd>
	</dl>

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name_new"><?php echo lang('ionize_label_name'); ?></label>
		</dt>
		<dd>
			<input id="name_new" name="name_new" class="inputtext w140" type="text" value=""/><br />
		</dd>
	</dl>

	<!-- Online  -->
	<dl class="small">
		<dt>
			<label for="online_new"><?php echo lang('ionize_label_online'); ?></label>
		</dt>
		<dd>
			<input id="online_new" name="online_new" class="inputcheckbox" type="checkbox" value="1" />
		</dd>
	</dl>

</form>

<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
-->
<div class="buttons">
	<button id="bSavelang" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancellang"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

