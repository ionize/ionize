
<div id="maincolumn">

	<h3><?=lang('ionize_title_modules_list')?></h3>

	<form name="modulesForm">
	
		<table class="list" id="modulesTable">

			<thead>
				<tr>
					<th axis="string"><?= lang('ionize_label_module_name') ?></th>
					<th axis="string"><?= lang('ionize_label_description') ?></th>
					<th axis="string"><?= lang('ionize_label_database_tables') ?></th>
					<th axis="string"><?= lang('ionize_label_module_uri') ?></th>
					<th axis="string"><?= lang('ionize_label_installed') ?></th>				
					<th></th>				
				</tr>
			</thead>

			<tbody>
			
			<?php foreach($modules as $module) :?>
				
				<tr class="module<?= $module['name'] ?>">
					<td>
						<?php if($module['has_admin'] == 'true'): ?>
							<a class="moduleAdminLink" href="<?= admin_url() ?>module/<?= $module['folder'] ?>/<?= $module['uri_segment'] ?>/index" rel="<?= $module['name'] ?>" id="module<?= $module['name'] ?>"><?= $module['name'] ?></a>
						<?php else :?>
							<?= $module['name'] ?>
						<?php endif; ?>
					</td>
					<td><?= $module['description'] ?></td>
					<td>
						<?php if ($module['database'] == TRUE ) :?>
							<a class="icon database help" title="<?= '&bull; ' . implode('<br/> &bull; ', $module['tables']) ?>"></a>
						<?php endif; ?>
					</td>
					<td>
						<?php if($module['installed']): ?>
							<a href="<?= base_url().$module['uri_user_segment'] ?>" target="_blank"><?= $module['uri_user_segment'] ?></a>
						<?php else :?>
							<input id="segment<?= $module['folder'] ?>" class="inputtext" value="<?= $module['uri_user_segment'] ?>" />
						<?php endif; ?>
					</td>
					<td><img src="<?= theme_url() ?>images/icon_16_<?php if($module['installed']): ?>ok<?php else: ?>nok<?php endif; ?>.png" /></td>
					<td>
						<?php if($module['installed']): ?>
							<a class="moduleUninstall" href="<?= admin_url() ?>modules/uninstall/<?= $module['folder'] ?>"><?= lang('ionize_label_module_uninstall') ?>
						<?php else :?>
							<a class="moduleInstall" rel="<?= $module['folder'] ?>" href="<?= admin_url() ?>modules/install/<?= $module['folder'] ?>"><?= lang('ionize_label_module_install') ?>
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
	MUI.initToolbox();


	/**
	 * Init help tips
	 * see init-ionize.js
	 *
	 */
	MUI.initLabelHelpLinks('#maincolumn');


	/**
	 * Module install link events
	 */
	$$('.moduleInstall').each(function(item, idx)
	{
		var url = 	item.getProperty('href');

		item.addEvent('click', function(e)
		{
			var e = new Event(e).stop();

			var module_uri = $('segment' + item.getProperty('rel')).value;
			
			MUI.sendData(url + '/' + module_uri,'');
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
			var e = new Event(e).stop();
			
			MUI.sendData(url,'');
		});
	});


	/**
	 * Module Admin link
	 */
	$$('.moduleAdminLink').each(function(item, idx)
	{
		item.addEvent('click', function(e)
		{
			var e = new Event(e).stop();
			
			MUI.updateContent({
				element: $('mainPanel'),
				title: item.get('rel'),
				url : item.getProperty('href')
			});
		});
	});
	
</script>

