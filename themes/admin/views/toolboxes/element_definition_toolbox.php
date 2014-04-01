
<?php if ( Authority::can('create', 'admin/element')) :?>

	<div class="divider">
		<a class="button light" id="addElementButton">
			<i class="icon-plus"></i>
			<?php echo lang('ionize_label_create_element'); ?>
		</a>
	</div>

	<script type="text/javascript">

		$('addElementButton').addEvent('click', function(e)
		{
			ION.JSON('element_definition/create');
		});

	</script>

<?php endif;?>
