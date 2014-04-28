<?php

// trace($result);

?>

<h3><?php echo lang('module_sfs_test_result') ?></h3>


<!-- API Key -->
<dl>
	<dt><label><?php echo lang('module_sfs_label_api_key'); ?></label></dt>
	<dd>
		<a class="icon <?php if($result['api_key']): ?> ok<?php else:?> nok<?php endif;?>"></a>
	</dd>
</dl>

<!-- Evidence Input -->
<dl>
	<dt><label><?php echo lang('module_sfs_evidence_input'); ?></label></dt>
	<dd>
		<a class="icon <?php if($result['evidence_input']): ?> ok<?php else:?> nok<?php endif;?>"></a>
	</dd>
</dl>

<!-- Called URL -->
<dl>
	<dt><label><?php echo lang('module_sfs_called_url'); ?></label></dt>
	<dd>
		<?php echo($result['called_url']) ?>
	</dd>
</dl>

<!-- Result -->
<dl>
	<dt><label><?php echo lang('module_sfs_server_response'); ?></label></dt>
	<dd>
<pre><?php print_r($result['server_response']) ?></pre>
	</dd>
</dl>
