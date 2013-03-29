
<h2 class="main tree" id="main-title"><?php echo lang('ionize_title_create_menu') ?></h2>

<form name="menuForm" id="menuForm" method="post" action="<?php echo admin_url(); ?>menu/save">

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name"><?php echo lang('ionize_label_name'); ?></label>
		</dt>
		<dd>
			<input id="name" name="name" class="inputtext w140" type="text" value=""/><br />
		</dd>
	</dl>

	<!-- Title  -->
	<dl class="small">
		<dt>
			<label for="title"><?php echo lang('ionize_label_title'); ?></label>
		</dt>
		<dd>
			<input id="title" name="title" class="inputtext w140" type="text" value="" />
		</dd>
	</dl>

</form>

<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
-->
<div class="buttons">
	<button id="bSavemenu" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelmenu"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

