
<div class="divider">
	<a class="button light" id="newUserToolbarButton">
		<i class="icon-plus"></i><?php echo lang('ionize_title_add_user'); ?>
	</a>
</div>
<div class="divider">
	<a class="button light" id="newGroupToolbarButton">
		<i class="icon-plus"></i><?php echo lang('ionize_title_add_group'); ?>
	</a>
</div>

<script type="text/javascript">

	/**
	 * New user button
	 *
	 */
	$('newUserToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'user', 					// Window ID
			'userForm',					// Form ID
			'ionize_title_add_user', 	// Window title
			'users/get_form',			// Window content URL
			{width: 400, resize:true}	// Window options
		);
	});

	/**
	 * New group button
	 *
	 */
	$('newGroupToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'group',
			'groupForm',
			'ionize_title_add_group',
			'groups/get_form',
			{width: 400, resize:true}
		);
	});

</script>
