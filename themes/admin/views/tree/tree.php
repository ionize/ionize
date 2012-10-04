<!-- Menus -->
<?php foreach($menus as $menu) :?>

	<h3 class="treetitle" rel="<?= $menu['id_menu'] ?>">
		<span class="action">
			<a title="" class="icon edit right ml5"></a>
			<a title="<?= lang('ionize_help_add_page_to_menu') ?>" class="icon right ml5 add_page" rel="<?= $menu['id_menu'] ?>"></a>
		</span>
		<?= $menu['title'] ?>
	</h3>
	<div class="treeContainer" id="<?= $menu['name'].'Tree' ?>"></div>

<?php endforeach ;?>


<!-- Trees -->
<script type="text/javascript">

	// Build the menus trees
	<?php foreach($menus as $menu) :?>
		var <?= $menu['name'] ?>Tree = new ION.TreeXhr('<?= $menu['name'] ?>Tree', '<?= $menu['id_menu'] ?>');
	<?php endforeach ;?>

	// Add links to each menu title
	$$('.treetitle').each(function(el)
	{
		ION.initTreeTitle(el);
	});
	
</script>
