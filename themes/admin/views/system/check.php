
<!-- Main Column -->
<div id="maincolumn">

	<!-- Title -->
	<h2 class="main system-check" id="main-title"><?php echo lang('ionize_title_system_check'); ?></h2>

	<!-- Subtitle -->
	<div class="subtitle">
		<p><?php echo lang('ionize_text_system_check'); ?></p>
	</div>


	<div id="checkTab" class="mainTabs">
		<ul class="tab-menu">
			<li id="infoTab"><a><?php echo lang('ionize_title_informations'); ?></a></li>
			<li id="toolTab"><a><?php echo lang('ionize_dashboard_title_tools'); ?></a></li>
			<li id="reportTab"><a><?php echo lang('ionize_title_check_reports'); ?></a></li>
		</ul>
		<div class="clear"></div>
	</div>

	<div id="checkTabContent">

		<!--
			Informations
		-->
		<div class="tabcontent">
			<table class="list m0">
				<thead>
				<tr>
					<th><?php echo lang('ionize_title_check_folder'); ?></th>
					<th class="center"><?php echo lang('ionize_title_label_write_rights'); ?></th>
				</tr>
				</thead>
				<tbody>
					<?php foreach ($folders as $folder):?>
						<tr>
							<td><?php echo $folder['path']; ?></td>
							<?php if ($folder['write'] == TRUE):?>
								<td class="center"><span class="success"><?php echo lang('ionize_message_check_ok'); ?></span></td>
							<?php else: ?>
								<td class="center"><span class="error"><?php echo lang('ionize_message_check_folder_nok'); ?></span></td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<!--
			Tools
		-->
		<div class="tabcontent">
			<table class="list m0">
				<thead>
					<tr>
						<th class="w160"></th>
						<th><?php echo lang('ionize_label_description'); ?></th>
					</tr>
				</thead>
				<tbody>
					<!-- Langs -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="system_check/check_lang">
								<i class="icon-lang"></i><?php echo lang('ionize_title_check_lang'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_check_lang'); ?></td>
					</tr>
					<!-- URLs -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="system_check/rebuild_urls">
								<i class="icon-urls"></i><?php echo lang('ionize_title_rebuild_urls'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_rebuild_urls'); ?></td>
					</tr>
					<!-- Article's context -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="system_check/check_article_context">
								<i class="icon-article"></i><?php echo lang('ionize_title_check_article_context'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_check_article_context'); ?></td>
					</tr>
					<!-- Page's levels -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="system_check/check_page_level">
								<i class="icon-folder"></i><?php echo lang('ionize_title_check_page_level'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_check_page_level'); ?></td>
					</tr>
					<!-- Media table -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="system_check/clean_media">
								<i class="icon-pictures"></i><?php echo lang('ionize_title_clean_media'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_clean_media'); ?></td>
					</tr>
					<!-- Lang tables -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="lang/clean_tables">
								<i class="icon-lang"></i><?php echo lang('ionize_button_clean_lang_tables'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_clean_lang_tables'); ?></td>
					</tr>
					<!-- Thumbs delete -->
					<tr>
						<td class="middle right pr10">
							<a class="button light check-btn" data-href="media/delete_all_thumbs">
								<i class="icon-pictures"></i><?php echo lang('ionize_title_delete_thumbs'); ?>
							</a>
						</td>
						<td class="middle"><?php echo lang('ionize_text_delete_thumbs'); ?></td>
					</tr>


				</tbody>
			</table>

		</div>

		<!--
			Reports
		-->
		<div class="tabcontent">
			<table class="list m0">
				<thead>
					<tr>
						<th class="w160"></th>
						<th><?php echo lang('ionize_label_description'); ?></th>
					</tr>
				</thead>
				<tbody>
					<!-- Langs -->
					<tr>
						<td class="right pr10">
							<a class="button light report-btn" data-href="system_check/broken_media_report">
								<i class="icon-picture broken"></i><?php echo lang('ionize_title_broken_media_links'); ?>
							</a>
						</td>
						<td class="middle report-content" rel="system_check/broken_media_report"><?php echo lang('ionize_text_broken_media_links'); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		</div>
</div> <!-- /maincolumn -->



<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('empty_toolbox');

	$$('.check-btn').each(function(a)
	{
		var td = a.getParent('td');

		ION.initRequestEvent(
			a, a.getAttribute('data-href'), {},
			{
				onRequest: function()
				{
					td.addClass('loading');
					a.dispose();
				},
				onSuccess: function(responseJSON, responseText)
				{
					td.removeClass('loading');
					td.addClass(responseJSON.status);
					td.set('html', responseJSON.message);
				}
			}
		);
	});

	$$('.report-btn').each(function(a)
	{
		var td = a.getParent('td');
		var reportCell = $$('td[rel='+ a.getAttribute('data-href') +']');
		reportCell = reportCell[0];

		ION.initRequestEvent(
			a, a.getAttribute('data-href'), {},
			{
				onRequest: function()
				{
					td.addClass('loading');
					// a.dispose();
				},
				onSuccess: function(responseJSON, responseText)
				{
					td.removeClass('loading');
					if (reportCell)
						reportCell.set('html', responseJSON.message);
				}
			}
		);
	});



	var checkTab = new TabSwapper({
		tabsContainer: 'checkTab',
		sectionsContainer: 'checkTabContent',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent',
		cookieName: 'checkTab'
	});

</script>