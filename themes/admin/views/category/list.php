<?php

/**
 * Displays the article's categories list
 * Called through XHR by : /views/articles.php
 *
 */

?>

<ul id="categoryList" class="mb20 sortable-container">

<?php foreach($categories as $category) :?>

	<li class="sortme category<?php echo $category['id_category']; ?>" id="category_<?php echo $category['id_category']; ?>" data-id="<?php echo $category['id_category']; ?>">
		<a class="icon delete right" data-id="<?php echo $category['id_category']; ?>"></a>
		<span class="icon left drag mr5"></span>
		<a class="left pl5 title" data-id="<?php echo $category['id_category']; ?>"><?php echo $category['name']; ?></a>
	</li>
<?php endforeach ;?>

</ul>

<script type="text/javascript">

	/**
	 * Categories list itemManager
	 *
	 */
	categoriesManager = new ION.ItemManager({ element: 'category', container: 'categoryList' });
	
	categoriesManager.makeSortable();

	// Make all categories editable
	$$('#categoryList .title').each(function(item, idx)
	{
		var id = item.getProperty('data-id');
		
		item.addEvent('click', function(e)
		{
			ION.formWindow('category' + id, 'categoryForm' + id, Lang.get('ionize_title_category_edit'), 'category/edit/' + id);
		});
	});

</script>
