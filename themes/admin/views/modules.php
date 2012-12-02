
<div id="maincolumn">

	<h3><?php echo lang('ionize_title_modules_list'); ?></h3>

	<form name="modulesForm">
	
		<table class="list" id="modulesTable">

			<thead>
				<tr>
					<th axis="string"><?php echo lang('ionize_label_module_name'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_description'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_database_tables'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_module_uri'); ?></th>
					<th axis="string"><?php echo lang('ionize_label_installed'); ?></th>
					<th></th>				
				</tr>
			</thead>

			<tbody>
			
			<?php foreach($modules as $module) :?>

				<?php
					$module_admin_controller_path = MODPATH.$module['folder'].'/controllers/admin/'.$module['uri_segment'].'.php';
					$module_admin_controller_url = admin_url().'module/'.$module['folder'].'/'.$module['uri_segment'].'/index';
				?>
				<tr class="module<?php echo $module['name']; ?>">
					<td>
						<?php if($module['has_admin'] == 'true' && $module['installed'] && file_exists($module_admin_controller_path)): ?>
							<a class="moduleAdminLink" href="<?php echo $module_admin_controller_url; ?>/index" rel="<?php echo $module['name']; ?>" id="module<?php echo $module['name']; ?>"><?php echo $module['name']; ?></a>
						<?php else :?>
							<?php echo $module['name']; ?>
						<?php endif; ?>
					</td>
					<td><?php echo $module['description']; ?></td>
					<td>
						<?php if ($module['database'] == TRUE ) :?>
							<a class="icon database help" title="<?php echo '&bull; ' . implode('<br/> &bull; ', $module['tables']); ?>"></a>
						<?php endif; ?>
					</td>
					<td>
						<?php if($module['installed']): ?>
							<a href="<?php echo base_url().$module['uri_user_segment']; ?>" target="_blank"><?php echo $module['uri_user_segment']; ?></a>
						<?php else :?>
							<input id="segment<?php echo $module['folder']; ?>" class="inputtext" value="<?php echo $module['uri_user_segment']; ?>" />
						<?php endif; ?>
					</td>
					<td><img src="<?php echo theme_url(); ?>images/icon_16_<?php if($module['installed']): ?>ok<?php else: ?>nok<?php endif; ?>.png" /></td>
					<td>
						<?php if($module['installed']): ?>
							<a class="moduleUninstall" href="<?php echo admin_url(); ?>modules/uninstall/<?php echo $module['folder']; ?>"><?php echo lang('ionize_label_module_uninstall'); ?>
						<?php else :?>
							<a class="moduleInstall" rel="<?php echo $module['folder']; ?>" href="<?php echo admin_url(); ?>modules/install/<?php echo $module['folder']; ?>"><?php echo lang('ionize_label_module_install'); ?>
						<?php endif; ?>
					</td>
				</tr>

			<?php endforeach ;?>
			
			</tbody>

		</table>

	</form>
		

</div> <!-- /maincolumn -->


<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	ION.initToolbox();


	/**
	 * Init help tips
	 * see init-ionize.js
	 *
	 */
	ION.initLabelHelpLinks('#maincolumn');


	/**
	 * Module install link events
	 */
	$$('.moduleInstall').each(function(item, idx)
	{
		var url = 	item.getProperty('href');

		item.addEvent('click', function(e)
		{
//			var e = new Event(e).stop();
			e.stop();

			var module_uri = $('segment' + item.getProperty('rel')).value;
			
			ION.sendData(url + '/' + module_uri,'');
		});
	});


	/**
	 * Module uninstall link events
	 */
	$$('.moduleUninstall').each(function(item, idx)
	{
		var url = 	item.getProperty('href');
		
		item.addEvent('click', function(e)
		{
//			var e = new Event(e).stop();
			e.stop();
			
			ION.sendData(url,'');
		});
	});


	/**
	 * Module Admin link
	 */
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

