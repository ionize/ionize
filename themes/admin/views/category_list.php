<?php

/**
 * Displays the article's categories list
 * Called through XHR by : /views/articles.php
 *
 */

?>

<ul id="categoryList" class="mb20">

<?php foreach($categories as $category) :?>

	<li class="sortme category<?= $category['id_category'] ?>" id="category_<?= $category['id_category'] ?>" rel="<?= $category['id_category'] ?>">
		<a class="icon delete right" rel="<?= $category['id_category'] ?>"></a>
		<img class="icon left drag pr5" src="<?= theme_url() ?>images/icon_16_ordering.png" />
		<a class="left pl5 title" rel="<?= $category['id_category'] ?>"><?= $category['name'] ?></a>
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
		var rel = item.getProperty('rel');
		
		item.addEvent('click', function(e){
			MUI.formWindow('category' + rel, 'categoryForm' + rel, Lang.get('ionize_title_category_edit'), 'category/edit/' + rel);	
		});
	});

</script>
