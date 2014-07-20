<?php
/**
 *
 *
 */
$id_definition = $id_item_definition;

?>
<?php if ( ! empty($fields)) :?>

	<ul id="fields<?php echo $UNIQ ?>" class="fields" data-id-definition="<?php echo $id_definition ?>">

		<?php foreach($fields as $field) :?>
			<?php
				$class_main = 'inactive';
				if ($field['main'] == 1) $class_main = '';
			?>
			<li id="extend_field<?php echo $field['id_extend_field'] ?>" class="extend_field sortme" data-id="<?php echo $field['id_extend_field'] ?>">
				<span class="icon left drag"></span>
				<a class="icon left display flag green ml10 <?php echo $class_main ?>" data-id="<?php echo $field['id_extend_field'] ?>" title="<?php echo lang('ionize_help_item_field_display') ?>"></a>
				<a class="left ml10 title" data-id="<?php echo $field['id_extend_field'] ?>"><?php echo $field['name'] ?></a>

				<a class="icon delete right" data-id="<?php echo $field['id_extend_field'] ?>"></a>
				<span class="lite right mr10" data-id="<?php echo $field['id_extend_field'] ?>">
					<?php echo $field['type_name'] ?>
					<?php if($field['translated'] == '1') :?>
						/ <?php echo lang('ionize_label_multilingual') ;?>
					<?php endif ;?>
				</span>
			</li>
		<?php endforeach ;;?>
	</ul>


	<script type="text/javascript">

		var uniq = '<?php echo $UNIQ ?>';
		var id_definition = '<?php echo $id_definition ?>';

		// Content Elements fields manager
		var fieldsManager<?php echo $UNIQ ?> = new ION.ItemManager({
			container: 'fields' + uniq,
			element: 'item_field'}
		);

		fieldsManager<?php echo $UNIQ ?>.makeSortable();

		// Loads the Extend Manager
		extendManager.init({
			parent: 'item'
		});

		// Edit
		$$('#fields'+uniq +' .title').each(function(item)
		{
			var id_extend = item.getProperty('data-id');

			item.addEvent('click', function(e)
			{
				extendManager.editExtend(
					id_extend,
					{
						onSuccess: function()
						{
							ION.HTML(
								ION.adminUrl + 'item_definition/get_field_list',
								{id_item_definition: id_definition},
								{update: 'itemFieldsContainer'}
							);
						}
					}
				);
			});
		});

		// Set "Display in List"
		$$('#fields'+uniq +' .display').each(function(item)
		{
			var id_extend = item.getProperty('data-id');

			item.addEvent('click', function(e)
			{
				e.stop();
				ION.JSON(
					ION.adminUrl + 'extend_field/set_main',
					{
						id_extend_field: id_extend
					},
					{
						onSuccess: function()
						{
							$$('#fields'+uniq +' .display').each(function(el)
							{
								el.addClass('inactive');
								if (el.getProperty('data-id') == id_extend)
									el.removeClass('inactive');
							});
						}
					}
				);
			});
		});

	</script>

<?php endif ;?>
