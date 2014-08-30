<?php

include_once('func.php');

$text = $_POST['text'];
$tags = $_POST['tags'];
$img = $_FILES['img'];

if (strlen($text) == 0) {
    echo -1;
    exit;
}

$tag_list = explode(' ', $tags);

if (count($tag_list) == 0) {
    echo -2;
    exit;
}

connect();

$file_path = upload_img($img);
$id = add_note($text, $file_path);
add_tag_list($id, $tag_list);

echo 1;

close();

?>