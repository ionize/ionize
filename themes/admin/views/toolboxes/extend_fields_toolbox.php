<?php if(Authority::can('create', 'admin/extend')) :?>

	<div class="divider">
		<a id="btnAddExtendField" class="button light">
			<i class="icon-plus"></i>
			<?php echo lang('ionize_title_extend_field_new'); ?>
		</a>
	</div>


	<script type="text/javascript">

		$('btnAddExtendField').addEvent('click', function(e)
		{
			// Does not limit to one parent
			ION.formWindow(
				'extendfield',
				'extendfieldForm',
				'ionize_title_extend_field_new',
				'extend_field/edit',
				{
					width:450,
					height:380
				}
			);
		});

	</script>

<?php endif;?>
