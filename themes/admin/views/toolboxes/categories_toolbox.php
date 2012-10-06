
<div class="divider">
	<a class="button light" id="newCategoryToolbarButton">
		<i class="icon-plus"></i><?php echo lang('ionize_label_new_category'); ?>
	</a>
</div>

<div class="toolbox"></div>


<script type="text/javascript">
		
	/**
	 * New category button
	 *
	 */
	$('newCategoryToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'category',
			'categoryForm',
			Lang.get('ionize_label_new_category'),
			'category/get_form'
		);
	});

</script>
