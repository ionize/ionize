
<div id="debug"></div>


<h3>page_groups key</h3>
<?php

$fields = $this->db->list_fields('page_user_groups');

print($fields[0]);
?>
<br/>
<h3>user_groups key</h3>
<?php

$fields = $this->db->list_fields('user_groups');

print($fields[0]);
?>

