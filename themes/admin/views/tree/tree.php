
<!-- Menus -->
<?php foreach($menus as $menu) :?>

	<h3 class="treetitle" rel="<?php echo $menu['id_menu']; ?>">
		<span class="action">
			<a title="" class="icon edit right ml5"></a>
			<a title="<?php echo lang('ionize_help_add_page_to_menu'); ?>" class="icon right ml5 add_page" rel="<?php echo $menu['id_menu']; ?>"></a>
		</span>
		<?php echo $menu['title']; ?>
	</h3>
	<div class="treeContainer" id="<?php echo $menu['name'].'Tree'; ?>" data-id-menu="<?php echo $menu['id_menu']; ?>"></div>

<?php endforeach ;?>


<!-- Trees -->
<script type="text/javascript">

	// Build the menus trees
	<?php foreach($menus as $menu) :?>
		var <?php echo $menu['name']; ?>Tree = new ION.TreeXhr('<?php echo $menu['name']; ?>Tree', '<?php echo $menu['id_menu']; ?>');
	<?php endforeach ;?>

	// Add links to each menu title
	$$('.treetitle').each(function(el)
	{
		ION.initTreeTitle(el);
	});
	
</script>
