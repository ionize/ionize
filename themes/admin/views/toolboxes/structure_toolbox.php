
<div class="toolbox divider">
	<a class="button light" id="btnStructureExpand">
		<?php echo lang('ionize_label_collapse_all'); ?>
	</a>
</div>

<div class="toolbox ml5" id="toggleHeaderButton">
	<a class="button light" title="<?php echo lang('ionize_button_toggle_header'); ?>">
		<i class="icon-drag mr0"></i>
	</a>
</div>


<script type="text/javascript">


	/**
	 * ToggleHeader Button
	 *
	 */
    $('toggleHeaderButton').store('header', 'desktopHeader');

    $('toggleHeaderButton').addEvents(
	{
        'click': function(e)
		{
            e.stop();
            var header = this.retrieve('header');
            var opened = 'true';

            if (Cookie.read(header))
                opened = (Cookie.read(header));

            if (opened == 'false')
                this.fireEvent('show');
            else
                this.fireEvent('hide');

		},
        'show': function(e)
		{
			var header = this.retrieve('header');
			Cookie.write(header, 'true');
			$(header).show();
            MUI.get('desktop').setDesktopSize();
        },
        'hide': function(e)
		{
			var header = this.retrieve('header');
			$(header).hide();
			Cookie.write(header, 'false');
            MUI.get('desktop').setDesktopSize();
        }
	});



	/**
	 * Init desktopHeader status from cookie
	 *
	 */
	var dh = $('desktopHeader');
	var opened = (Cookie.read('desktopHeader'));
	if (opened == 'false') {dh.hide();}
	else {dh.show();} 
	MUI.get('desktop').setDesktopSize();

	
	/**
	 * Expand / Collapse button
	 *
	 */
	$('btnStructureExpand').store('status', 'expand');
	
	$('btnStructureExpand').addEvent('click', function(e) 
	{
		e.stop();
		
		$$('#structurePanel div.minus').each(function(el){
			el.fireEvent('click', {'target': el});
		});
/*
		if (this.retrieve('status') == 'collapse')
		{
			$$('#structurePanel div.plus').each(function(el){
				el.fireEvent('click', {'target': el});
			});
			this.value = Lang.get('ionize_label_collapse_all');
			this.store('status', 'expand');
		}
		else
		{
			$$('#structurePanel div.minus').each(function(el){
				el.fireEvent('click', {'target': el});
			});
			this.value = Lang.get('ionize_label_expand_all');
			this.store('status', 'collapse');
		}
*/
	});



</script>