<?php
$key=6;
$value='equipPartae';
shell_exec("/usr/bin/php /home/wge469/adv_soft_eng/import_FINAL.php $key $value > /home/wge469/adv_soft_eng/logs/$value.log 
                2>/home/wge469/adv_soft_eng/logs/$value.log &");
?>
