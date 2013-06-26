
<div id="maincolumn">

	<h2 class="main modules"><?php echo lang('ionize_title_modules_list'); ?></h2>

	<form name="modulesForm">
	
		<table class="list" id="modulesTable">

			<thead>
				<tr>
					<th axis="string"><?php echo lang('ionize_label_module_name'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_description'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_module_uri'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_installed'); ?></th>
					<th></th>				
				</tr>
			</thead>

			<tbody>
			
			<?php foreach($modules as $module) :?>

				<?php
					$module_admin_controller_path = MODPATH.$module['folder'].'/controllers/admin/'.$module['uri'].'.php';
					$module_admin_controller_url = admin_url().'module/'.$module['folder'].'/'.$module['uri'].'/index';
				?>
				<tr class="module<?php echo $module['name']; ?>">
					<td>
						<?php if($module['has_admin'] == 'true' && $module['installed'] && file_exists($module_admin_controller_path)): ?>
							<a class="moduleAdminLink" href="<?php echo $module_admin_controller_url; ?>/index" rel="<?php echo $module['name']; ?>" id="module<?php echo $module['name']; ?>"><?php echo $module['name']; ?></a>
						<?php else :?>
							<?php echo $module['name']; ?>
						<?php endif; ?>
					</td>
					<td><?php echo auto_link($module['description']) ?></td>
					<td>
						<?php if($module['installed']): ?>
							<?php if (! empty($module['has_frontend']) && $module['has_frontend'] == TRUE) :?>
								<a href="<?php echo base_url().$module['uri_user_segment']; ?>" target="_blank"><?php echo $module['uri_user_segment']; ?></a>
							<?php endif; ?>
						<?php else :?>
							<input id="segment<?php echo $module['folder']; ?>" class="inputtext" value="<?php echo $module['uri_user_segment']; ?>" />
						<?php endif; ?>
					</td>
					<td>
						<?php if($module['installed']): ?>
							<a class="icon ok"></a>
						<?php else: ?>
							<a class="icon nok"></a>
						<?php endif; ?>
					</td>
					<td>
						<?php if($module['installed']): ?>
							<a class="moduleUninstall" href="modules/uninstall/<?php echo $module['folder']; ?>"><?php echo lang('ionize_label_module_uninstall'); ?>
						<?php else :?>
							<a class="moduleInstall" rel="<?php echo $module['folder']; ?>" href="modules/install/<?php echo $module['folder']; ?>"><?php echo lang('ionize_label_module_install'); ?>
						<?php endif; ?>
					</td>
				</tr>

			<?php endforeach ;?>
			
			</tbody>

		</table>

	</form>
		
</div>


<script type="text/javascript">
	
	// Panel toolbox
	ION.initToolbox();


	// Module install link events
	$$('.moduleInstall').each(function(item, idx)
	{
		var url = 	item.getProperty('href');

		item.addEvent('click', function(e)
		{
			e.stop();
			var module_uri = $('segment' + item.getProperty('rel')).value;
			ION.sendData(url + '/' + module_uri,'');
		});
	});


	// Module uninstall link events
	$$('.moduleUninstall').each(function(item, idx)
	{
		var url = 	item.getProperty('href');
		
		item.addEvent('click', function(e)
		{
			e.stop();
			
			ION.sendData(url,'');
		});
	});


	// Module Admin link
	$$('.moduleAdminLink').each(function(item, idx)
	{
		item.addEvent('click', function(e)
		{
			e.stop();

            ION.contentUpdate({
				element: $('mainPanel'),
				title: item.get('rel'),
				url : item.getProperty('href')
			});
		});
	});
	
</script>

