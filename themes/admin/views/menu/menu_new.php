
<form name="menuForm" id="menuForm" method="post" action="<?= admin_url() ?>menu/save">

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name_new"><?=lang('ionize_label_name')?></label>
		</dt>
		<dd>
			<input id="name_new" name="name_new" class="inputtext w140" type="text" value=""/><br />
		</dd>
	</dl>

	<!-- Title  -->
	<dl class="small">
		<dt>
			<label for="title_new"><?=lang('ionize_label_title')?></label>
		</dt>
		<dd>
			<input id="title_new" name="title_new" class="inputtext w140" type="text" value="" />
		</dd>
	</dl>

</form>

<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
-->
<div class="buttons">
	<button id="bSavemenu" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelmenu"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	/**
	 * Window resize
	 *
	 */
	ION.windowResize('menu', {width:350, height:100});


</script>