<?php if(Authority::can('create', 'admin/extend')) :?>

	<div class="toolbox divider">
		<input type="button" class="toolbar-button plus extends" id="addextendfield" value="<?php echo lang('ionize_title_extend_field_new'); ?>" />
	</div>


	<script type="text/javascript">

		$('addextendfield').addEvent('click', function(e)
		{
			ION.formWindow('extendfield', 'extendfieldForm', 'ionize_title_extend_fields', 'extend_field/get_form/', {width:400, height:330});
		});

	</script>

<?php endif;?>
