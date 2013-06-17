<?php
/**
 * Users list
 * Loaded through XHR
 *
 */
?>

<!-- Result -->
<p class="mt10">
	<strong>
		<?php if( ! empty($filter)) :?>
			<?php echo lang('ionize_label_filter_result') ?> :
		<?php else: ?>
			<?php echo lang('ionize_label_users_count') ?> :
		<?php endif; ?>
	</strong>
	<?php echo $users_count ?>
</p>


<?php if ($users_pages > 1) :?>
<!-- Pages -->
	<ul class="pagination mt5" id="users_pagination">
		<?php
			for($i=1; $i<=$users_pages; $i++)
			{
			?>
				<li><a <?php if($i == $current_page) :?>class="current"<?php endif; ?> data-page-number="<?php echo $i ?>"><?php echo $i ?></a></li>
			<?php
			}
		?>
	</ul>
<?php endif; ?>


<?php if (!empty($users)) :?>

	<table class="list" id="usersTable">

		<thead>
			<tr>
				<th axis="number"><?php echo lang('ionize_label_id') ?></th>
                <th axis="string"><?php echo lang('ionize_label_email') ?></th>
				<th axis="string"><?php echo lang('ionize_label_username') ?></th>
				<th axis="string"><?php echo lang('ionize_label_screen_name') ?></th>
				<th axis="string"><?php echo lang('ionize_label_role') ?></th>
				<th axis="string"><?php echo lang('ionize_label_join_date') ?></th>
				<th></th>
				<th></th>
			</tr>
		</thead>

		<tbody>

			<?php

			$i = 0;

			?>

			<?php foreach($users as $user) :?>

				<tr data-id="<?php echo $user['id_user'] ?>">
					<td><?php echo $user['id_user'] ?></td>
                    <td><?php echo $user['email'] ?></td>
					<td><a><?php echo $user['username'] ?></a></td>
					<td><?php echo $user['screen_name'] ?></td>
					<td><?php echo $user['role_name'] ?></td>
					<td>
						<?php echo humanize_mdate($user['join_date'], Settings::get('date_format')) ?>
					</td>
                    <td>
                        <a class="icon mail" data-email="<?php echo $user['email'] ?>"></a>
                    </td>
					<td>
						<?php if(User()->getId() != $user['id_user'] && Authority::can('delete', 'admin/user')) :?>
							<a class="icon delete" data-id="<?php echo $user['id_user'] ?>"></a>
						<?php endif; ?>
					</td>
				</tr>

			<?php endforeach ;?>

		</tbody>
	</table>

	<script type="text/javascript">

		// Sortable
		new SortableTable('usersTable',{sortOn: 0, sortBy: 'ASC'});

		// Edit window
		$$('#usersTable tbody tr').each(function(item)
		{
			item.addEvent('click', function(e)
			{
				e.stop();
				var id = item.getProperty('data-id');
				ION.formWindow(
					'user'+ id, 				// Window ID
					'userForm'+ id,				// Form ID
					'ionize_title_user_edit', 	// Window title
					'user/edit',			// Window content URL
					{width: 520, height:440},	// Window options
					{'id_user': id}
				);
			});
		});

        $$('#usersTable tbody tr .delete').each(function(item)
        {
            var id = item.getProperty('data-id');
            ION.initRequestEvent(
				item,
				'user/delete',
				{'id_user': id},
				{'confirm':true}
            );
        });

        $$('#usersTable tbody tr .mail').each(function(item)
        {
            item.addEvent('click', function(e)
            {
                location.href="mailto:" + item.getProperty('data-email');
			});
        });

        // Pagination
		$$('#users_pagination li a').each(function(item, idx)
		{
			item.addEvent('click', function(e)
			{
				e.stop();

				new Request.HTML({
					url: admin_url + 'user/get_list/' + this.getProperty('data-page-number'),
					method: 'post',
					loadMethod: 'xhr',
					data: $('userFilter'),
					update: $('userList')
				}).send();
			});
		});

	</script>

<?php endif; ?>