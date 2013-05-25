<?php
/**
 * Modal window for Creating a role
 *
 */

?>
<h2 class="main groups"><?php echo lang('ionize_title_add_role'); ?></h2>

<form name="roleForm" id="roleForm" action="role/save">

    <!-- Code -->
    <dl class="small">
        <dt>
            <label for="role_code"><?php echo lang('ionize_label_role_code'); ?></label>
        </dt>
        <dd>
            <input id="role_code" name="role_code" class="inputtext required" type="text" value="" />
        </dd>
    </dl>

    <!-- Name -->
    <dl class="small">
        <dt>
            <label for="role_name"><?php echo lang('ionize_label_role_name'); ?></label>
        </dt>
        <dd>
            <input id="role_name" name="role_name" class="inputtext required" type="text" value="" />
        </dd>
    </dl>

    <!-- Description -->
    <dl class="small">
        <dt>
            <label for="role_description"><?php echo lang('ionize_label_description'); ?></label>
        </dt>
        <dd>
            <textarea id="role_description" name="role_description"></textarea>
        </dd>
    </dl>

    <!-- Level -->
    <dl class="small">
        <dt>
            <label for="role_level"><?php echo lang('ionize_label_role_level'); ?></label>
        </dt>
        <dd>
			<input type="text" class="inputtext w50 required left mr15" name="role_level" id="role_level" />
			<div class="lite left">

				<strong><?php echo lang('ionize_help_role_choice') ?></strong><br/>
				<div style="font-size: 10px;line-height: 12px;">
				<?php foreach($roles as $role) :?>

					<?php echo $role['role_level'] ?> : <?php echo $role['role_name'] ?><br/>

				<?php endforeach ;?>
                </div>

			</div>
        </dd>
    </dl>

</form>

<div class="buttons">
    <button id="bSaverole" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
    <button id="bCancelrole"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>
