
<div id="maincolumn">
	
	<!-- Existing elements -->
	<h2 class="main elements"><?php echo lang('ionize_title_content_element_list'); ?></h2>
	
	<ul id="elementContainer" class="sortable-container mt20"></ul>
	

</div>

<script type="text/javascript">


	ION.HTML('element_definition/get_element_definition_list', {}, {'update': 'elementContainer' });
	
	
	/**
	 * Panel toolbox
	 *
	 */
	
	ION.initToolbox('element_definition_toolbox');
	
	

</script>
