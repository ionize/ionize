
<?php if( ! empty($page)) :?>

	<?php
	
		$id = $page['id_page'];
		$title = ($page['title'] != '') ? $page['title'] : $page['name'];
		$status = (!$page['online']) ? 'offline' : 'online' ;
	?>
	
	<ul class="sortable-container ml15" id="maintenancePageList">
	
		<li class="sortme" rel="<?= $id ?>">
	
			<!-- Unlink icon -->
			<a class="icon unlink right" rel="<?= $id ?>"></a>
	
			<!-- Title (draggable) -->
			<a style="overflow:hidden;height:16px;display:block;" class="pl5 pr10 page page<?= $id ?> <?= $status ;?>" title="<?= lang('ionize_label_edit') ?>" rel="<?= $id ?>"><?= $title ?></a>

		</li>

	</ul>

<?php else :?>

	<div class="droppable h40 ml15 dropPageAsMaintenancePage">

		<sapn class="lite"><?= lang('ionize_drop_maintenance_page_here') ?></span>
	
	</div>

<?php endif ;?>



<script type="text/javascript">
	
	$$('#maintenancePageList li .unlink').each(function(item)
	{
		ION.initRequestEvent(item, 'setting/set_maintenance_page', {}, {'update':'maintenancePageContainer'}, 'HTML');
	});

</script>
