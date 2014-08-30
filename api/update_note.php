<?php

include_once('func.php');

$text = $_POST['text'];
$tags = $_POST['tags'];
$id = $_POST['id'];
$remove_media = $_POST['remove_media'];

if (strlen($id) == 0) {
    echo -3;
    exit;
}

if (strlen($text) == 0) {
    echo -1;
    exit;
}

$tag_list = explode(' ', $tags);

if (count($tag_list) == 0) {
    echo -2;
    exit;
}

$img = $_FILES['img'];

connect();

$old_note = get_note($id);
$old_file_path = $old_note['media'];

remove_tag_by_id($id);

if (strlen($remove_media) == 0) {

    $file_path = upload_img($img);

    if (strlen($file_path) > 0) {
        remove_media_by_id($id);
    }
    else {
        $file_path = $old_file_path;
    }
}
else {
    remove_media_by_id($id);
    $file_path = '';
}

update_note($id, $text, $file_path);
add_tag_list($id, $tag_list);

echo 1;

close();

?>