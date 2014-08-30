<?php

include_once('func.php');

$tag = $_GET['tag'];
$page = $_GET['page'];

connect();

$id_list = list_note_id($tag, $page);
$note_list = list_note_by_id_list($id_list);
$tags_map = map_tags_foreach_id($id_list);
$note_list = set_tags_foreach_note($note_list, $tags_map);

echo json_encode($note_list);

close();

?>