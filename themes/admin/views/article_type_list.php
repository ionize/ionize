<?php

/**
 * Displays the article's type list
 * Called through XHR by : /views/articles.php
 *
 */

?>

<ul id="article_typeList">

<?php foreach($types as $type) :?>

	<li class="sortme article_type<?= $type['id_type'] ?>" id="article_type_<?= $type['id_type'] ?>" rel="<?= $type['id_type'] ?>">
		<a class="icon delete right" rel="<?= $type['id_type'] ?>"></a>
		<img class="icon left drag pr5" src="<?= theme_url() ?>images/icon_16_ordering.png" />
		<a class="left pl5 title" rel="<?= $type['id_type'] ?>">
			<span class="flag flag<?= $type['type_flag'] ?>"></span>
			<?= $type['type'] ?>
		</a>
	</li>

<?php endforeach ;?>

</ul>


<script type="text/javascript">

	/**
	 * Types list itemManager
	 *
	 */
	typesManager = new ION.ItemManager({ element: 'article_type', container: 'article_typeList' });
	
	typesManager.makeSortable();

	// Make all types editable
	$$('#article_typeList .title').each(function(item, idx)
	{
		var rel = item.getProperty('rel');
		
		item.addEvent('click', function(e){
			MUI.formWindow('article_type' + rel, 'article_typeForm' + rel, Lang.get('ionize_title_type_edit'), 'article_type/edit/' + rel);	
		});
	});

</script>
