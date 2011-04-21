<?php

/**
 * View used by extend_field controller to display again the extend fields table after an ADD / DELETE of one extend field
 *
 */

$title = ($parent !== FALSE) ? lang('ionize_label_'.$parent) : lang('ionize_label_extend_field_for_all');

?>

<?php if( !empty($extend_fields)) :?>

	<h3><?= $title ?></h3>

	<ul id="extendfieldsContainer<?=$parent?>" style="clear:both;overflow:hidden;">
	
	<?php foreach($extend_fields as $extend_field) :?>
	
		<li class="sortme extend_field<?= $extend_field['id_extend_field'] ?>" id="extend_field_<?= $extend_field['id_extend_field'] ?>" rel="<?= $extend_field['id_extend_field'] ?>">
			<a class="icon delete right" rel="<?= $extend_field['id_extend_field'] ?>"></a>
			<?php if($extend_field['global'] == '1') :?><span class="right lite mr10"><?=lang('ionize_label_extend_field_global')?></span><?php endif ;?>
			<img class="icon left drag" src="<?= theme_url() ?>images/icon_16_ordering.png" />
			<a class="left pl5 edit" rel="<?= $extend_field['id_extend_field'] ?>" title="<?= $extend_field['name'] ?> : <?= $extend_field['description'] ?>"><?= $extend_field['name'] ?> | <?= $extend_field['label'] ?></a>
		</li>
	
	<?php endforeach ;?>
	
	</ul>
	
	<script type="text/javascript">
	
		extendfieldsManager<?=$parent?> = new ION.ItemManager(
		{
			parent: 	'<?=$parent?>',
			element: 	'extend_field',
			container: 	'extendfieldsContainer<?=$parent?>'
		});
		
		extendfieldsManager<?=$parent?>.makeSortable();
		
		$$('#extendfieldsContainer<?=$parent?> li a.edit').each(function(item, idx)
		{
			var id = item.getProperty('rel');
			
			item.addEvent('click', function()
			{
				ION.formWindow('extendfield' + id, 'extendfieldForm'+id, '<?= lang('ionize_title_extend_field') ?>', 'extend_field/edit/' + id, {width: 400, height: 400});
			});
		});
	
	
	</script>
	

<?php endif; ?>
