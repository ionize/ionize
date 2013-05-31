<h2 class="main api-key">API Key</h2>

<form name="apiKeyForm<?php echo $id ?>" id="apiKeyForm<?php echo $id ?>" action="<?php echo admin_url() ?>api/save_key">

    <!-- Hidden fields -->
    <input id="id" name="id" type="hidden" value="<?php echo $id ?>" />


    <!-- Key Hash -->
    <dl class="small">
        <dt>
            <label for="key<?php echo $id ?>" ><?php echo lang('ionize_label_api_key_hash'); ?></label>
        </dt>
        <dd>
			<?php if ($key) :?>
				<?php echo $key ?>
			<?php else :?>
				<input type="hidden" name="key" value="" />
				<div id="new_api_key"></div>
			<?php endif;?>
        </dd>
    </dl>


    <a class="button light left">
		<i class="icon-edit"></i>
		<?php echo lang('ionize_label_api_key_level'); ?>
	</a>

    <!-- Level -->
    <dl class="small">
        <dt>
            <label for="level<?php echo $id ?>" ><?php echo lang('ionize_label_api_key_level'); ?></label>
        </dt>
        <dd>
            <select id="level<?php echo $id ?>" name="level" class="select">
                <option value="1" <?php if( $level == 1) :?> selected="selected" <?php endif ;?> >1</option>
                <option value="2" <?php if( $level == 2) :?> selected="selected" <?php endif ;?> >3</option>
                <option value="3" <?php if( $level == 3) :?> selected="selected" <?php endif ;?> >3</option>
            </select>
        </dd>
    </dl>

    <!-- Ignore limits -->
    <dl class="small">
        <dt>
            <label for="ignore_limits<?php echo $id ?>" ><?php echo lang('ionize_label_api_key_ignore_limits'); ?></label>
        </dt>
        <dd>
			<label for="ignore_limits<?php echo $id ?>_0">
            	<input type="radio" id="ignore_limits<?php echo $id ?>_0" name="ignore_limits" class="inputradio" value="0" <?php if( $ignore_limits == 0) :?> checked="checked" <?php endif ;?>>
				No
            </label>
            <label for="ignore_limits<?php echo $id ?>_1">
				<input type="radio" id="ignore_limits<?php echo $id ?>_1" name="ignore_limits" class="inputradio" value="1" <?php if( $ignore_limits == 1) :?> checked="checked" <?php endif ;?>>
                Yes
			</label>
        </dd>
    </dl>


    <!-- Is private key ?-->
    <dl class="small">
        <dt>
            <label for="is_private<?php echo $id ?>" ><?php echo lang('ionize_label_api_key_is_private'); ?></label>
        </dt>
        <dd>
			<label for="is_private<?php echo $id ?>_0">
            	<input type="radio" id="is_private<?php echo $id ?>_0" name="is_private" class="inputradio" value="0" <?php if( $is_private == 0) :?> checked="checked" <?php endif ;?>>
				No
            </label>
            <label for="is_private<?php echo $id ?>_1">
				<input type="radio" id="is_private<?php echo $id ?>_1" name="is_private" class="inputradio" value="1" <?php if( $is_private == 1) :?> checked="checked" <?php endif ;?>>
                Yes
			</label>
        </dd>
    </dl>

	<!-- IP addresses -->
    <dl class="small">
        <dt>
            <label for="ip_addresses<?php echo $id ?>" ><?php echo lang('ionize_label_api_key_ip_addresses'); ?></label>
        </dt>
		<dd>
            <textarea id="ip_addresses<?php echo $id ?>" name="ip_addresses">
				<?php echo $ip_addresses ;?>
            </textarea>
		</dd>
    </dl>

</form>

<div class="buttons">
    <button id="bSaveApiKey<?php echo $id ?>" type="button" class="button yes right mr40"><?php echo lang('ionize_button_save_close') ?></button>
    <button id="bCancelApiKey<?php echo $id ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel') ?></button>
</div>

