<?php if ( Authority::can('access', 'admin/settings/users/create')) :?>

	<div class="divider">
		<a class="button light" id="newUserToolbarButton">
			<i class="icon-plus"></i><?php echo lang('ionize_title_add_user'); ?>
		</a>
	</div>

<?php endif;?>


<?php if ( Authority::can('access', 'admin/settings/roles/create')) :?>

	<div class="divider">
		<a class="button light" id="newRoleToolbarButton">
			<i class="icon-plus"></i><?php echo lang('ionize_title_add_role'); ?>
		</a>
	</div>

<?php endif;?>

<script type="text/javascript">

	<?php if ( Authority::can('access', 'admin/settings/users/create')) :?>

		// New user
		$('newUserToolbarButton').addEvent('click', function(e)
		{
			ION.formWindow(
				'user', 					// Window ID
				'userForm',					// Form ID
				'ionize_title_add_user', 	// Window title
				'user/create',			// Window content URL
				{width: 400, resize:true}	// Window options
			);
		});

	<?php endif;?>

	<?php if ( Authority::can('access', 'admin/settings/roles/create')) :?>

		// New Role
		$('newRoleToolbarButton').addEvent('click', function(e)
		{
			ION.formWindow(
				'role',
				'roleForm',
				'ionize_title_add_role',
				'role/create',
				{width: 420, resize:true}
			);
		});

	<?php endif;?>

</script>
