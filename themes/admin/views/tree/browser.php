
<!-- Menus -->
<?php foreach($menus as $menu) :?>

	<h3 rel="<?php echo $menu['id_menu'] ?>">
		<?php echo $menu['title']; ?>
	</h3>
	<div class="treeContainer" id="browser_<?php echo $menu['name'].'Tree'; ?>"></div>

<?php endforeach ;?>

<!-- Trees -->
<script type="text/javascript">

    // Build the menus trees
	<?php foreach($menus as $menu) :?>
		var browser_<?php echo $menu['name']; ?>Tree = new ION.BrowserTreeXhr('browser_<?php echo $menu['name']; ?>Tree', '<?php echo $menu['id_menu']; ?>');
	<?php endforeach ;?>

</script>
