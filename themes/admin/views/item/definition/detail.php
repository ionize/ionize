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
		<span class="lite">
			<?php echo $id_definition ?>
			|
		</span>
		<span class="lite"><?php echo lang('ionize_label_key') ?> : </span> <?php echo $definition['name'] ?>
		<?php if ( ! empty( $definition['description'])) :?><span class="lite"> | </span> <?php endif ;?>
		<?php echo $definition['description'] ?>
	</p>
</div>

<!--
	Fields of this Item Definition
-->
<?php if (Authority::can('edit', 'admin/item/definition')) :?>

	<h3 class="toggler itemDefinition"><?php echo lang('ionize_title_item_fields') ?></h3>

	<div class="element itemDefinition">
		<!-- Add Field button -->
		<p class="h30">
			<a id="btnAddItemField" class="button light right" data-id-definition="<?php echo $id_definition ?>">
				<i class="icon-plus"></i><?php echo lang('ionize_label_add_field') ?>
			</a>
		</p>
		<div id="itemFieldsContainer" class="mb30"></div>
	</div>

	<h3 class="toggler itemDefinition"><?php echo lang('ionize_title_item_instances') ?></h3>

<?php endif ;?>

<!--
	Item Instances
-->
<div class="element itemDefinition">
	<p class="h30">
		<a id="btnAddItem" class="button light right" data-id-definition="<?php echo $id_definition ?>">
			<i class="icon-plus"></i><?php echo lang('ionize_label_item_add_item') ?>
		</a>
	</p>
	<div id="itemInstancesContainer"></div>
</div>



<script type="text/javascript">


	var uniq = '<?php echo $UNIQ ?>';
	var id_definition = '<?php echo $id_definition ?>';

	// Instances List (Items having this definition)
	staticItemManager.init({
		destination: 'itemInstancesContainer',
		id_definition: id_definition
	});

	staticItemManager.getItemsFromDefinition();

	// Add instance button
	$('btnAddItem').addEvent('click', function(e)
	{
		staticItemManager.createItem(id_definition);
	});


	<?php if (Authority::can('edit', 'admin/item/definition') ) :?>

		// Inits the Extend Manager
		extendManager.init({
			parent: 'item',
			id_parent: id_definition
		});

		// Field List
		ION.HTML(
			'item_definition/get_field_list',
			{'id_item_definition': id_definition},
			{'update': 'itemFieldsContainer'}
		);

		// Add field button event
		$('btnAddItemField').addEvent('click', function(e)
		{
			extendManager.createExtend({
				parent: 'item',
				id_parent: id_definition,
				onSuccess: function()
				{
					ION.HTML(
						ION.adminUrl + 'item_definition/get_field_list',
						{id_item_definition: id_definition},
						{update: 'itemFieldsContainer'}
					);
				}
			});
		});

		ION.initAccordion(
			'.toggler.itemDefinition',
			'.element.itemDefinition',
			true,
			'itemDefinitionAccordion'
		);

	<?php endif ;?>


</script>