
<div class="divider">
	<a class="button submit" id="medialistFormSubmit">
		<?php echo lang('ionize_button_save_media_data'); ?>
	</a>
</div>

<div class="divider">
	<a class="button light right" id="btnMedialistViewList">
		<i class="icon-list"></i>
		<?php echo lang('ionize_button_list_view') ?>
	</a>
</div>

<div class="divider">
	<a class="button light right" id="btnMedialistViewCard">
		<i class="icon-card"></i>
		<?php echo lang('ionize_button_card_view') ?>
	</a>
</div>

<div class="right mr10">
	<select class="select" id="mediaListFilter">
		<option value="0"><?php echo lang('ionize_medialist_filter_by') ?></option>
		<option value="1"><?php echo lang('ionize_medialist_filter_by_alt_missing') ?></option>
		<option value="4"><?php echo lang('ionize_medialist_filter_by_used_in_content_AL_missing') ?></option>
	</select>
</div>

<script type="text/javascript">

	<?php if(Authority::can('edit', 'admin/page')) :?>

	// Form save action
	// ION.setFormSubmit('medialistForm', 'medialistFormSubmit', 'medialist/save');
	$('medialistFormSubmit').addEvent('click', function()
	{
		ION.JSON(
			ION.adminUrl + 'medialist/save',
			$('medialistForm')
		)
	});

	<?php endif;?>

	var cookieName = 'medialistView';

	// Cards view
	$('btnMedialistViewCard').addEvent('click', function(btn)
	{
		$$('#mediaList .media').removeClass('list').addClass('card');
		$$('#mediaList .media .data').slide('hide');
		$$('#mediaList .toggle-card').show();
		Cookie.write(cookieName, 'card');
	});

	// List view
	$('btnMedialistViewList').addEvent('click', function(btn)
	{
		$$('#mediaList .toggle-card').addClass('panel-expand').addClass('panel-expanded');
		$$('#mediaList .toggle-card').removeClass('panel-collapse').removeClass('panel-collapsed');
		$$('#mediaList .toggle-card').hide();
		$$('#mediaList .media').removeClass('card').addClass('list');
		$$('#mediaList .media .data').slide('show');
		Cookie.write(cookieName, 'list');
	});


	// Filter
	$('mediaListFilter').addEvent('change', function(e)
	{
		e.stop();
		var choice = parseInt(e.target.value);
		var data = {};

		switch (choice)
		{
			case 0:
				break;

			case 1 :
				data = {filter:'alt_missing'};
				break;

			case 4 :
				data = {filter:'alt_missing,used'};
				break;
		}

		ION.HTML(
			ION.adminUrl + 'medialist/get_list',
			data,
			{
				update:'medialistContainer'
			}
		);
	});


</script>
