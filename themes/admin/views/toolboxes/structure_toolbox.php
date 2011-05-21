<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="btnStructureExpand" value="<?= lang('ionize_label_collapse_all') ?>" />
</div>

<div class="toolbox ml5" id="toggleHeaderButton">
	<span class="iconWrapper"><img src="<?= theme_url() ?>images/icon_16_screen.png" width="16" height="16" alt="<?= lang('ionize_button_toggle_header') ?>" title="<?= lang('ionize_button_toggle_header') ?>" /></span>
</div>


<script type"text/javascript">


	// ToggleHeader Button
	$('toggleHeaderButton').addEvent('click', function(e)
	{
		e.stop();
		var cn = 'desktopHeader';
		var el = $(cn);
		var opened = 'true';
		
		if (Cookie.read(cn))
		{
			opened = (Cookie.read(cn));
		}
		if (opened == 'false')
		{
			Cookie.write(cn, 'true');
			el.show();
		}
		else
		{
			Cookie.write(cn, 'false');
			el.hide();
		}
		MUI.get('desktop').setDesktopSize();
	});
	
	// Init desktopHeader status from cookie
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
	});



</script>