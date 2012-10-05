<?php

/**
 * Modal window for Editing a group
 *
 */
    log_message('error', 'View File Loaded : group.php');

if ( ! empty($group['id_group']))
	$action = 'update';
else
	$action = 'save';


?>

<form name="groupForm<?php echo $group['id_group']; ?>" id="groupForm<?php echo $group['id_group']; ?>" action="<?php echo admin_url(); ?>groups/<?php echo $action; ?>">

	<!-- Hidden fields -->
	<input id="group_PK" name="group_PK" type="hidden" value="<?php echo $group['id_group']; ?>" />
	
	<!-- Group name -->
	<dl class="small">
		<dt>
			<label for="slug"><?php echo lang('ionize_label_group_name'); ?></label>
		</dt>
		<dd>
			<input id="slug" name="slug" class="inputtext" type="text" value="<?php echo $group['slug']; ?>" />
		</dd>
	</dl>

	<!-- Group title -->
	<dl class="small">
		<dt>
			<label for="group_name"><?php echo lang('ionize_label_group_title'); ?></label>
		</dt>
		<dd>
			<input id="group_name" name="group_name" class="inputtext" type="text" value="<?php echo $group['group_name']; ?>" />
		</dd>
	</dl>

	<!-- Level -->
	<dl class="small">
		<dt>
			<label for="level" ><?php echo lang('ionize_label_group_level'); ?></label>
		</dt>
		<dd>
			<input id="level" name="level" class="inputtext" type="text" value="<?php echo $group['level']; ?>" />
		</dd>
	</dl>

	<!-- Description -->
	<dl class="small">
		<dt>
			<label for="description"><?php echo lang('ionize_label_group_description'); ?></label>
		</dt>
		<dd>
			<textarea id="description" name="description"><?php echo $group['description']; ?></textarea>
		</dd>
	</dl>
	
</form>

<div class="buttons">
	<button id="bSavegroup<?php echo $group['id_group']; ?>" type="button" class="button yes right mr40"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelgroup<?php echo $group['id_group']; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>
