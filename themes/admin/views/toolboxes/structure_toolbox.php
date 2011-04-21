<div class="toolbox left ml5" id="toggleHeaderButton">
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
//		window.fireEvent('resize');
	});
	
	// Init desktopHeader status from cookie
	var dh = $('desktopHeader');
	var opened = (Cookie.read('desktopHeader'));
	if (opened == 'false') {dh.hide();}
	else {dh.show();} 
//	window.fireEvent('resize');
	MUI.get('desktop').setDesktopSize();


</script>