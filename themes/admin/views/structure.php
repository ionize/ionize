<?php

/*
 * Img folder global variable
 * Mandatory for the recursive function getTree()
 */
$GLOBALS['theme_img_folder'] = theme_url().'images';

/** 
 * Returns the pages tree as pure HTML list
 */
function getTree($items, $first = false, $id_item=false, $root_id = '')
{
	$param = $root = '';
	
	if($first == true)
	{
		$param = ' id="'. $id_item .'" class="tree pageContainer"';
		$root = ' root';
		$root_id = $id_item;
		$rel = '0';
	}
	else {
		$rel = $id_item;
		$param = ' id="pageContainer'. $id_item .'" class="pageContainer"';
	}
	
	$tree = '<ul'.$param.' rel="'.$rel.'">';
	
	foreach($items as $key => $item)
	{
		// Online status
		$status = ( $item['online'] === '0' ) ? 'offline' : 'online';
		
		// Home ?
		$home = ( $item['home'] === '1' ) ? ' home' : '';
		
		$title = ($item['title'] != '') ? $item['title'] : $item['name'];

		// Name
		$tree .= '<li id="page_'.$item['id_page'].'" class="folder'.$root.$home.' page'.$item['id_page'].' page '.$status.'" rel="'.$item['id_page'].'">'
		
		/*
		 * ID : 	pl'.$item['id_page'] : 				used by callback to update name
		 * class : 	article'.$article['id_article'] : 	used to delete element
		 * rel :	$item['id_page'] :					used for drag / drop functionnality
		 *
		 */
		.'<span><a class="title page'.$item['id_page'].' '.$status.'" rel="'.$item['id_page'].'" title="'.$title.'">'.$title.'</a></span>'


		.'<span class="action">'
			// Online / Offline
			.'<span class="icon"><a title="'.lang('ionize_button_switch_online').'" class="status '.$status.' page'.$item['id_page'].'" rel="'.$item['id_page'].'"></a></span>'

			// Add article 
			.'<span class="icon"><a class="addArticle article" title="'.lang('ionize_title_create_article').'" rel="'.$item['id_page'].'"></a></span>'
		.'</span>';
		
		
		// Get folders
		if (!empty($item['children']))
			 $tree .= getTree($item['children'], false, $item['id_page'], $root_id);

		// Get files
		if (!empty($item['articles']))
		{
			$tree.= '<ul id="articleContainer'.$item['id_page'].'" class="articleContainer" rel="'.$item['id_page'].'">';
			
			foreach($item['articles'] as $article)
			{
				$status = ( $article['online'] == '1' ) ? ' online ' : ' offline ';
				
				$title = (trim($article['title']) != '') ? $article['title'] : $article['name'];
				$title = character_limiter($title, 40);
				$class = ($article['indexed'] == 1) ? ' doc ' : ' sticky ';
				
				$rel = $article['id_page'].'.'.$article['id_article'];
				
				$flat_rel = $article['id_page'].'x'.$article['id_article'];
				
				$flag = ($article['flag'] != 0) ? $article['flag'] : $article['type_flag'];
				
				/*
				 * class : article'.$article['id_article'] : used to delete element
				 *
				 */
				$tree.= '<li id="article_'.$flat_rel.'" class="file'.$class.$status.' article'.$article['id_article'].' article'.$flat_rel.'" rel="'. $rel .'">'

					// Edit link. 
					// rel : needed for drag / drop functionnality. ID article in this case
					.'<span class="title_item"><a class="title article'.$article['id_article'].' article'.$flat_rel.' '.$status.'"  title="'.$title.'" rel="'.$rel.'"><span class="flag flag'. $flag .'"></span>'. $title .'</a></span>'
				

					.'<span class="action">'
						// Online / Offline
						// rel : needed for swith online/offline in the page context
						.'<span class="icon"><a class="status '.$status.' article'.$article['id_article'].' article'.$flat_rel.'" rel="'. $rel .'"></a></span>'

						// Unlink icon
						.'<span class="icon"><a class="unlink" rel="' . $rel . '"></a></span>'
					
					.'</span>'
					
					
				.'</li>';
			}
			$tree.= '</ul>';
		}
		$tree.= '</li>';
	}
	
	$tree .= '</ul>';

	return $tree;
}

?>

<!-- Menus -->
<?php foreach($menus as $menu) :?>

	<h3 class="treetitle" rel="<?= $menu['id_menu'] ?>">
		<span class="action">
			<a title="" class="icon edit right ml5"></a>
			<a title="<?= lang('ionize_help_add_page_to_menu') ?>" class="icon right ml5 add_page" rel="<?= $menu['id_menu'] ?>"></a>
		</span>
		<?= $menu['title'] ?>
	</h3>
	<?= getTree($menu['items'], true, $menu['name'].'Tree') ?>

<?php endforeach ;?>


<!-- Events -->

<script type="text/javascript">

	/** Build the menus trees
	 *
	 */
	<?php foreach($menus as $menu) :?>
		var <?= $menu['name'] ?>Tree = new ION.Tree('<?= $menu['name'] ?>Tree');
	<?php endforeach ;?>

	
	<?php if($this->connect->is('admins')) :?>
	
	/** Add links to each menu title
	 *
	 */
	$$('.treetitle').each(function(el)
	{
		ION.initTreeTitle(el);
	});
	
	<?php endif; ?>

</script>
