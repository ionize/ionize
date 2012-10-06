<?php

    $path_to_filemanager = FCPATH . 'themes/' . Settings::get('theme_admin') . '/javascript/kcfinder/';
    $url_to_filemanager = theme_url() . '/javascript/kcfinder/';

?>
<div style="width: 100%; height: 100%; position: absolute; overflow: hidden;">
	<iframe id="filemanager_iframe" src="<?php echo $url_to_filemanager; ?>browse.php?lng=<?php echo Settings::get_lang('current'); ?>" style="width: 100%; height: 100%; border:none; margin: 0; padding: 0;"></iframe>
</div>
