<?php

    log_message('error', 'View File Loaded : phpinfo.php');

    if ($this->connect->is('super-admins'))
    {
        phpinfo();
    }
    else
    {
        echo '<h1>Nice server !</h1>';
    }

?>