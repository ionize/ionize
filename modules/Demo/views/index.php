<?php
/**
 * Ionize Author Demo Module
 * Frontend Main view
 *
 * Loaded by the tag : <ion:demo:main />
 *
 * Receives : no vars
 */
?>

<!-- Container for the Authors List -->
<div id="moduleDemoAuthorList"></div>


<script type="text/javascript">

	// Controller URL to call
	var url = 'demo/author/get_list';

	// Ajax request
	jQuery.ajax(
		url,
		{
			type:'post',
			// Get the result (the view HTML string)
			// and display it in the Authors List container
			success: function(result)
			{
				$('#moduleDemoAuthorList').html(result);
			}
		}
	);

</script>