<?php
    log_message('error', 'View File Loaded : users_list.php');

/**
 * Users list
 * Loaded through XHR
 *
 */
?>

<!-- Result -->

<?php if( ! empty($filter)) :?>

	<p><strong><?php echo lang('ionize_label_filter_result') ?> : </strong><?php echo $users_count ?></p>

<?php endif; ?>

<!-- Pages -->
<ul class="pagination" id="users_pagination">
	<?php
		if ($users_pages > 1)
		{
			for($i=1; $i<=$users_pages; $i++)
			{
			?>
				<li><a <?php if($i == $current_page) :?>class="current"<?php endif; ?> rel="<?php echo $i ?>"><?php echo $i ?></a></li>
			<?php
			}
		}
	?>
</ul>


<table class="list" id="usersTable">

	<thead>
		<tr>
			<th axis="string"><?php echo lang('ionize_label_id') ?></th>
			<th axis="string"><?php echo lang('ionize_label_username') ?></th>
			<th axis="string"><?php echo lang('ionize_label_screen_name') ?></th>
			<th axis="string"><?php echo lang('ionize_label_group') ?></th>
			<th axis="string"><?php echo lang('ionize_label_email') ?></th>
			<th axis="string"><?php echo lang('ionize_label_join_date') ?></th>
			<th></th>
		</tr>
	</thead>

	<tbody>
	
	<?php
	
	$i = 0;
	
	?>
	
	<?php foreach($users as $user) :?>
		
		<tr class="users<?php echo $user['id_user'] ?> users">
			<td><?php echo $user['id_user'] ?></td>
			<td><a class="user" id="user<?php echo $user['id_user'] ?>" rel="<?php echo $user['id_user'] ?>" href="<?php echo admin_url() ?>users/edit/<?php echo $user['id_user'] ?>"><?php echo $user['username'] ?></a></td>
			<td><?php echo $user['screen_name'] ?></td>
			<td><?php echo $user['group']['group_name'] ?></td>
			<td><?php echo $user['email'] ?></td>
			<td>
				<?php echo humanize_mdate($user['join_date'], Settings::get('date_format')) ?>
			</td>
			<td>
				<a class="icon delete" rel="<?php echo $user['id_user'] ?>"></a>
			</td>
		</tr>

	<?php endforeach ;?>
	
	</tbody>
</table>


<script type="text/javascript">

	/**
	 * Users itemManager
	 * Manager delete
	 *
	 */
	usersManager = new ION.ItemManager(
	{
		container: 'usersTable',
		element: 	'users'
	});
	
	
	/**
	 * Sortable on the current users list table
	 *
	 */
 	new SortableTable('usersTable',{sortOn: 0, sortBy: 'ASC'});

	/**
	 * User Edit window
	 *
	 */
	$$('.user').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			e.stop();
			var id = item.getProperty('rel');
			ION.formWindow(
				'user'+ id, 				// Window ID
				'userForm'+ id,				// Form ID
				'ionize_title_user_edit', 	// Window title
				'users/edit/' + id,			// Window content URL
				{width: 400, resize:true}	// Window options
			);
		});
	});
	
	
	/**
	 * Pagination element link
	 *
	 */
	$$('#users_pagination li a').each(function(item, idx)
	{
		item.addEvent('click', function(e)
		{
			e.stop();

			new Request.HTML({
				url: admin_url + 'users/users_list/' + this.getProperty('rel') + '/<?php echo $nb ?>',
				method: 'post',
				loadMethod: 'xhr',
				data: $('usersFilter'),
				update: $('usersList')
			}).send();
		});
	});

</script>
