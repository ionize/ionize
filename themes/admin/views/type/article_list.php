<?php

/**
 * Displays the article's type list
 * Called through XHR by : /views/articles.php
 *
 */

?>

<ul id="article_typeList" class="sortable-container">

<?php foreach($types as $type) :?>

	<li class="sortme article_type<?php echo $type['id_type']; ?>" id="article_type_<?php echo $type['id_type']; ?>" data-id="<?php echo $type['id_type']; ?>">
		<?php if ( Authority::can('delete', 'admin/article/type')) :?>
        	<a class="icon delete right" data-id="<?php echo $type['id_type']; ?>"></a>
		<?php endif;?>

        <span class="icon left drag mr5"></span>
		<a class="left pl5 title" data-id="<?php echo $type['id_type']; ?>">
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

	<?php if ( Authority::can('edit', 'admin/article/type')) :?>
		// Type editable
		$$('#article_typeList .title').each(function(item, idx)
		{
			var id = item.getProperty('data-id');

			item.addEvent('click', function(e){
				ION.formWindow('article_type' + id, 'article_typeForm' + id, Lang.get('ionize_title_type_edit'), 'article_type/edit/' + id);
			});
		});
	<?php endif;?>

</script>
