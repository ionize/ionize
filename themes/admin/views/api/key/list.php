<?php
/**
 * API keys list
 * Loaded through XHR
 *
 */
?>

<?php if ( ! empty($keys)) :?>

	<table class="list" id="apiKeysTable">

        <thead>
        <tr>
            <th axis="string"><?php echo lang('ionize_label_id') ?></th>
            <th axis="string"><?php echo lang('ionize_label_api_key') ?></th>
            <th axis="string"><?php echo lang('ionize_label_api_key_level') ?></th>
            <th axis="string"><?php echo lang('ionize_label_api_ignore_limits') ?></th>
            <th axis="string"><?php echo lang('ionize_label_api_is_private') ?></th>
            <th axis="string"><?php echo lang('ionize_label_api_ip_addresses') ?></th>
            <th axis="string"><?php echo lang('ionize_label_api_date_created') ?></th>
            <th></th>
        </tr>
        </thead>

        <tbody>

			<?php foreach($keys as $key) :?>
			<?php endforeach ;?>

        </tbody>
    </table>


	<script type="text/javascript">

        new SortableTable('apiKeysTable',{sortOn: 0, sortBy: 'ASC'});

    </script>

<?php endif ;?>
