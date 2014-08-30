<?php

include_once('func.php');

$tag = $_POST['tag'];

if (strlen($tag) == 0) {
    echo -1;
    exit;
}

connect();

if (has_tag($tag)) {
    echo 0;
    exit;
}

$tag_list = array($tag);
add_tag_list(0, $tag_list);

echo 1;

close();

?>