<?php

include_once('func.php');

connect();

$tag_data_list = list_tag_rank();

echo json_encode($tag_data_list);

close();

?>