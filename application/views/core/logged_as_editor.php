<?php

/**
 *
 * Used by Base_Controller->render() to display the "connected" marker on the website side
 * when the user is connected to the Admin panel as Editor.
 * 
 * The CSS class are defined in /themes/admin/css/logged-as-editor.css
 *
 * See /application/librairies/MY_Controller.php for more information.
 *
 * @since	0.9.6
 *
 */

?>
<link rel="stylesheet" href="<?= base_url() ?>themes/admin/styles/<?php echo Settings::get('backend_ui_style') ?>/css/logged-as-editor.css" />
<div id="ionizeLoggedAsEditorFlag"><a href="<?= base_url() ?><?= config_item('admin_url') ?>/"></a></div>
</body>