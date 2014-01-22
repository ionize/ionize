<?php
/**
 *
 * View used by extend_field controller to display again the extend fields table
 * after an ADD / DELETE of one extend field
 *
 */
?>

<?php foreach($parents as $parent) :?>

	<h3><?php echo ucfirst($parent); ?></h3>

	<ul id="efContainer<?php echo $parent?>" class="efContainer" style="clear:both;overflow:hidden;" data-parent="<?php echo $parent?>">
	
		<?php foreach($extend_fields as $extend) :?>

			<?php if($extend['parent'] == $parent) :?>

			<li class="sortme extend_field<?php echo $extend['id_extend_field']; ?>" id="extend_field_<?php echo $extend['id_extend_field']; ?>" data-id="<?php echo $extend['id_extend_field']; ?>">
				<?php if(Authority::can('delete', 'admin/extend')) :?>
					<a class="icon delete right" data-id="<?php echo $extend['id_extend_field']; ?>"></a>
				<?php endif;?>
				<?php if($extend['global'] == '1') :?><span class="right lite mr10"><?php echo lang('ionize_label_extend_field_global'); ?></span><?php endif ;?>
				<span class="icon left drag"></span>
				<a class="left ml5 edit" data-id="<?php echo $extend['id_extend_field']; ?>" title="<?php echo lang('ionize_title_element_field_edit') ?> : <?php echo $extend['label']; ?>"><?php echo $extend['name']; ?> | <?php echo $extend['label']; ?></a>
			</li>

			<?php endif ;?>

		<?php endforeach ;?>
	
	</ul>

<?php endforeach ;?>


<script type="text/javascript">

	$$('.efContainer').each(function(item)
	{
		var parent = item.getProperty('data-parent');

		var efManager = new ION.ItemManager(
		{
			parent: 	parent,
			element: 	'extend_field',
			container: 	'efContainer' + parent
		});

		efManager.makeSortable();

		<?php if(Authority::can('edit', 'admin/extend')) :?>
			item.getChildren('li a.edit').each(function(item)
			{
				var id = item.getProperty('data-id');

				item.addEvent('click', function()
				{
					ION.formWindow('extendfield' + id, 'extendfieldForm'+id, '<?php echo lang('ionize_title_extend_field'); ?>', 'extend_field/edit/' + id, {width: 400, height: 400});
				});
			});
		<?php endif;?>
	});

</script>
