
<div id="sidecolumn" class="close">

	<div id="options" class="mt20">

		<!-- New Menu -->
		<h3 class="toggler"><?=lang('ionize_title_add_menu')?></h3>

		<div class="element">

			<form name="newMenuForm" id="newMenuForm" method="post" action="<?= admin_url() ?>menu/save">

				<!-- Menu Name -->
				<dl class="small">
					<dt>
						<label for="name_new"><?=lang('ionize_label_name')?></label>
					</dt>
					<dd>
						<input id="name_new" name="name_new" class="inputtext w140" type="text" value="" />
					</dd>
				</dl>

				<!-- Menu Title -->
				<dl class="small">
					<dt>
						<label for="title_new"><?=lang('ionize_label_title')?></label>
					</dt>
					<dd>
						<input id="title_new" name="title_new" class="inputtext w140" type="text" value=""/><br />
					</dd>
				</dl>

				<!-- Submit button  -->
				<dl class="small">
					<dt>&#160;</dt>
					<dd>
						<input id="submit_new" type="submit" class="submit" value="<?= lang('ionize_button_save_new_menu') ?>" />
					</dd>
				</dl>
				
			</form>

		</div> <!-- /element -->

	</div> <!-- /options -->

</div> <!-- /sidecolumn -->


<!-- Main Column -->

<div id="maincolumn">

	<form name="existingMenuForm" id="existingMenuForm" method="post" action="<?= admin_url() ?>menu/update">

	<h3><?=lang('ionize_title_existing_menu')?></h3>
	

	<!-- Sortable UL -->
	<ul id="menuContainer" class="sortable">

		<?php foreach($menus as $menu) :?>

			<?php
				$name = $menu['name'];
				$id = $menu['id_menu'];
				$title = $menu['title'];
			?>

			<li id="menu_<?= $id ?>" class="sortme" rel="<?= $id ?>">

				<!-- Drag icon -->
				<div class="drag" style="float:left;height:100px;">
					<img src="<?= theme_url() ?>images/icon_16_ordering.png" />
				</div>

				<!-- Name -->
				<dl class="small">
					<dt>
						<label for="name_<?= $id ?>"><?=lang('ionize_label_name')?></label>
					</dt>
					<dd>
						<?php if($id < 3) :?> 
							<input type="text" disabled="disabled" value="<?= $name ?>"  class="inputtext" />
						<?php endif ;?>
						
						<input type="<?php if($id < 3) :?>hidden<?php else :?>text<?php endif ;?>" name="name_<?= $id ?>" id="name_<?= $id ?>" class="inputtext" value="<?= $name ?>"/>
						
						<!-- Delete button -->
						<?php if($id > 2) :?>
							<a class="icon right delete" rel="<?= $id ?>"></a>
						<?php endif ;?>
					</dd>
				</dl>

				<!-- Title -->
				<dl class="small">
					<dt>
						<label for="title_<?= $id ?>"><?=lang('ionize_label_title')?></label>
					</dt>
					<dd>
						<input name="title_<?= $id ?>" id="title_<?= $id ?>" class="inputtext" type="text" value="<?= $title ?>"/>
					</dd>
				</dl>
				
				<!-- Internal ID -->
				<dl class="small">
					<dt>
						<label><?=lang('ionize_label_internal_id')?></label>
					</dt>
					<dd><?= $id ?></dd>
				</dl>

			</li>

		<?php endforeach ;?>

		</ul>

	</form>


</div> <!-- /maincolumn -->


<script type="text/javascript">
	

	/**
	 * Form action
	 * see init-form.js for more information about this method
	 *
	 */
	ION.setFormSubmit('newMenuForm', 'submit_new', 'menu/save');

	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('menu_toolbox');

				
	ION.initAccordion('.toggler', 'div.element', true, 'menuAccordion1');
	


	/*
	 * Menu itemManager
	 * Use of ItemManager.deleteItem, etc.
	 */
	menuManager = new ION.ItemManager(
	{
		element: 	'menu',
		container: 	'menuContainer'		
	});
	
	menuManager.makeSortable();

	
</script>



