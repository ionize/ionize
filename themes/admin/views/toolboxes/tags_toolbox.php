

<div class="divider">
	<a class="button submit" id="saveTagToolbarButton">
		<?php echo lang('ionize_button_save_tags'); ?>
	</a>
</div>



<div class="toolbox"></div>


<script type="text/javascript">
		
	// New tag button
	/*
	$('newTagToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'tag',
			'tagForm',
			Lang.get('ionize_label_new_tag'),
			'tag/create'
		);
	});
	*/

	$('saveTagToolbarButton').addEvent('click', function()
	{
		ION.JSON(
			ION.adminUrl + 'tag/save_list',
			$('tagsForm')
		)
	});

</script>
