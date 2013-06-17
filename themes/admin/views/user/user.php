<?php
/**
 * Modal window for Editing an user
 *
 */
$from = ! empty($from) ? $from : '';
?>
<?php if ($user['id_user'] == '') :?>
	<h2 class="main user"><?php echo lang('ionize_title_add_user'); ?></h2>
<?php else :?>
	<h2 class="main user"><?php echo lang('ionize_title_user_edit'); ?></h2>
<?php endif ;?>


<form name="userForm<?php echo $user['id_user'] ?>" id="userForm<?php echo $user['id_user'] ?>" action="user/save">

	<!-- Hidden fields -->
	<input id="id_user" name="id_user" type="hidden" value="<?php echo $user['id_user'] ?>" />
	<input id="join_date" name="join_date" type="hidden" value="<?php echo $user['join_date'] ?>" />
	<input id="salt" name="salt" type="hidden" value="<?php echo $user['salt'] ?>" />
	<input name="from" type="hidden" value="<?php echo $from ?>" />

	<!-- Firstname -->
	<dl class="small">
		<dt>
			<label for="firstname<?php echo $user['id_user'] ?>"><?php echo lang('ionize_label_firstname'); ?></label>
		</dt>
		<dd>
			<input id="firstname<?php echo $user['id_user'] ?>" name="firstname" class="inputtext" type="text" value="<?php echo $user['firstname'] ?>" />
		</dd>
	</dl>

	<!-- Lastname -->
	<dl class="small">
		<dt>
			<label for="lastname<?php echo $user['id_user'] ?>"><?php echo lang('ionize_label_lastname'); ?></label>
		</dt>
		<dd>
			<input id="lastname<?php echo $user['id_user'] ?>" name="lastname" class="inputtext" type="text" value="<?php echo $user['lastname'] ?>" />
		</dd>
	</dl>

	<!-- Screen Name -->
	<dl class="small">
		<dt>
			<label for="screen_name<?php echo $user['id_user'] ?>"><?php echo lang('ionize_label_screen_name'); ?></label>
		</dt>
		<dd>
			<input id="screen_name<?php echo $user['id_user'] ?>" name="screen_name" class="inputtext" type="text" value="<?php echo $user['screen_name'] ?>" />
		</dd>
	</dl>

	<!-- Username -->
	<dl class="small">
		<dt>
			<label for="username<?php echo $user['id_user'] ?>"><?php echo lang('ionize_login'); ?></label>
		</dt>
		<dd>
			<input id="username<?php echo $user['id_user'] ?>" name="username" class="inputtext required minLength:4" type="text" value="<?php echo $user['username'] ?>" />
		</dd>
	</dl>

	<!-- Email -->
	<dl class="small">
		<dt>
			<label for="email<?php echo $user['id_user'] ?>" ><?php echo lang('ionize_label_email'); ?></label>
		</dt>
		<dd>
			<input id="email<?php echo $user['id_user'] ?>" data-id-user="<?php echo $user['id_user'] ?>" name="email" class="inputtext required emailUnique" type="text" value="<?php echo $user['email'] ?>" />
			<br/><span class="lite"><?php echo lang('ionize_help_email_can_be_used_as_login') ?></span>
		</dd>
	</dl>

	<!-- Role -->
	<dl class="small">
		<dt>
			<label for="id_role<?php echo $user['id_user'] ?>"><?php echo lang('ionize_label_role'); ?></label>
		</dt>
		<dd>
			<select name="id_role" class="select required">
				<?php foreach($roles as $role) :?>

					<option value="<?php echo $role['id_role'] ?>" <?php if($user['id_role'] == $role['id_role']) :?> selected="selected" <?php endif ;?> ><?php echo $role['role_name'] ?></option>
				
				<?php endforeach ;?>
			</select>
		</dd>
	</dl>
	

	<!-- New password -->
	<?php if ($user['id_user'] != '') :?>

		<h3 class="toggler toggler-user<?php echo $user['id_user'] ?> mt15"><?php echo lang('ionize_title_change_password'); ?></h3>

		<div class="element element-user<?php echo $user['id_user'] ?>">

			<p class="lite"><?php echo lang('ionize_help_password_change') ?></p>

	<?php endif ;?>


	<!-- Password -->
	<dl class="small">
		<dt>
			<label for="password<?php echo $user['id_user'] ?>"><?php echo lang('ionize_label_password'); ?></label>
		</dt>
		<dd>
			<input id="password<?php echo $user['id_user'] ?>" name="password" class="inputtext i120 <?php if ($user['id_user'] == ''):?>required <?php endif;?>" type="password" value="" />
		</dd>
	</dl>

	<!-- Password confirm -->
	<dl class="small">
		<dt>
			<label for="password2<?php echo $user['id_user'] ?>"><?php echo lang('ionize_label_password2'); ?></label>
		</dt>
		<dd>
			<input id="password2<?php echo $user['id_user'] ?>" name="password2" class="inputtext i120 <?php if ($user['id_user'] == ''):?>required <?php endif;?>validate-match matchInput:'password' matchName:'password'" type="password" value="" />
		</dd>
	</dl>

	<?php if ($user['id_user'] != '') :?>
		</div>
	<?php endif ;?>

	<h3 class="toggler toggler-user<?php echo $user['id_user'] ?>"><?php echo lang('ionize_title_notify_user_account_updated'); ?></h3>
	<div class="element element-user<?php echo $user['id_user'] ?>">

		<p class="lite"><?php echo lang('ionize_help_notify_user_account_updated') ?></p>

		<textarea name="message" class="autogrow"></textarea>

	</div>

</form>

<div class="buttons">
	<?php if ( Authority::can('edit', 'admin/user')) :?>
		<button id="bSaveuser<?php echo $user['id_user'] ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close') ?></button>
	<?php endif;?>
	<button id="bCanceluser<?php echo $user['id_user'] ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	ION.initFormAutoGrow();

    Form.Validator.add('emailUnique', {
        errorMsg: '<?php echo lang('ionize_message_email_already_registered') ?>',
        test: function(element, props) {
            if (element.value.length > 0) {
                var req = new Request({
                    url: admin_url + 'user/check_email_exists',
                    async: false,
                    data: {
                        email: $('email<?php echo $user['id_user'] ?>').value,
                        id_user: $('email<?php echo $user['id_user'] ?>').getProperty('data-id-user')
                    }
                }).send();
                return (req.response.text != '1');
            }
            return true;
        }
    });

	<?php if ($user['id_user'] != '') :?>
		ION.initAccordion('.toggler-user<?php echo $user['id_user'] ?>', 'div.element-user<?php echo $user['id_user'] ?>');
	<?php endif ;?>

</script>