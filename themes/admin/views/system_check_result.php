<?php

    /**
     * Displays one result for a system check
     * Called through XHR by : /admin/system_check.php
     *
     */

?>
<tr>
	<td><?php echo $title; ?></td>
	<td class="center"><span class="<?php echo $result_status; ?>"><?php echo $result_text; ?></span></td>
	<td class="center"><a class="icon ok ml10"></a></td>
</tr>
