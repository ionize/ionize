
<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="btnStructureExpand" value="<?php echo lang('ionize_label_collapse_all'); ?>" />
</div>

<div class="toolbox ml5" id="toggleHeaderButton">
	<span class="iconWrapper"><img src="<?php echo theme_url(); ?>images/icon_16_screen.png" width="16" height="16" alt="<?php echo lang('ionize_button_toggle_header'); ?>" title="<?php echo lang('ionize_button_toggle_header'); ?>" /></span>
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