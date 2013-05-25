<?php
/**
 * Role List view
 *
 * Receives vars :
 * $roles :		Array of roles
 * 
 */
?>

<table class="list" id="roleTable">

    <thead>
		<tr>
			<th axis="number"><?php echo lang('ionize_label_id') ?></th>
			<th axis="number"><?php echo lang('ionize_label_role_level') ?></th>
			<th axis="string"><?php echo lang('ionize_label_role_code') ?></th>
			<th axis="string"><?php echo lang('ionize_label_role_name') ?></th>
			<th axis="string"><?php echo lang('ionize_label_description') ?></th>
			<th></th>
		</tr>
    </thead>

    <tbody>

		<?php foreach($roles as $role) :?>

			<tr data-id="<?php echo $role['id_role'] ?>">
				<td><?php echo $role['id_role'] ?></td>
				<td><?php echo $role['role_level'] ?></td>
				<td><a><?php echo $role['role_code'] ?></a></td>
				<td><?php echo $role['role_name'] ?></td>
				<td><?php echo $role['role_description'] ?></td>
				<td>
					<?php if( Authority::can('delete', 'admin/role')) :?>
                		<a data-id="<?php echo $role['id_role'] ?>" class="icon delete"></a>
					<?php endif; ?>
				</td>
			</tr>

		<?php endforeach ;?>

    </tbody>

</table>

<script type="text/javascript">

    // Sortbale
    new SortableTable('roleTable',{sortOn: 1, sortBy: 'DESC'});


    $$('#roleTable tbody tr').each(function(item)
	{
        var id = item.getProperty('data-id');

        item.addEvent('click', function()
		{
			ION.HTML(
				'role/edit',
				{'id_role': id},
				{update: 'roleContainer'}
			);
        });
    });

    $$('#roleTable tbody tr .delete').each(function(item)
	{
        var id = item.getProperty('data-id');
		ION.initRequestEvent(
			item,
            'role/delete',
            {'id_role': id},
            {'confirm':true}
		);
	});

</script>
