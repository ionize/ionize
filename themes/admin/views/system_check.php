
<!-- Main Column -->
<div id="maincolumn">

	<!-- Title -->
	<h2 class="main system-check" id="main-title"><?= lang('ionize_title_system_check') ?></h2>

	<!-- Subtitle -->
	<div class="subtitle">
		<p><?= lang('ionize_text_system_check')?></p>
		<p><input id="startCheckButton" type="button" class="button yes ml0" value="<?= lang('ionize_button_start_system_check') ?>" /></p>
	
	
		<!-- Check report -->
		<table class="list">
			<thead>
				<tr>
					<th><?= lang('ionize_title_check_element')?></th>
					<th class="center"><?= lang('ionize_title_check_result')?></th>
					<th class="center"><?= lang('ionize_title_check_status')?></th>
				</tr>
			</thead>
			<tbody id="system_check_report">
				
			</tbody>
		</table>
	
	</div>
	
</div> <!-- /maincolumn -->



<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('empty_toolbox');

	
	ION.initRequestEvent($('startCheckButton'), 'system_check/start_check');


</script>