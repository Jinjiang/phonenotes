<?php

include_once('mysql.php');

connect();

query('TRUNCATE TABLE `notes`');
query('TRUNCATE TABLE `tags`');

exec('rm -Rf ../archives/*');

close();

?>