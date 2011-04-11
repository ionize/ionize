<?php

/**
 * Displays the list of all users
 *
 */
?>


<div id="maincolumn">

	<h2 class="main groups" id="main-title"><?= lang('ionize_title_users') ?></h2>

	<!-- Tabs -->
	<div id="usersTab" class="mainTabs">
		<ul class="tab-menu">
			<li id="usersListTab"><a><?= lang('ionize_title_existing_users') ?></a></li>
			<li><a><?= lang('ionize_title_existing_groups') ?></a></li>
			<li><a><?= lang('ionize_title_users_export') ?></a></li>
		</ul>
		<div class="clear"></div>
	</div>

	<!-- Existing users table -->
	<div id="usersTabContent">
	
		<div class="tabcontent">
			
			<div class="tabsidecolumn">
			
				<!-- Infos about all users -->
				<div class="info mb10">
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_users_count') ?></label></dt>
						<dd><?= $users_count_all ?></dd>
					</dl>
					<dl class="small compact">
						<dt><label><?= lang('ionize_label_last_registered') ?></label></dt>
						<dd></dd>
					</dl>
				</div>
	
	
				<h3 class="toggler1"><?= lang('ionize_title_filter_userslist') ?></h3>
	
				<div class="element1">
				
					<!-- User list filter -->
					<form name="usersFilter" id="usersFilter" method="post"action="<?= admin_url() ?>users/users_list">
					
						<!-- Users / page -->
						<dl class="small">
							<dt><label for="filter_nb"><?= lang('ionize_label_users_per_page') ?></label></dt>
							<dd><input type="text" class="inputtext w60" id="filter_nb" name="nb" value="50" /></dd>
						</dl>
		
						<!-- Group -->
						<dl class="small">
							<dt><label for="filter_id_group"><?= lang('ionize_label_group') ?></label></dt>
							<dd>
								<select name="slug"  id="filter_slug" class="select">
									<option value=""><?= lang('ionize_label_all_groups') ?></option>
									<?php foreach($groups as $group) :?>
									
										<option value="<?= $group['slug'] ?>"><?= $group['group_name'] ?></option>
									
									<?php endforeach ;?>
								</select>
							</dd>
						</dl>
		
						<!-- ID -->
						<dl class="small">
							<dt><label for="filter_username"><?= lang('ionize_label_username') ?></label></dt>
							<dd><input type="text" class="inputtext w140" id="filter_username" name="username" value="" /></dd>
						</dl>
						
						
						<!-- Screen name -->
						<dl class="small">
							<dt><label for="filter_screenname"><?= lang('ionize_label_screen_name') ?></label></dt>
							<dd><input type="text" class="inputtext w140" id="filter_screenname" name="screenname" value="" /></dd>
						</dl>
						
						<!-- Email -->
						<dl class="small">
							<dt><label for="filter_email"><?= lang('ionize_label_email') ?></label></dt>
							<dd><input type="text" class="inputtext w140" id="filter_email" name="email" value="" /></dd>
						</dl>
						
						<!-- Last registered -->
						<dl class="small">
							<dt><label for="filter_registered"><?= lang('ionize_label_last_registered') ?></label></dt>
							<dd><input type="checkbox" class="inputcheckbox" id="filter_registered" name="registered" value="1" /></dd>
						</dl>
						
						
						
						<!-- Submit -->
						<dl class="small">
							<dt>&#160;</dt>
							<dd>
								<input id="submit_filter" type="submit" class="submit" value="<?= lang('ionize_button_filter') ?>" />
							</dd>
						</dl>
					</form>
				</div>
	
	
				<h3 class="toggler1"><?= lang('ionize_title_add_user') ?></h3>
				
				<div class="element1">
					
					<form name="newUserForm" id="newUserForm" method="post" action="<?= admin_url() ?>users/save">
		
						<!-- Username -->
						<dl class="small">
							<dt>
								<label for="username"><?=lang('ionize_label_username')?></label>
							</dt>
							<dd>
								<input id="username" name="username" class="inputtext w140" type="text" value="" />
							</dd>
						</dl>
						
						<!-- Screen name -->
						<dl class="small">
							<dt>
								<label for="screen_name"><?=lang('ionize_label_screen_name')?></label>
							</dt>
							<dd>
								<input id="screen_name" name="screen_name" class="inputtext w140" type="text" value="" />
							</dd>
						</dl>
						
						<!-- Group -->
						<dl class="small">
							<dt>
								<label for="group_FK"><?=lang('ionize_label_group')?></label>
							</dt>
							<dd>
								<select name="id_group" class="select">
									<?php foreach($groups as $group) :?>
									
										<option value="<?= $group['id_group'] ?>"><?= $group['group_name'] ?></option>
									
									<?php endforeach ;?>
								</select>
							</dd>
						</dl>
						
						<!-- Email -->
						<dl class="small">
							<dt>
								<label for="email"><?=lang('ionize_label_email')?></label>
							</dt>
							<dd>
								<input id="email" name="email" class="inputtext w140" type="text" value="" />
							</dd>
						</dl>
						
						<!-- Password -->
						<dl class="small">
							<dt>
								<label for="password"><?=lang('ionize_label_password')?></label>
							</dt>
							<dd>
								<input id="password" name="password" class="inputtext w120" type="password" value="" />
							</dd>
						</dl>
		
						<!-- Password confirm -->
						<dl class="small">
							<dt>
								<label for="password2"><?=lang('ionize_label_password2')?></label>
							</dt>
							<dd>
								<input id="password2" name="password2" class="inputtext w120" type="password" value="" />
							</dd>
						</dl>
		
						<!-- Submit button  -->
						<dl class="small">
							<dt>&#160;</dt>
							<dd>
								<input id="submit_new_user" type="submit" class="submit" value="<?= lang('ionize_button_save') ?>" />
							</dd>
						</dl>
		
					</form>
				</div>
			</div>
				
			<!-- Users list -->
			<div class="tabcolumn">
				<div id="usersList"></div>
			</div>
		
		</div>
	
	
		<!-- Existing groups table -->
		<div class="tabcontent">
	
			<!-- New group -->
			<div class="tabsidecolumn">
			
				<h3><?= lang('ionize_title_add_group') ?></h3>
				
				<form name="newGroupForm" id="newGroupForm" method="post" action="<?= admin_url() ?>groups/save">
	
					<!-- Group name -->
					<dl class="small">
						<dt>
							<label for="slug"><?=lang('ionize_label_group_name')?></label>
						</dt>
						<dd>
							<input id="slug" name="slug" class="inputtext w140" type="text" value="" />
						</dd>
					</dl>
					
					<!-- Group Title -->
					<dl class="small">
						<dt>
							<label for="group_name"><?=lang('ionize_label_group_title')?></label>
						</dt>
						<dd>
							<input id="group_name" name="group_name" class="inputtext w140" type="text" value="" />
						</dd>
					</dl>
					
					<!-- Description -->
					<dl class="small">
						<dt>
							<label for="description"><?=lang('ionize_label_group_description')?></label>
						</dt>
						<dd>
							<input id="description" name="description" class="inputtext w140" type="text" value="" />
						</dd>
					</dl>
					
					<!-- Level -->
					<dl class="small">
						<dt>
							<label for="level"><?=lang('ionize_label_group_level')?></label>
						</dt>
						<dd>
							<select name="level" class="select">
								<?php foreach($groups as $group) :?>
								
									<option value="<?= $group['level'] ?>"><?= $group['group_name'] ?></option>
								
								<?php endforeach ;?>
							</select>
						</dd>
					</dl>
					
					<!-- Submit button  -->
					<dl class="small">
						<dt>&#160;</dt>
						<dd>
							<input id="submit_new_group" type="submit" class="submit" value="<?= lang('ionize_button_save') ?>" />
						</dd>
					</dl>
	
				</form>
			</div>
	
			<!-- Groups list -->
			<div class="tabcolumn">
				<table class="list" id="groupsTable">
			
					<thead>
						<tr>
							<th axis="string"><?= lang('ionize_label_id') ?></th>
							<th axis="string"><?= lang('ionize_label_group_name') ?></th>
							<th axis="string"><?= lang('ionize_label_group_title') ?></th>
							<th axis="string"><?= lang('ionize_label_group_level') ?></th>
							<th axis="string"><?= lang('ionize_label_group_description') ?></th>				
							<th></th>
						</tr>
					</thead>
			
					<tbody>
					
					<?php foreach($groups as $group) :?>
						
						<tr class="groups<?= $group['id_group'] ?>">
							<td><?= $group['id_group'] ?></td>
							<td><a class="group" id="group<?= $group['id_group'] ?>" rel="<?= $group['id_group'] ?>" href="<?= admin_url() ?>groups/edit/<?= $group['id_group'] ?>"><?= $group['slug'] ?></a></td>
							<td><?= $group['group_name'] ?></td>
							<td><?= $group['level'] ?></td>
							<td><?= $group['description'] ?></td>
							<td>
								<?php if( $current_user_level > $group['level']) :?>
									<a class="icon delete" rel="<?= $group['id_group'] ?>"></a>
								<?php endif ;?>
							</td>
						</tr>
			
					<?php endforeach ;?>
					
					</tbody>
			
				</table>
			</div>
		</div>
	
	
		<!-- Export tool -->
		<div class="tabcontent">
		
			<form name="userExportForm" id="userExportForm" method="post" action="<?= admin_url() ?>users/export">
	
				<dl>
					<dt><?= lang('ionize_label_export_meta') ?></dt>
					
					<?php foreach($meta_data as $meta) :?>
					<dd>
							<input id="meta_<?= $meta['Field'] ?>" name="metas[]" type="checkbox" value="<?= $meta['Field'] ?>" />
							<label for="meta_<?= $meta['Field'] ?>"><?= $meta['Field'] ?></label>
							<br/>
					</dd>
					<?php endforeach ;?>
					
				</dl>
				
				<dl>
					<dt><?= lang('ionize_label_export_format') ?></dt>
					<dd>
						<input id="format" name="format" type="radio" checked="checked" value="csv" />
						<label for="format">CSV</label>
					</dd>
				</dl>
	
				<dl>
					<dt>&#160;</dt>
					<dd>
						<input id="submit_user_export" type="submit" class="submit" value="<?= lang('ionize_button_export') ?>" />
					</dd>
				</dl>
				
			</form>
		</div>
	
	</div>

</div> <!-- /maincolumn -->


<script type="text/javascript">

	/**
	 * Panel toolbox
	 *
	 */
	MUI.initToolbox('empty_toolbox');


	/**
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler1', 'div.element1');


	/** 
	 * Tabs init
	 *
	 */
	var usersTabSwapper = new TabSwapper({tabsContainer: 'usersTab', sectionsContainer: 'usersTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'usersTab' });

	// Users list tab
	$('usersListTab').addEvent('click', function()
	{
		if ( ! this.retrieve('loaded'))
		{ 
			MUI.updateElement({
				'url': 'users/users_list',
				'element': 'usersList'
			});
		}
	});
	$('usersListTab').fireEvent('click');


	/**
	 * Init help tips on label
	 *
	 */
	MUI.initLabelHelpLinks('#newUserForm');
	MUI.initLabelHelpLinks('#newGroupForm');
	MUI.initLabelHelpLinks('#userExportForm');

	/**
	 * New user form action
	 * see init.js for more information about this method
	 */
	MUI.setFormSubmit('newUserForm', 'submit_new_user', 'users/save');
	MUI.setFormSubmit('newGroupForm', 'submit_new_group', 'groups/save');
//	MUI.setFormSubmit('userExportForm', 'submit_user_export', $('userExportForm').action);

	/**
	 * Filter users list
	 *
	 */
	$('submit_filter').addEvent('click', function(e)
	{
		e.stop();
		
		new Request.HTML({
			url: admin_url + 'users/users_list',
			method: 'post',
			loadMethod: 'xhr',
			data: $('usersFilter'),
			update: $('usersList'),
			onRequest: function() { MUI.showSpinner(); },
			onFailure: function(xhr) { MUI.hideSpinner();},
			onComplete: function() { MUI.hideSpinner();}
		}).send();
	});


	/**
	 * Events to each group
	 * Opens an edition window
	 */
	$$('.group').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			var e = new Event(e).stop();
			var id = item.getProperty('rel');
			MUI.formWindow(	
				id, 							// object ID
				'groupForm',					// Form ID
				'ionize_title_group_edit', 		// Window title
				'groups/edit/' + id,			// Window content URL
				{width: 340, height: 230}		// Window options
			);
		});
	});


	/**
	 * Groups itemManager
	 *
	 */
	groupsManager = new ION.ItemManager(
	{
		container: 'groupsTable',
		element: 'groups'
	});

	
	/**
	 * Adds Sortable function to the user list table
	 *
	 */
	new SortableTable('groupsTable',{sortOn: 3, sortBy: 'ASC'});

</script>





