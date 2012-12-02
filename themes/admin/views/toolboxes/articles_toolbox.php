
<div class="divider">
	<a class="button light" id="newArticleToolbarButton">
		<i class="icon-article add"></i><?php echo lang('ionize_title_create_article'); ?>
	</a>
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
        ION.contentUpdate({
			'element': $(ION.mainpanel),
			'loadMethod': 'xhr',
			'url': admin_url + 'article/create',
			'title': Lang.get('ionize_title_create_article')
		});
	});

</script>
