<?php

/**
 * View used by extend_table controller to display the extend table details
 *
 */
?>

<?php if( !empty($extends)) :?>

	<ul id="extendtableContainer" style="clear:both;overflow:hidden;">
	
	<?php foreach($extends as $extend) :?>
	
		<li class="sortme extend_field<?= $extend->name ?>" id="extend_field_<?= $extend->name ?>" rel="<?= $extend->name ?>">
			<a class="icon delete right" rel="<?= $extend->name ?>"></a>
			<img class="icon left drag" src="<?= theme_url() ?>images/icon_16_ordering.png" />
			<a class="left pl5" href="javascript:void(0);" onclick="javascript:MUI.formWindow('extendtable', 'extendtableForm', '<?= lang('ionize_title_extend_field') ?>', 'extend_table/edit/<?=$table?>/<?= $extend->name ?>', {width: 400, height: 330, title:Lang.get('ionize_title_extend_table_field')});" title="<?= $extend->name ?>"><?= $extend->name ?> | <?= $extend->type ?></a>
		</li>
	
	<?php endforeach ;?>
	
	</ul>
	
	<script type="text/javascript">
	
		extendtableManager = new ION.ItemManager(
		{
			parent: 	'',
			element: 	'extend_field',
			container: 	'extendtableContainer'
		});
		
//		extendfieldsManager.makeSortable();
	
	
	</script>
	

<?php endif; ?>
