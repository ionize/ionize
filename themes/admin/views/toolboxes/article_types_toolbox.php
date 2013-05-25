<?php if ( Authority::can('create', 'admin/article/type')) :?>

	<div class="divider">
		<a class="button light" id="newTypeToolbarButton">
			<i class="icon-plus"></i><?php echo lang('ionize_label_new_type'); ?>
		</a>
	</div>

	<div class="toolbox"></div>


	<script type="text/javascript">

		/**
		 * New type button
		 *
		 */
		$('newTypeToolbarButton').addEvent('click', function(e)
		{
			ION.formWindow(
				'article_type',
				'article_typeForm',
				Lang.get('ionize_label_new_type'),
				'article_type/get_form'
			);
		});

	</script>
<?php endif;?>
