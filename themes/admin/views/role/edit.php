<?php
/**
 * Editing a role
 *
 * Receives vars :
 * $role :				Edited role
 * $json_resources : 	Tree of all resources (JSON)
 * $json_rules : 		Role's permissions (JSON)
 * has_all : 			TRUE if the user has all permissions
 *
 */

$protected_role_codes = array(
	'guest', 'super-admin', 'pending', 'banned', 'deactivated'
);

?>

	<p class="h20 mb10">
		<a class="button light left" id="backToRoleListButton">
			<i class="icon-back"></i><?php echo lang('ionize_label_back_to_role_list'); ?>
		</a>

		<?php if (
			Authority::can('edit', 'admin/role') OR
			Authority::can('access', 'admin/modules/permissions') OR
			Authority::can('access', 'admin/role/permissions')
		) :?>
			<a class="button green right saveRoleButton" id="saveRoleButton">
				<?php echo lang('ionize_button_save'); ?>
			</a>
		<?php endif;?>

	</p>

	<form name="roleEditForm" id="roleEditForm" action="role/save">

		<!-- Hidden fields -->
		<input id="id_role" name="id_role" type="hidden" value="<?php echo $role['id_role'] ?>" />

		<?php if (in_array($role['role_code'], $protected_role_codes)): ?>
			<p class="lite left mb10 ml25">This role is used by System, its code cannot be changed</p>
		<?php endif ;?>

		<!-- Group name -->
		<dl class="small left">
			<dt>
				<label for="role_code"><?php echo lang('ionize_label_role_code'); ?></label>

			</dt>
			<dd>
				<input id="role_code" name="role_code" class="inputtext w150 left required" <?php if ( ! Authority::can('edit', 'admin/role') OR ( in_array($role['role_code'], $protected_role_codes))) :?> disabled="disabled" <?php endif ;?>type="text" value="<?php echo $role['role_code']; ?>" />

                <label for="role_name" class="left ml20"><?php echo lang('ionize_label_role_name'); ?></label>
                <input id="role_name" name="role_name" class="inputtext left w150 required" <?php if ( ! Authority::can('edit', 'admin/role')) :?> disabled="disabled" <?php endif ;?>type="text" value="<?php echo $role['role_name']; ?>" />
			</dd>
		</dl>


		<!-- Description -->
		<dl class="small">
			<dt>
				<label for="role_description"><?php echo lang('ionize_label_description'); ?></label>
			</dt>
			<dd>
				<textarea class="textarea autogrow" id="role_description" name="role_description" <?php if ( ! Authority::can('edit', 'admin/role')) :?> disabled="disabled" <?php endif ;?>><?php echo $role['role_description']; ?></textarea>
			</dd>
		</dl>


        <!-- Level -->
		<?php
			$str_roles = '';
			foreach($roles as $r)
				$str_roles .= $r['role_level'] . ':' . $r['role_name'].', ';
		?>
        <dl class="small">
            <dt>
                <label for="role_level" title="<?php echo $str_roles ?>"><?php echo lang('ionize_label_role_level'); ?></label>
            </dt>
            <dd>
                <input type="text" class="inputtext w50 required left mr15" <?php if ( ! Authority::can('edit', 'admin/role')) :?> disabled="disabled" <?php endif ;?> name="role_level" id="role_level" value="<?php echo $role['role_level']; ?>"/>
            </dd>
        </dl>

		<?php if ( Authority::can('access', 'admin/role/permissions')) :?>

			<h3><?php echo lang('ionize_title_backend_permissions'); ?></h3>

			<dl class="small mt20">
				<dt>
					<label><?php echo lang('ionize_title_permissions'); ?></label>
				</dt>
				<dd>
					<div class="mb10">
						<label class="ml0">
							<input id="permissionLevelCustom" type="radio" name="permission_level" class="mr5" value="custom"/>
							<a><?php echo lang('ionize_label_permissions_custom'); ?></a>
						</label>

						<label>
							<input id="permissionLevelAll" type="radio" name="permission_level" <?php if ($has_all) :?>checked="checked"<?php endif ;?> class="mr5" value="all"/>
							<a><?php echo lang('ionize_label_permissions_all'); ?></a>
						</label>
					</div>
				</dd>
			</dl>

			<dl class="small" id="customPermissionContainer">
                <dt>
                    <label><?php echo lang('ionize_label_allowed_resources'); ?></label>
                </dt>
                <dd>
                    <div id="rulesContainer" class="hidden"></div>
				</dd>
			</dl>

		<?php endif;?>


		<?php if ( Authority::can('access', 'admin/modules/permissions')) :?>

        	<h3><?php echo lang('ionize_title_modules_permissions'); ?></h3>

			<dl class="small" id="modulePermissionContainer">
				<dt>
					<label><?php echo lang('ionize_label_allowed_resources'); ?></label>
				</dt>
				<dd>
					<div id="modulesRulesContainer"></div>
				</dd>
			</dl>

		<?php endif;?>


		<?php if (
			Authority::can('edit', 'admin/role') OR
			Authority::can('access', 'admin/modules/permissions') OR
			Authority::can('access', 'admin/role/permissions')
		) :?>

			<a class="button green right saveRoleButton" id="saveRoleButton">
				<?php echo lang('ionize_button_save'); ?>
			</a>

		<?php endif;?>


	</form>

	<script type="text/javascript">

		ION.initFormAutoGrow();

        // Form Validation
        var fvRole = new Form.Validator.Inline('roleEditForm', {
            errorPrefix: '',
            showError: function(element) {
                element.show();
            }
        });

		<?php if (
			Authority::can('edit', 'admin/role') OR
			Authority::can('access', 'admin/modules/permissions') OR
			Authority::can('access', 'admin/role/permissions')
		) :?>
			$$('.saveRoleButton').each(function(item)
			{
				item.addEvent('click', function()
				{
					if ( ! fvRole.validate())
					{
						new ION.Notify(
							$('roleEditForm').getParent('div'),
							{type:'error'}
						).show('ionize_message_form_validation_please_correct');
					}
					else
					{
						ION.JSON(
							'role/save',
							$('roleEditForm')
						);
					}
				});
			});
		<?php endif;?>

        // Back to Roles list
		$('backToRoleListButton').addEvent('click', function(){
			$('roleListTab').fireEvent('click');
		});

		<?php if ( Authority::can('access', 'admin/role/permissions')) :?>

			// Permission Level checkboxes
			$('permissionLevelAll').addEvent('click', function(evt){
				$('customPermissionContainer').hide();
			});
			$('permissionLevelCustom').addEvent('click', function(evt){
				$('customPermissionContainer').show();
			});

			if ( ! $('permissionLevelAll').getProperty('checked'))
			{
				$('permissionLevelCustom').fireEvent('click');
				$('permissionLevelCustom').setProperty('checked', 'checked');
			}
			else
			{
                $('customPermissionContainer').hide();
			}


			var backRules = new ION.PermissionTree(
				'rulesContainer',
				<?php echo $json_resources ?>,
					{
						'key': 'id_resource',
						'data': [
							{'key':'resource', 'as':'resource'},
							{'key':'title', 'as':'title'},
							{'key':'description', 'as':'description'},
							{'key':'actions', 'as':'actions'}
						],
						'rules' : <?php echo $json_rules ?>
					}
			);

		<?php endif;?>

		<?php if ( Authority::can('access', 'admin/modules/permissions')) :?>

			<?php
			// log_message('error', print_r(json_decode($json_modules_resources), true));
 			?>

			var modRules = new ION.PermissionTree(
				'modulesRulesContainer',
				<?php echo $json_modules_resources ?>,
				{
					'key': 'id_resource',
					'data': [
						{'key':'resource', 'as':'resource'},
						{'key':'title', 'as':'title'},
						{'key':'description', 'as':'description'},
						{'key':'actions', 'as':'actions'}
					],
					'rules' : <?php echo $json_rules ?>
				}
			);

		<?php endif;?>

	</script>
