
<div class="toolbox divider">
	<input type="button" class="toolbar-button plus extends" id="addextendfield" value="<?= lang('ionize_title_extend_field_new') ?>" />
</div>


<script type="text/javascript">

	$('addextendfield').addEvent('click', function(e)
	{
		MUI.formWindow('extendfield', 'extendfieldForm', 'ionize_title_extend_fields', 'extend_field/get_form/' + this.getProperty('rel'), {width:400, height:330});
	});
		
</script>
