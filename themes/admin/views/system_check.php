
<!-- Main Column -->
<div id="maincolumn">

	<!-- Title -->
	<h2 class="main system-check" id="main-title"><?= lang('ionize_title_system_check') ?></h2>

	<!-- Subtitle -->
	<div class="subtitle">
		<p><?= lang('ionize_text_system_check')?></p>
		<p><input id="startCheckButton" type="button" class="button yes ml0" value="<?= lang('ionize_button_start_system_check') ?>" /></p>
	</div>
	
	
	<!-- Check report -->
	<div id="system_check_report"></div>



</div> <!-- /maincolumn -->

</form>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('empty_toolbox');

	
	ION.initRequestEvent($('startCheckButton'), 'system_check/start_check');


	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
//	ION.initLabelHelpLinks('#ionizeSettingsForm');



</script>