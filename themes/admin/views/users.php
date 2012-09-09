<?php

/**
 * Displays the list of all users
 *
 */
?>


<div id="maincolumn">

	<h2 class="main groups" id="main-title"><?= lang('ionize_title_users') ?></h2>

	<!-- Tabs -->
	<div id="usersTab" class="mainTabs mt20">
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
	
	
				<h3><?= lang('ionize_title_filter_userslist') ?></h3>
	

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
	

				
			<!-- Users list -->
			<div class="tabcolumn">
				<div id="usersList"></div>
			</div>
		
		</div>
	
	
		<!-- Existing groups table -->
		<div class="tabcontent">
	
			<!-- Groups list -->
			<div class="tabcolumn">
				<table class="list" id="groupsTable">
			
					<thead>
						<tr>
							<th axis="string"><?= lang('ionize_label_id') ?></th>
							<th axis="string"><?= lang('ionize_label_group_name') ?></th>
							<th axis="string"><?= lang('ionize_label_group_title') ?></th>
							<th axis="number"><?= lang('ionize_label_group_level') ?></th>
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
							<input id="meta_<?= $meta['field'] ?>" name="metas[]" type="checkbox" value="<?= $meta['field'] ?>" />
							<label for="meta_<?= $meta['field'] ?>"><?= $meta['field'] ?></label>
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
	ION.initToolbox('users_toolbox');

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
			ION.HTML(
				'users/users_list',
				{},
				{update:'usersList'}
			);
		}
	});
	$('usersListTab').fireEvent('click');


	/**
	 * Init help tips on label
	 *
	 */
	ION.initLabelHelpLinks('#newGroupForm');
	ION.initLabelHelpLinks('#userExportForm');


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
			e.stop();
			var id = item.getProperty('rel');

			ION.formWindow(	
				'group' + id, 					// object ID
				'groupForm' + id,				// Form ID
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





