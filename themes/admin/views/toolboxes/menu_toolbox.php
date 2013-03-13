<?php if ( Authority::can('edit', 'admin/menu')) :?>

	<div class="toolbox divider nobr">
		<input id="existingMenuFormSubmit" type="button" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
	</div>

<?php endif;?>

<?php if ( Authority::can('create', 'admin/menu')) :?>

	<div class="divider">
		<a class="button light" id="newMenuToolbarButton">
			<i class="icon-plus"></i><?php echo lang('ionize_button_create_menu'); ?>
		</a>
	</div>

<?php endif;?>

<script type="text/javascript">

	<?php if ( Authority::can('edit', 'admin/menu')) :?>

		// Adds action to the existing menus form
		ION.setFormSubmit('existingMenuForm', 'existingMenuFormSubmit', 'menu/update');

		// Save with CTRL+s
    	ION.addFormSaveEvent('existingMenuFormSubmit');

	<?php endif;?>

	<?php if ( Authority::can('create', 'admin/menu')) :?>

		$('newMenuToolbarButton').addEvent('click', function(e)
		{
			ION.formWindow(
				'menu',
				'menuForm',
				Lang.get('ionize_title_create_menu'),
				admin_url + 'menu/get_form',
				{
					'width':350,
					'height':180
				}
			);
		});

	<?php endif;?>

</script>

