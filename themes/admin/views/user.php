<?php

/**
 * Modal window for Editing an user
 *
 */

if ( ! empty($user['id_user']))
	$action = 'update';
else
	$action = 'save';

?>

<form name="userForm<?php echo $user['id_user'] ?>" id="userForm<?php echo $user['id_user'] ?>" action="<?php echo admin_url() ?>users/<?php echo $action ?>">

	<!-- Hidden fields -->
	<input id="user_PK" name="user_PK" type="hidden" value="<?php echo $user['id_user'] ?>" />
	<input id="join_date" name="join_date" type="hidden" value="<?php echo $user['join_date'] ?>" />
	<input id="salt" name="salt" type="hidden" value="<?php echo $user['salt'] ?>" />
	
	<!-- Username -->
	<dl class="small">
		<dt>
			<label for="username"><?php echo lang('ionize_label_username'); ?></label>
		</dt>
		<dd>
			<input id="username" name="username" class="inputtext" type="text" value="<?php echo $user['username'] ?>" />
		</dd>
	</dl>

	<!-- Screen Name -->
	<dl class="small">
		<dt>
			<label for="screen_name"><?php echo lang('ionize_label_screen_name'); ?></label>
		</dt>
		<dd>
			<input id="screen_name" name="screen_name" class="inputtext" type="text" value="<?php echo $user['screen_name'] ?>" />
		</dd>
	</dl>

	<!-- Email -->
	<dl class="small">
		<dt>
			<label for="email" ><?php echo lang('ionize_label_email'); ?></label>
		</dt>
		<dd>
			<input id="email" name="email" class="inputtext" type="text" value="<?php echo $user['email'] ?>" />
		</dd>
	</dl>

	<!-- Group -->
	<dl class="small">
		<dt>
			<label for="email"><?php echo lang('ionize_label_group'); ?></label>
		</dt>
		<dd>
			<select name="id_group" class="select">
				<?php foreach($groups as $group) :?>

					<option value="<?php echo $group['id_group'] ?>" <?php if(! empty ($user['group']['id_group']) && $user['group']['id_group'] == $group['id_group']) :?> selected="selected" <?php endif ;?> ><?php echo $group['group_name'] ?></option>
				
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	

	<!-- New password -->
	<h3><?php echo lang('ionize_title_change_password'); ?></h3>


	<!-- Password -->
	<dl class="small">
		<dt>
			<label for="password"><?php echo lang('ionize_label_password'); ?></label>
		</dt>
		<dd>
			<input id="password" name="password" class="inputtext i120" type="password" value="" />
		</dd>
	</dl>

	<!-- Password confirm -->
	<dl class="small">
		<dt>
			<label for="password2"><?php echo lang('ionize_label_password2'); ?></label>
		</dt>
		<dd>
			<input id="password2" name="password2" class="inputtext i120" type="password" value="" />
		</dd>
	</dl>

	
	<!-- Meta data -->
	<h3><?php echo lang('ionize_title_user_meta'); ?></h3>

	<?php foreach($meta_data as $key => $field) :?>

		<dl class="small">
			<dt>
				<label for="<?php echo $key ?>"><?php echo $key ?></label>
			</dt>
			<dd>
				<?php
					echo form_build_field($field);
				?>
			</dd>
		</dl>

	<?php endforeach ;?>
	
</form>

<div class="buttons">
	<button id="bSaveuser<?php echo $user['id_user'] ?>" type="button" class="button yes right mr40"><?php echo lang('ionize_button_save_close') ?></button>
	<button id="bCanceluser<?php echo $user['id_user'] ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">


	/**
	 * TinyEditors
	 * Must be called after tabs init.
	 *
	 */
	/*
	 *  TODO : Create a tinyMCE init which launch the non translated editors
	 * 	ION.initTinyEditors('.tab_article', '#articleTabContent .tinyTextarea');
	 * 	Purpose : Init tinyMCE from user's meta potential fields
	 *
	 */

	ION.initDatepicker('<?php echo Settings::get('date_format') ;?>');

	ION.initLabelHelpLinks('#userForm<?php echo $user['id_user'] ?>');


</script>

