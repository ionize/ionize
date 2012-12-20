<<<<<<< HEAD
<?php if( ! empty($pages)): ?>

	<ul class="sortable-container mr20" id="rssPageList">
	
		<?php foreach($pages as $page): ?>
			
			<?php
			$title = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
			?>
			
			<li class="sortme">
		
				<a class="left link-img page" ></a>
		
				<!-- Unlink icon -->
				<a class="icon unlink right" rel="<?php echo $page['id_page']; ?>"></a>
		
				<!-- Title -->
				<a id="link_title" style="overflow:hidden;height:16px;display:block;" class="pl5 pr10" title="<?php echo $title; ?>"><?php echo $title; ?></a>
		
			</li>
		
		<?php endforeach; ?>
	
	</ul>

<?php endif ;?>

<script type="text/javascript">
/**
* Unlink event on each page
*/
$$('#rssPageList li .unlink').each(function(item)
{
	ION.initRequestEvent(item, '<?php echo admin_url() ?>module/rss/rss/remove_page', {'id_page': item.getProperty('rel')});
});
</script>
=======
<?php if( ! empty($pages)): ?>

	<ul class="sortable-container mr20" id="rssPageList">
	
		<?php foreach($pages as $page): ?>
			
			<?php
			$title = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
			?>
			
			<li class="sortme">
		
				<a class="left link-img page" ></a>
		
				<!-- Unlink icon -->
				<a class="icon unlink right" rel="<?php echo $page['id_page']; ?>"></a>
		
				<!-- Title -->
				<a id="link_title" style="overflow:hidden;height:16px;display:block;" class="pl5 pr10" title="<?php echo $title; ?>"><?php echo $title; ?></a>
		
			</li>
		
		<?php endforeach; ?>
	
	</ul>

<?php endif ;?>

<script type="text/javascript">
/**
* Unlink event on each page
*/
$$('#rssPageList li .unlink').each(function(item)
{
	ION.initRequestEvent(item, '<?php echo admin_url() ?>module/rss/rss/remove_page', {'id_page': item.getProperty('rel')});
});
</script>
>>>>>>> 37ae275c480b6d3e0b24d07a92920ce8f2b8b12e
