
<?php if( ! empty($page)) :?>

	<?php
	
		$id = $page['id_page'];
		$title = ($page['title'] != '') ? $page['title'] : $page['name'];
		$status = (!$page['online']) ? 'offline' : 'online' ;
	?>
	
	<ul class="sortable-container" id="maintenancePageList">
	
		<li class="sortme" data-id="<?php echo $id; ?>">
	
			<!-- Unlink icon -->
			<a class="icon unlink right" data-id="<?php echo $id; ?>"></a>
	
			<!-- Title (draggable) -->
			<a class="pl5 pr10 page page<?php echo $id; ?> <?php echo $status ;?>" title="<?php echo lang('ionize_label_edit'); ?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></a>

		</li>

	</ul>

<?php else :?>

	<div class="droppable h40 dropPageAsMaintenancePage">

		<span class="lite"><?php echo lang('ionize_drop_maintenance_page_here'); ?></span>
	
	</div>

<?php endif ;?>



<script type="text/javascript">
	
	$$('#maintenancePageList li .unlink').each(function(item)
	{
		ION.initRequestEvent(item, 'setting/set_maintenance_page', {}, {'update':'maintenancePageContainer'}, 'HTML');
	});

</script>
