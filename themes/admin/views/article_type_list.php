<?php

/**
 * Displays the article's type list
 * Called through XHR by : /views/articles.php
 *
 */
    log_message('error', 'View File Loaded : article_type_list.php');

?>

<ul id="article_typeList" class="sortable-container">

<?php foreach($types as $type) :?>

	<li class="sortme article_type<?php echo $type['id_type']; ?>" id="article_type_<?php echo $type['id_type']; ?>" rel="<?php echo $type['id_type']; ?>">
		<a class="icon delete right" rel="<?php echo $type['id_type']; ?>"></a>
		<span class="icon left drag mr5"></span>
		<a class="left pl5 title" rel="<?php echo $type['id_type']; ?>">
			<span class="flag flag<?php echo $type['type_flag']; ?>"></span>
			<?php echo $type['type']; ?>
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
			ION.formWindow('article_type' + rel, 'article_typeForm' + rel, Lang.get('ionize_title_type_edit'), 'article_type/edit/' + rel);	
		});
	});

</script>
