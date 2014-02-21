<?php
/**
 * Static Items > Definition list
 *
 */
?>
<?php if( ! empty($items)): ?>

	<ul id="staticItemsDefinitionPanelList" class="mb20 mt10 list">

		<?php foreach($items as $item) :?>

			<li class="list pointer" draggable="true" data-id="<?php echo $item['id_item_definition'] ?>">
				<a class="left title unselectable"><?php echo $item['title_definition'] ?></a>
				<a class="icon delete right"></a>
				<a class="icon edit right mr5"></a>
			</li>

		<?php endforeach ;?>

	</ul>


	<script type="text/javascript">

		$$('#staticItemsDefinitionPanelList li').each(function(item)
		{
			var id = item.getProperty('data-id');

			// Display details
			item.getElement('a.title').addEvent('click', function()
			{
				ION.HTML(
					ION.adminUrl + 'item_definition/detail',
					{'id_item_definition': id},
					{'update': 'splitPanel_mainPanel_pad'}
				);
			});

			// Edit
			item.getElement('a.edit').addEvent('click', function()
			{
				ION.formWindow(
					'definition' + id,
					'definitionForm' + id,
					Lang.get('ionize_title_edit_definition'),

					ION.adminUrl + 'item_definition/edit',
					{
						'width':500,
						'height':300
					},
					{id_item_definition: id}
				);
			});

			// Delete
			ION.initRequestEvent(
				item.getElement('a.delete'),
				ION.adminUrl + 'item_definition/delete',
				{'id_item_definition':id},
				{confirm:true}
			);
		});

	</script>

<?php endif;?>