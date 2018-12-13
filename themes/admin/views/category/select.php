<?php
/**
 * Categories select form
 * Used by article's option panel
 *
 */
?>

<?php echo $categories; ?>

<script type="text/javascript">

	// Categories
	var el_categories = $('categories');
	var categoriesSelect = el_categories.getFirst('select');
	categoriesSelect.addEvent('change', function(e)
	{
		var ids = [];
		var sel = this;
		for (var i = 0; i < sel.options.length; i++) {
			if (sel.options[i].selected) ids.push(sel.options[i].value);
		}
		ION.JSON('article/update_categories', {'categories': ids, 'id_article': $('id_article').value});
	});
	var nbCategories = (el_categories.getElements('option')).length;
	if (nbCategories > 5)
	{
		$$('#categories select').setStyles({
			'height': (nbCategories * 15) + 'px'
		});
	}

</script>