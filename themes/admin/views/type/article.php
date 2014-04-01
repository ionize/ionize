<?php

/**
 * Modal window for Type creation / edition
 *
 */

?>

<form name="article_typeForm<?php echo $id_type; ?>" id="article_typeForm<?php echo $id_type; ?>" action="<?php echo admin_url(); ?>article_type/save">

	<!-- Hidden fields -->
	<input id="id_type" name="id_type" type="hidden" value="<?php echo $id_type; ?>" />
	<input id="parent" name="parent" type="hidden" value="<?php echo $parent; ?>" />
	<input id="id_parent" name="id_parent" type="hidden" value="<?php echo $id_parent; ?>" />
	<input id="ordering" name="ordering" type="hidden" value="<?php echo $ordering; ?>" />

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="type"><?php echo lang('ionize_label_type'); ?></label>
		</dt>
		<dd>
			<input id="type" name="type" class="inputtext required" type="text" value="<?php echo $type; ?>" />
		</dd>
	</dl>
	
	<!-- Flag -->
	<dl class="small">
		<dt>
			<label for="flag0<?php echo $UNIQ; ?>" title="<?php echo lang('ionize_help_flag'); ?>"><?php echo lang('ionize_label_flag'); ?></label>
		</dt>
			<dd>
				<p class="h25">
					<label class="flag flag0"><input id="flag0<?php echo $UNIQ; ?>" name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 0):?> checked="checked" <?php endif;?> value="0" /></label>
				</p>

				<p class="h25">
					<label class="flag flag19"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 19):?> checked="checked" <?php endif;?> value="19" /></label>
					<label class="flag flag20"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 20):?> checked="checked" <?php endif;?> value="20" /></label>
					<label class="flag flag21"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 21):?> checked="checked" <?php endif;?> value="21" /></label>
					<label class="flag flag22"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 22):?> checked="checked" <?php endif;?> value="22" /></label>
					<label class="flag flag23"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 23):?> checked="checked" <?php endif;?> value="23" /></label>
					<label class="flag flag24"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 24):?> checked="checked" <?php endif;?> value="24" /></label>
					<label class="flag flag25"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 25):?> checked="checked" <?php endif;?> value="25" /></label>
				</p>
				<p class="h25">
					<label class="flag flag1"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 1):?> checked="checked" <?php endif;?> value="1" /></label>
					<label class="flag flag2"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 2):?> checked="checked" <?php endif;?> value="2" /></label>
					<label class="flag flag3"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 3):?> checked="checked" <?php endif;?> value="3" /></label>
					<label class="flag flag4"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 4):?> checked="checked" <?php endif;?> value="4" /></label>
					<label class="flag flag5"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 5):?> checked="checked" <?php endif;?> value="5" /></label>
					<label class="flag flag6"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 6):?> checked="checked" <?php endif;?> value="6" /></label>
					<label class="flag flag26"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 26):?> checked="checked" <?php endif;?> value="26" /></label>
				</p>

				<p class="h25">
					<label class="flag flag7"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 7):?> checked="checked" <?php endif;?> value="7" /></label>
					<label class="flag flag8"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 8):?> checked="checked" <?php endif;?> value="8" /></label>
					<label class="flag flag9"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 9):?> checked="checked" <?php endif;?> value="9" /></label>
					<label class="flag flag10"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 10):?> checked="checked" <?php endif;?> value="10" /></label>
					<label class="flag flag11"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 11):?> checked="checked" <?php endif;?> value="11" /></label>
					<label class="flag flag12"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 12):?> checked="checked" <?php endif;?> value="12" /></label>
					<label class="flag flag27"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 27):?> checked="checked" <?php endif;?> value="27" /></label>
				</p>
				<p class="h25">
					<label class="flag flag13"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 13):?> checked="checked" <?php endif;?> value="13" /></label>
					<label class="flag flag14"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 14):?> checked="checked" <?php endif;?> value="14" /></label>
					<label class="flag flag15"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 15):?> checked="checked" <?php endif;?> value="15" /></label>
					<label class="flag flag16"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 16):?> checked="checked" <?php endif;?> value="16" /></label>
					<label class="flag flag17"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 17):?> checked="checked" <?php endif;?> value="17" /></label>
					<label class="flag flag18"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 18):?> checked="checked" <?php endif;?> value="18" /></label>
					<label class="flag flag28"><input name="type_flag" class="inputradio" type="radio" <?php if ($type_flag == 28):?> checked="checked" <?php endif;?> value="28" /></label>
				</p>
			</dd>
		</dt>
	</dl>

	<!-- Description -->
	<dl class="small">
		<dt>
			<label for="description<?php echo $UNIQ; ?>"><?php echo lang('ionize_label_description'); ?></label>
		</dt>
		<dd>
			<textarea id="description<?php echo $UNIQ; ?>" name="description" class="tinyType<?php echo $UNIQ; ?> w240 h120"><?php echo $description; ?></textarea>
		</dd>
	</dl>
	

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSavearticle_type<?php echo $id_type; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelarticle_type<?php echo $id_type; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	ION.windowResize('article_type<?php echo $id_type; ?>', {width:450, height:230});

	tinyMCE.init(ION.tinyMceSettings('tinyType<?php echo $UNIQ; ?>', 240, 120, 'small'));


</script>

