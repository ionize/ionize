<?php

/**
 * View used by extend_table controller to display the extend table details
 *
 */

?>

<?php if( !empty($extends)) :?>

	<ul id="extendtableContainer" style="clear:both;overflow:hidden;">
	
	<?php foreach($extends as $extend) :?>
	
		<li class="sortme extend_field<?php echo $extend->name; ?>" id="extend_field_<?php echo $extend->name; ?>" rel="<?php echo $extend->name; ?>">
			<a class="icon delete right" rel="<?php echo $extend->name; ?>"></a>
			<span class="icon left drag"></span>
			<a class="left ml5" href="javascript:void(0);" onclick="javascript:ION.formWindow('extendtable', 'extendtableForm', '<?php echo lang('ionize_title_extend_field'); ?>', 'extend_table/edit/<?php echo$table?>/<?php echo $extend->name; ?>', {width: 400, height: 330, title:Lang.get('ionize_title_extend_table_field')});" title="<?php echo $extend->name; ?>"><?php echo $extend->name; ?> | <?php echo $extend->type; ?></a>
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
