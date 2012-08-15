<?php

/**
 * View used by extend_field controller to display again the extend fields table after an ADD / DELETE of one extend field
 *
 */

?>

<?php foreach($parents as $parent) :?>

	<h3><?= ucfirst($parent) ?></h3>

	<ul id="efContainer<?=$parent?>" class="efContainer" style="clear:both;overflow:hidden;" rel="<?=$parent?>">
	
	<?php foreach($extend_fields as $extend) :?>
	
		<?php if($extend['parent'] == $parent) :?>
	
		<li class="sortme extend_field<?= $extend['id_extend_field'] ?>" id="extend_field_<?= $extend['id_extend_field'] ?>" rel="<?= $extend['id_extend_field'] ?>">
			<a class="icon delete right" rel="<?= $extend['id_extend_field'] ?>"></a>
			<?php if($extend['global'] == '1') :?><span class="right lite mr10"><?=lang('ionize_label_extend_field_global')?></span><?php endif ;?>
			<span class="icon left drag"></span>
			<a class="left ml5 edit" rel="<?= $extend['id_extend_field'] ?>" title="<?= $extend['name'] ?> : <?= $extend['description'] ?>"><?= $extend['name'] ?> | <?= $extend['label'] ?></a>
		</li>
		
		<?php endif ;?>
	
	<?php endforeach ;?>
	
	</ul>

<?php endforeach ;?>


<script type="text/javascript">

	$$('.efContainer').each(function(item)
	{
		var rel = item.getProperty('rel');
		
		var efManager = new ION.ItemManager(
		{
			parent: 	rel,
			element: 	'extend_field',
			container: 	'efContainer' + rel
		});

		efManager.makeSortable();
		
		item.getChildren('li a.edit').each(function(item, idx)
		{
			var id = item.getProperty('rel');
			
			item.addEvent('click', function()
			{
				ION.formWindow('extendfield' + id, 'extendfieldForm'+id, '<?= lang('ionize_title_extend_field') ?>', 'extend_field/edit/' + id, {width: 400, height: 400});
			});
		});

	});

</script>

	
	

