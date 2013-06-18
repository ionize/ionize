<?php
/**
 * Modal window for Not Editing an user
 *
 */
$from = ! empty($from) ? $from : '';
?>
<h2 class="main user">
	<?php echo $user['firstname'] ?>
	<?php echo $user['lastname'] ?>
</h2>
<div class="main subtitle">
	<?php echo lang('ionize_message_write_an_email_to_this_user') ?> : <a href="mailto:<?php echo $user['email'] ?>"><?php echo $user['email'] ?></a>
</div>

<div class="buttons">
	<button id="bCanceluser<?php echo $user['id_user'] ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel') ?></button>
</div>

