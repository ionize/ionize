<html>
<body>

<h2>Welcome !</h2>

<p>You just registered an account on our website.</p>

<p>Your login information :</p>

<p>
Name : <b><?= $screen_name ?></b><br/>

Login : <b><?= $username ?></b><br/>
</p>

<p>
Activation link : <br/>
{unwrap}<a href="<?= $url?><?= $username ?>/<?= $key ?>"><?= $url?><?= $username ?>/<?= $key ?></a>{/unwrap}

</p>

</body>
</html>