<?php
/**
 * Tree view
 * Displays the website's content as a tree in one left column
 *
 */
?>
<?php if ( Authority::can('access', 'admin/tree')) :?>

	<!-- Menus -->
	<?php foreach($menus as $menu) :?>

		<?php if ( Authority::can('access', 'backend/menu/' . $menu['id_menu'], NULL, TRUE)) :?>

			<h3 class="treetitle" data-id="<?php echo $menu['id_menu']; ?>">
				<span class="action">
					<?php if (
						Authority::can('edit', 'admin/menu') &&
						Authority::can('edit', 'admin/tree/menu')

					) :?>
						<a title="" class="icon edit right ml5"></a>
					<?php endif ;?>
					<?php if (
					Authority::can('create', 'admin/page') &&
					Authority::can('add_page', 'admin/tree/menu')

					) :?>
						<a title="<?php echo lang('ionize_help_add_page_to_menu'); ?>" class="icon right ml5 add_page" data-id="<?php echo $menu['id_menu']; ?>"></a>
					<?php endif ;?>
				</span>
				<span <?php if ( Authority::resource_has_rule('backend/menu/' . $menu['id_menu'])) :?>class="locked"<?php endif ;?>
					><?php echo $menu['title']; ?>
				</span>
			</h3>
			<div class="treeContainer" id="<?php echo $menu['name'].'Tree'; ?>" data-id-menu="<?php echo $menu['id_menu']; ?>"></div>

		<?php endif ;?>

	<?php endforeach ;?>


	<!-- Trees -->
	<script type="text/javascript">

		// Build the menus trees
		<?php foreach($menus as $menu) :?>
			<?php if ( Authority::can('access', 'backend/menu/' . $menu['id_menu'], NULL, TRUE)) :?>
				var <?php echo $menu['name']; ?>Tree = new ION.TreeXhr('<?php echo $menu['name']; ?>Tree', '<?php echo $menu['id_menu']; ?>');
			<?php endif ;?>
		<?php endforeach ;?>

		// Add links to each menu title
		$$('.treetitle').each(function(el)
		{
			ION.initTreeTitle(el);
		});

	</script>
<?php endif ;?>