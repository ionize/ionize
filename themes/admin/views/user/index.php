<?php

/**
 * Displays the list of all users
 *
 */
?>


<div id="maincolumn">

	<h2 class="main groups" id="main-title"><?php echo lang('ionize_title_users') ?></h2>

	<!-- Tabs -->
	<div id="usersTab" class="mainTabs mt20">
		<ul class="tab-menu">
			<li id="userListTab"><a><?php echo lang('ionize_title_existing_users') ?></a></li>
			<?php if ( Authority::can('access', 'admin/role')) :?>
				<li id="roleListTab"><a><?php echo lang('ionize_title_roles') ?></a></li>
			<?php endif ;?>
		</ul>
		<div class="clear"></div>
	</div>

	<!-- Existing users table -->
	<div id="usersTabContent">
	
		<div class="tabcontent">
			
			<!-- Users list -->
			<div class="form-bloc">
				<form name="userFilter" id="userFilter" method="post">

					<select id="userRoleSelect" name="id_role" class="select">
						<option value=""><?php echo lang('ionize_label_all_groups') ?></option>
						<?php foreach($roles as $role) :?>
							<option value="<?php echo $role['id_role'] ?>"><?php echo $role['role_name'] ?></option>
						<?php endforeach ;?>
					</select>

					<label class="over">
						<?php echo lang('ionize_label_email') ?>
						<input alt="<?php echo lang('ionize_label_email') ?>" type="text" class="inputtext w140" id="filter_email" name="email" value="" />
					</label>

					<label class="over">
						<?php echo lang('ionize_label_screen_name') ?>
						<input type="text" class="inputtext w140" id="filter_screenname" name="screen_name" value="" />
					</label>

					<label class="over">
						<?php echo lang('ionize_label_users_per_page') ?>
						<input type="text" class="inputtext w40" id="filter_nb" name="nb" value="50" />
					</label>

					<a id="btnSubmitFilter" class="button green"><?php echo lang('ionize_button_filter') ?></a>

				</form>
			</div>

			<div id="userList"></div>

		</div>

		<?php if ( Authority::can('access', 'admin/role')) :?>

		<!-- Roles -->
		<div class="tabcontent">
			<div id="roleContainer"></div>
		</div>

		<?php endif ;?>

	</div>

</div>


<script type="text/javascript">

	// Toolbox
	ION.initToolbox('users_toolbox');


	// Users list tab
	$('userListTab').addEvent('click', function()
	{
		if ( ! this.retrieve('loaded'))
		{
			var roleToLoad = 'users';

			$$('#userRoleSelect option').each(function(option){

				if (option.value == roleToLoad)
					option.setProperty('selected', 'selected');

			});
			ION.HTML(
				'user/get_list',
				{
					'role_code' : roleToLoad
				},
				{update:'userList'}
			);
		}
	});
	$('userListTab').fireEvent('click');


	<?php if ( Authority::can('access', 'admin/role')) :?>
		// Roles list tab
		$('roleListTab').addEvent('click', function()
		{
			if ( ! this.retrieve('loaded'))
			{
				ION.HTML(
					'role/get_list',
					{},
					{update:'roleContainer'}
				);
			}
		});
		$('roleListTab').fireEvent('click');
	<?php endif ;?>

	// Tabs
    var usersTabSwapper = new TabSwapper({tabsContainer: 'usersTab', sectionsContainer: 'usersTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'usersTab' });


	// Filter
	$('btnSubmitFilter').addEvent('click', function(e)
	{
		ION.HTML('user/get_list', $('userFilter'), {'update':$('userList')});
	});

</script>
