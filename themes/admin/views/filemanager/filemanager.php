

<form name="fileManagerForm">

	<div style="overflow:auto;">
		<iframe id="filemanager_iframe" src="about:blank" style="width: 100%; height: 500px; border:none;padding-bottom:20px;"></iframe>
	</div>
	
	<input type="hidden" id="hiddenFile" />

</form>


<script type="text/javascript">

	/**
	 * Panel toolbox
	 *
	 */
	MUI.initToolbox();


	mcFileManager.openInIframe('filemanager_iframe', 'fileManagerForm', 'hiddenFile');


</script>

