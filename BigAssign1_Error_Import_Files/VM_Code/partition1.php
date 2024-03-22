<?php
$key=2;
$value='equipPartaa';
shell_exec("/usr/bin/php /home/wge469/adv_soft_eng/import_FINAL.php $key $value > /home/wge469/adv_soft_eng/logs/$value.log 
                2>/home/wge469/adv_soft_eng/logs/$value.log &");
?>
