
<div class="divider">
	<a class="button submit" id="medialistFormSubmit">
		<?php echo lang('ionize_button_save'); ?>
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
		<option value="2"><?php echo lang('ionize_medialist_filter_by_broken_src') ?></option>
		<option value="3"><?php echo lang('ionize_medialist_filter_by_used_in_content') ?></option>
		<option value="4"><?php echo lang('ionize_medialist_filter_by_used_in_content_AL_missing') ?></option>
		<option value="5"><?php echo lang('ionize_medialist_filter_by_not_used') ?></option>
	</select>
</div>

<script type="text/javascript">

	<?php if(Authority::can('edit', 'admin/page')) :?>

	// Form save action
	ION.setFormSubmit('medialistForm', 'medialistFormSubmit', 'medialist/save');

	<?php endif;?>

	// Cards view
	$('btnMedialistViewCard').addEvent('click', function(btn)
	{
		$$('#mediaList .media').removeClass('list').addClass('card');
		$$('#mediaList .media .data').slide('hide');
		$$('#mediaList .toggle-card').show();
	});

	// List view
	$('btnMedialistViewList').addEvent('click', function(btn)
	{
		$$('#mediaList .toggle-card').addClass('panel-expand').addClass('panel-expanded');
		$$('#mediaList .toggle-card').removeClass('panel-collapse').removeClass('panel-collapsed');
		$$('#mediaList .toggle-card').hide();
		$$('#mediaList .media').removeClass('card').addClass('list');
		$$('#mediaList .media .data').slide('show');
	});

	// Filter
	$('mediaListFilter').addEvent('change', function(e)
	{
		e.stop();
		var choice = parseInt(e.target.value);

		switch (choice)
		{
			case 0:
				$$('#mediaList .media').each(function(el){el.show()});
				break;

			case 1 :
				$$('#mediaList .media').each(function(el)
				{
					el.show();
					if (el.getProperty('data-alt-missing') == '')
						el.hide();
				});
				break;

			case 2 :
				$$('#mediaList .media').each(function(el)
				{
					el.show();
					if (el.getProperty('data-has-source') == '1')
						el.hide();
				});
				break;

			case 3 :
				$$('#mediaList .media').each(function(el)
				{
					el.show();
					if (el.getProperty('data-is-used') == '')
						el.hide();
				});
				break;

			case 4 :
				$$('#mediaList .media').each(function(el)
				{
					el.hide();
					if (el.getProperty('data-is-used') == '1' && el.getProperty('data-alt-missing') == '1')
						el.show();
				});
				break;

			case 5 :
				$$('#mediaList .media').each(function(el)
				{
					el.show();
					if (el.getProperty('data-is-used') == '1')
						el.hide();
				});
				break;
		}
	});


</script>
