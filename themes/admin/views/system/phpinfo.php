<?php

    if (User()->is('super-admin'))
    {
        phpinfo();
    }
    else
    {
        echo '<h1>Nice server !</h1>';
    }

?>