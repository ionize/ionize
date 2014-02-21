
<?php if (Authority::can('access', 'admin/item/definition')) :?>

	<div class="divider">
		<a class="button light" id="newDefinitionToolbarButton">
			<i class="icon-plus"></i><?php echo lang('ionize_label_create'); ?>
		</a>
	</div>

	<script type="text/javascript">

		$('newDefinitionToolbarButton').addEvent('click', function(e)
		{
			ION.formWindow(
				'definition',
				'definitionForm',
				Lang.get('ionize_title_new_definition'),
				'item_definition/edit',
				{
					width:550,
					height:300
				}
			);
		});

	</script>
<?php endif ;?>
