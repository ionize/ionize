<?php
/**
 * Menu view
 *
 */
?>

<div id="maincolumn">

    <h2 class="main tree" id="main-title"><?php echo lang('ionize_menu_menu') ?></h2>

    <form name="existingMenuForm" id="existingMenuForm" method="post" action="<?php echo admin_url(); ?>menu/update">

	<!-- Sortable UL -->
	<ul id="menuContainer" class="sortable">

		<?php foreach($menus as $menu) :?>

			<?php
				$name = $menu['name'];
				$id = $menu['id_menu'];
				$title = $menu['title'];
			?>

			<li id="menu_<?php echo $id; ?>" class="sortme" data-id="<?php echo $id; ?>">



				<a class="button yes right saveMenu" data-id="<?php echo $id; ?>">
					<?php echo lang('ionize_button_save'); ?>
				</a>



				<!-- Drag icon -->
				<div class="drag left">
					<img src="<?php echo theme_url(); ?>images/icon_16_ordering.png" />
				</div>

                <!-- Delete button -->
				<?php if($id > 2 && Authority::can('delete', 'admin/menu')) :?>
                	<a class="icon right delete" data-id="<?php echo $id; ?>"></a>
				<?php endif ;?>

				<!-- Internal ID -->
				<dl class="small">
					<dt>
						<label><?php echo lang('ionize_label_internal_id'); ?></label>
					</dt>
					<dd><?php echo $id; ?></dd>
				</dl>

				<!-- Name -->
				<dl class="small">
					<dt>
						<label for="name_<?php echo $id; ?>"><?php echo lang('ionize_label_name'); ?></label>
					</dt>
					<dd>
						<?php if($id < 3) :?> 
							<input type="text" disabled="disabled" value="<?php echo $name; ?>"  class="inputtext" />
						<?php endif ;?>
						
						<input type="<?php if($id < 3) :?>hidden<?php else :?>text<?php endif ;?>" name="name_<?php echo $id; ?>" id="name_<?php echo $id; ?>" class="inputtext" value="<?php echo $name; ?>"/>
						
					</dd>
				</dl>

				<!-- Title -->
				<dl class="small">
					<dt>
						<label for="title_<?php echo $id; ?>"><?php echo lang('ionize_label_title'); ?></label>
					</dt>
					<dd>
						<input name="title_<?php echo $id; ?>" id="title_<?php echo $id; ?>" class="inputtext" type="text" value="<?php echo $title; ?>"/>
					</dd>
				</dl>
				


				<?php if(Authority::can('access', 'admin/menu/permissions/backend')) :?>

					<?php if ( ! empty($menu['backend_roles_resources'])): ?>

						<dl class="small">
							<dt><label><?php echo lang('ionize_label_can_see_backend'); ?></label></dt>
							<dd>
								<?php foreach($menu['backend_roles_resources'] as $id_role => $role_resources): ?>
									<div id="roleRulesContainer<?php echo $id; ?>_<?php echo $id_role ?>"></div>
								<?php endforeach;?>
							</dd>
						</dl>

						<script type="text/javascript">

							<?php foreach($menu['backend_roles_resources'] as $id_role => $role_resources): ?>

								var modRules<?php echo $id; ?>_<?php echo $id_role ?> = new ION.PermissionTree
								(
									'roleRulesContainer<?php echo $id; ?>_<?php echo $id_role ?>',
									<?php echo json_encode($role_resources['resources'], true) ?>,
									{
										'cb_name':'backend_rule[<?php echo $id_role ?>][]',
										'key': 'id_resource',
										'data': [
											{'key':'resource', 'as':'resource'},
											{'key':'title', 'as':'title'},
											{'key':'description', 'as':'description'},
											{'key':'actions', 'as':'actions'}
										],
										'rules' : <?php echo json_encode($role_resources['rules'], true) ?>
									}
								);

							<?php endforeach;?>

						</script>
					<?php endif;?>
				<?php endif ;?>
			</li>

		<?php endforeach ;?>

		</ul>

	</form>


</div> <!-- /maincolumn -->


<script type="text/javascript">
	
	// Toolbox
	ION.initToolbox('menu_toolbox');

	// Menu manager
	menuManager = new ION.ItemManager(
	{
		element: 	'menu',
		container: 	'menuContainer'		
	});
	
	menuManager.makeSortable();


</script>



