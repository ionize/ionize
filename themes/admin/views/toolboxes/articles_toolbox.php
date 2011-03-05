
<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="newArticleToolbarButton" value="<?= lang('ionize_title_create_article') ?>" />
</div>

<div class="toolbox"></div>


<script type="text/javascript">
		
	/**
	 * New article button
	 *
	 */
	$('newArticleToolbarButton').addEvent('click', function(e)
	{
		e.stop();
		MUI.updateContent({
			'element': $(ION.options.mainpanel),
			'loadMethod': 'xhr',
			'url': admin_url + 'article/create',
			'title': Lang.get('ionize_title_create_article')
		});
	});

</script>
