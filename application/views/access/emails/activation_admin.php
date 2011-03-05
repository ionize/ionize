<html>
<body>

<h2>Website registration !</h2>

<p>The following user has registered to your website : </p>

<p>
Name : <b><?= $screen_name ?></b><br/>
Email : <b><?= $email ?></b><br/><br/>

Login : <b><?= $username ?></b><br/>
</p>

<p>
Activation link : <br/>
{unwrap}<a href="<?= $url?><?= $username ?>/<?= $key ?>"><?= $url?><?= $username ?>/<?= $key ?>/admin</a>{/unwrap}

</p>

</body>
</html>