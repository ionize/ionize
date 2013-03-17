<?php

/**
 * Displays the article's categories list
 * Called through XHR by : /views/articles.php
 *
 */

?>

<ul id="tagList" class="mb20 sortable-container">

<?php foreach($tags as $tag) :?>

	<li class="sortme tag<?php echo $tag['id_tag']; ?>" id="tag_<?php echo $tag['id_tag']; ?>" data-id="<?php echo $tag['id_tag']; ?>">
		<a class="icon delete right" data-id="<?php echo $tag['id_tag']; ?>"></a>
		<a class="left pl5 title" data-id="<?php echo $tag['id_tag']; ?>"><?php echo $tag['tag']; ?></a>
	</li>
<?php endforeach ;?>

</ul>

<script type="text/javascript">

	// Tags list manager
	tagManager = new ION.ItemManager({ element: 'tag', container: 'tagList' });

	// Make all categories editable
	$$('#tagList .title').each(function(item, idx)
	{
		var id = item.getProperty('data-id');
		
		item.addEvent('click', function(e)
		{
			ION.formWindow(
				'tag' + id,
				'tagForm' + id,
				Lang.get('ionize_title_tag_edit'),
				'tag/edit/' + id
			);
		});
	});

</script>
