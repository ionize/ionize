<p>Thank you for registering at <?php echo anchor('', Access()->reg_site_name); ?>. Before we can activate your account, please complete the registration process by clicking on the following link:</p>

<p>{unwrap}<?php echo anchor('user/activate/'.$username.'/'.$key); ?>{/unwrap}</p>

<p>In case your email program does not recognize the above link as, please direct your browser to the following URL and enter the activation code:</p>
<p>{unwrap}<?php echo anchor('user/activate'); ?>{/unwrap}<br />
Username: <?php echo $username; ?><br />
Activation Code: <?php echo $key; ?></p>