
<!--
<div class="toolbox divider">
	<input type="button" class="toolbar-button plus extends" id="addextendfieldUsers" rel ="users" value="<?= lang('ionize_label_users') ?>" />
</div>
-->

<div class="toolbox divider">
	<input type="button" class="toolbar-button plus extends" id="addextendfieldMedia" rel ="media" value="<?= lang('ionize_label_media') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button plus extends" id="addextendfieldArticle" rel ="article" value="<?= lang('ionize_label_article') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button plus extends" id="addextendfieldPage" rel ="page" value="<?= lang('ionize_label_page') ?>" />
</div>

<script type="text/javascript">

	/**
	 * Buttons events
	 *
	 */
	$$('.extends').each(function(item, idx)
	{
		item.addEvent('click', function(e)
		{
			MUI.formWindow('extendfield', 'extendfieldForm', 'ionize_title_extend_fields', 'extend_field/get_form/' + this.getProperty('rel'), {width:400, height:330});
		});
		
	});

</script>
