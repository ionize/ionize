<div class="toolbox divider nobr">
	<input id="translationFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="btnExpand" value="<?= lang('ionize_label_expand_all') ?>" />
</div>

<div class="toolbox">
	<input type="button" class="toolbar-button plus" id="btnAddTranslation" value="<?= lang('ionize_label_add_translation') ?>" />
</div>

<script type="text/javascript">

	/**
	 * Form save action
	 * see init.js for more information about this method
	 *
	 */
	MUI.setFormSubmit('translationForm', 'translationFormSubmit', 'translation/save');

	/**
	 * Expand / Collapse button
	 *
	 */
	$('btnExpand').store('status', 'collapse');
	
	$('btnExpand').addEvent('click', function(e) 
	{
		e.stop();
		
		if (this.retrieve('status') == 'collapse')
		{
			$$('#block .toggler').each(function(el){
				el.fx.show();
				el.addClass('expand');
				el.getParent('ul').addClass('highlight');
			});
			this.value = Lang.get('ionize_label_collapse_all');
			this.store('status', 'expand');
		}
		else
		{
			$$('#block .toggler').each(function(el){
				el.fx.hide();
				el.removeClass('expand');
				el.getParent('ul').removeClass('highlight');
			});
			this.value = Lang.get('ionize_label_expand_all');
			this.store('status', 'collapse');
		}
	});


	/**
	 * Add button 
	 *
	 */
	$('btnAddTranslation').addEvent('click', function(e) 
	{
		ION.addTranslationTerm('block');
	});
	
	
	/**
	 * Save with CTRL+s
	 *
	 */
	MUI.addFormSaveEvent('translationFormSubmit');

</script>
