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

			<li id="menu_<?php echo $id; ?>" class="sortme" rel="<?php echo $id; ?>">

				<!-- Drag icon -->
				<div class="drag" style="float:left;">
					<img src="<?php echo theme_url(); ?>images/icon_16_ordering.png" />
				</div>

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
						
						<!-- Delete button -->
						<?php if($id > 2) :?>
							<a class="icon right delete" rel="<?php echo $id; ?>"></a>
						<?php endif ;?>
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
				
				<!-- Internal ID -->
				<dl class="small">
					<dt>
						<label><?php echo lang('ionize_label_internal_id'); ?></label>
					</dt>
					<dd><?php echo $id; ?></dd>
				</dl>

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



