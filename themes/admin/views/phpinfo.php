<?php

    if ($this->connect->is('super-admins'))
    {
        phpinfo();
    }
    else
    {
        echo '<h1>Nice server !</h1>';
    }

?>