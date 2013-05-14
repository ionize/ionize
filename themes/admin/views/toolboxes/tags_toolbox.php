<div class="divider">
	<a class="button submit" id="saveTagToolbarButton">
		<?php echo lang('ionize_button_save_tags'); ?>
	</a>
</div>

<div class="toolbox"></div>

<script type="text/javascript">
		
	$('saveTagToolbarButton').addEvent('click', function()
	{
		ION.JSON(
			ION.adminUrl + 'tag/save_list',
			$('tagsForm')
		)
	});

</script>
