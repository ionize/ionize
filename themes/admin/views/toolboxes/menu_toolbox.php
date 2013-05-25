
<?php if ( Authority::can('create', 'admin/menu')) :?>

	<div class="divider">
		<a class="button light" id="newMenuToolbarButton">
			<i class="icon-plus"></i><?php echo lang('ionize_button_create_menu'); ?>
		</a>
	</div>

<?php endif;?>

<script type="text/javascript">

	<?php if ( Authority::can('create', 'admin/menu')) :?>

		$('newMenuToolbarButton').addEvent('click', function(e)
		{
			ION.formWindow(
				'menu',
				'menuForm',
				Lang.get('ionize_title_create_menu'),
				admin_url + 'menu/create',
				{
					'width':350,
					'height':180
				}
			);
		});

	<?php endif;?>

</script>

