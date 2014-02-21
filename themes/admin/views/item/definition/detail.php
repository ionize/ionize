<?php
/**
 * Item Definition Detail :
 *
 * - Add fields to the Item definition (Super Admin or authorized people)
 * - List instances
 * - Create / Edit / Delete item instances
 *
 * @receives :
 *		$definition : Detail of the definition
 */

$id_definition = $definition['id_item_definition'];

?>

<h2 class="main definition items"><?php echo $definition['title_definition']; ?></h2>
<div class="main subtitle ">
	<p>
	<?php if (User()->is('super-admin')) :?>
		<span class="lite">ID : </span> <?php echo $definition['name'] ?>
		<?php if ( ! empty( $definition['description'])) :?> | <?php endif ;?>
	<?php endif ;?>
	<?php echo $definition['description'] ?></p>
</div>

<!--
	Fields of this Item Definition
-->
<?php if (Authority::can('edit', 'admin/item/definition')) :?>

	<!-- Add Field button -->
	<p class="h30">
		<a id="btnAddItemField" class="button light right" data-id-definition="<?php echo $id_definition ?>">
			<i class="icon-plus"></i><?php echo lang('ionize_label_add_field') ?>
		</a>
	</p>
	<div id="itemFieldsContainer" class="mb30"></div>

	<h3><?php echo lang('ionize_title_item_instances') ?> : <?php echo $definition['title_item'] ?></h3>

<?php endif ;?>

<!--
	Item Instances
-->
<p class="h30">
	<a id="btnAddItem" class="button light right" data-id-definition="<?php echo $id_definition ?>">
		<i class="icon-plus"></i><?php echo lang('ionize_label_item_add_item') ?>
	</a>
</p>
<div id="itemInstancesContainer"></div>



<script type="text/javascript">

	var uniq = '<?php echo $UNIQ ?>';
	var id_definition = '<?php echo $id_definition ?>';

	// Instance List
	ION.HTML(
		'item/get_list_from_definition',
		{'id_item_definition': id_definition},
		{'update': 'itemInstancesContainer'}
	);

	// Add instance button
	$('btnAddItem').addEvent('click', function(e)
	{
		ION.formWindow(
			'item',
			'itemForm',
			'ionize_title_item_new',
			'item/add_item',
			{width:600, height:350},
			{'id_item_definition': id_definition}
		);

	});

	<?php if (Authority::can('edit', 'admin/item/definition') ) :?>

		// Field List
		ION.HTML(
			'item_definition/get_field_list',
			{'id_item_definition': '<?php echo $id_definition ?>'},
			{'update': 'itemFieldsContainer'}
		);

		// Add field button event
		$('btnAddItemField').addEvent('click', function(e)
		{
			var id = this.getProperty('data-id-definition');
			ION.formWindow(
				'itemfield',
				'itemfieldForm',
				'ionize_title_item_field_new',
				'item_field/create',
				{
					width:500,
					height:350
				},
				{'id_item_definition': id}
			);
		});
	<?php endif ;?>


</script>