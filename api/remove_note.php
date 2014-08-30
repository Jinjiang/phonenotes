<?php

include_once('func.php');

$id = $_GET['id'];
$tag_list = list_tag($id);

if (strlen($id) == 0) {
    echo -1;
    exit;
}

connect();

remove_media_by_id($id);
remove_tag_by_id($id);
remove_note_by_id($id);

echo 1;

close();

?>