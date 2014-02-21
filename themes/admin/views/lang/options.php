<!-- New language -->
<h3 class="toggler mt20"><?php echo lang('ionize_title_add_language'); ?></h3>

<div class="element">

	<form name="newLangForm" id="newLangForm" method="post" action="<?php echo admin_url(); ?>lang/save">

		<!-- Lang Code -->
		<dl class="small">
			<dt>
				<label for="lang_new"><?php echo lang('ionize_label_code'); ?></label>
			</dt>
			<dd>
				<input id="lang_new" name="lang_new" class="inputtext w40" type="text" value="" />
			</dd>
		</dl>

		<!-- Name -->
		<dl class="small">
			<dt>
				<label for="name_new"><?php echo lang('ionize_label_name'); ?></label>
			</dt>
			<dd>
				<input id="name_new" name="name_new" class="inputtext w140" type="text" value=""/><br />
			</dd>
		</dl>

		<!-- Online  -->
		<dl class="small">
			<dt>
				<label for="online_new"><?php echo lang('ionize_label_online'); ?></label>
			</dt>
			<dd>
				<input id="online_new" name="online_new" class="inputcheckbox" type="checkbox" value="1" />
			</dd>
		</dl>

		<!-- Submit button  -->
		<dl class="small">
			<dt>
				<label>&#160;</label>
			</dt>
			<dd>
				<input id="submit_new" type="submit" class="submit" value="<?php echo lang('ionize_button_save_new_lang'); ?>" />
			</dd>
		</dl>

	</form>

</div> <!-- /element -->


<!-- Copy Content -->
<h3 class="toggler"><?php echo lang('ionize_title_content'); ?></h3>

<div class="element">

	<dl class="small">
		<dt>
			<label for="lang_copy_from" title="<?php echo lang('ionize_help_copy_all_content'); ?>"><?php echo lang('ionize_label_copy_all_content'); ?></label>
		</dt>
		<dd>
			<div class="w100 left">
				<select name="lang_copy_from" id="lang_copy_from" class="w100 select">
					<?php foreach(Settings::get_languages() as $language) :?>
					<option value="<?php echo $language['lang']; ?>"><?php echo ucfirst($language['name']); ?></option>
					<?php endforeach ;?>
				</select>

				<br/>

				<select name="lang_copy_to" id="lang_copy_to" class="w100 select mt5">
					<?php foreach(Settings::get_languages() as $language) :?>
					<option value="<?php echo $language['lang']; ?>"><?php echo ucfirst($language['name']); ?></option>
					<?php endforeach ;?>
				</select>

			</div>
			<div class="w30 h50 left ml5" style="background:url('<?php echo admin_style_url(); ?>images/icon_24_from_to.png') no-repeat 50% 50%;"></div>
		</dd>
	</dl>

	<!-- Submit button  -->
	<dl class="small">
		<dt>&#160;</dt>
		<dd>
			<input type="submit" value="<?php echo lang('ionize_button_copy_content'); ?>" class="submit" id="copy_lang">
		</dd>
	</dl>

</div>


<!-- Advanced actions with content -->
<h3 class="toggler"><?php echo lang('ionize_title_advanced_language'); ?></h3>

<div class="element">

	<p><?php echo lang('ionize_notify_advanced_language'); ?></p>

	<form name="cleanLangForm" id="cleanLangForm" method="post">

		<input id="submit_clean" type="submit" class="submit" value="<?php echo lang('ionize_button_clean_lang_tables'); ?>" />
		<label title="<?php echo lang('ionize_help_clean_lang_tables'); ?>"></label>

	</form>

</div>

<script type="text/javascript">


	/**
	 * New lang form action
	 * see init-form.js for more information about this method
	 *
	 */
	ION.setFormSubmit('newLangForm', 'submit_new', 'lang/save');

	/**
	 * Clean Lang tables form action
	 *
	 */
	ION.setFormSubmit('cleanLangForm', 'submit_clean', 'lang/clean_tables', {message:Lang.get('ionize_confirmation_clean_lang')});

	ION.initAccordion('.toggler', 'div.element', true, 'langAccordion1');

	// Content copy confirmation callback
	var copyLang = function()
	{
		var url = admin_url + 'lang/copy_lang_content';

		var data = {
			'case': 'lang',
			'from' : $('lang_copy_from').value,
			'to' : $('lang_copy_to').value
		};
		ION.sendData(url, data);

	};

	// Copy content
	$('copy_lang').addEvent('click', function(e)
	{
		e.stop();

		ION.confirmation(
			'copyLangConfWindow',
			copyLang,
			Lang.get('ionize_message_confirm_copy_whole_content')
		);
	});

</script>
