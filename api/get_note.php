<?php

include_once('func.php');

$id = $_GET['id'];

if (strlen($id) == 0) {
    echo -1;
    exit;
}

connect();

$tag_list = list_tag($id);
$note = get_note($id);

$note['tags'] = $tag_list;

echo json_encode($oute);

close();

?>