<?php

include_once('mysql.php');




// get <note_id list> with a certain <tag> in a certain <page> sorted by <note_id>
function list_note_id($tag, $page) {
    $PAGE_UNIT = 30;

    $where = "";
    if (strlen($tag) > 0) {
        $where = " WHERE `tag` = '$tag'";
    }

    $offset = 0;
    if ($page > 0) {
        $offset = $PAGE_UNIT * ($page - 1);
    }

    $sql = "SELECT `note_id` as `id` FROM `tags` $where
        GROUP BY `note_id`
        LIMIT $offset, 30";

    $output = query($sql, true);

    $note_id_list = array();

    foreach ($output as $value) {
        $note_id_list[] = $value['id'];
    }

    return $note_id_list;
}

// get <note list> by <note_id list>
function list_note_by_id_list($note_id_list) {
    $note_id_list_str = implode(', ', $note_id_list);
    $sql = "SELECT * FROM `notes` WHERE `id` IN ( $note_id_list_str ) ORDER BY `id` DESC";
    $output = query($sql, true);

    return $output;
}

// get <note> by <note_id>
function get_note($note_id) {
    $sql = "SELECT * FROM `notes` WHERE `id` = $note_id";
    $output = query($sql, true);

    return $output[0];
}

// add a <note> to notes and tags
function add_note($text, $file_path) {
    $sql = "INSERT INTO `notes` (`text`, `media`) VALUES ('$text', '$file_path');";
    query($sql);
    return mysql_insert_id();
}

// set note info
function update_note($note_id, $text, $file_path) {
    $sql = "UPDATE `notes` SET `text` = '$text', `media` = '$file_path' WHERE `id` = '$note_id'";
    query($sql);
}




// list all <tag_data> in database
function list_tag_rank() {
    $sql = "SELECT `tag` , COUNT( * ) as `count` FROM `tags` GROUP BY `tag` ORDER BY `tag` ASC";
    $tag_data_list = query($sql, true);
    return $tag_data_list;
}

// get <tag list> by <note_id>
function list_tag($note_id) {
    $sql = "SELECT `tag` FROM `tags` WHERE `note_id` = $note_id";
    $result = query($sql, true);

    $output = array();
    foreach ($result as $value) {
        $tag = $value['tag'];
        $output[] = $tag;
    }

    return $output;
}




// whether a certain <tag> existed in database
function has_tag($tag) {
    $sql = "SELECT COUNT( * ) as `count` FROM `tags` WHERE `tag` = '$tag'";
    $result = query($sql, true);

    return $result[0]['count'] > 0;
}

// add <tags> from <tag_list> for a note by <note_id>
function add_tag_list($note_id, $tag_list) {
    $tag_str_list = array();
    foreach ($tag_list as $tag) {
        $tag_str_list[] = "( $note_id, '$tag' )";
    }
    $tag_str_list_str = implode(', ', $tag_str_list);

    $sql = "INSERT INTO `tags` (`note_id`, `tag`) VALUES $tag_str_list_str ;";
    query($sql);
}




// remove data from tags by <note_id>
function remove_tag_by_id($note_id) {
    $sql = "DELETE FROM `tags` WHERE `note_id` = '$note_id';";
    query($sql);
}

// remove data from notes by <note_id>
function remove_note_by_id($note_id) {
    $sql = "DELETE FROM `notes` WHERE `note_id` = '$note_id';";
    query($sql);
}

// remove media for <note_id>
function remove_media_by_id($note_id) {
    $note = get_note($note_id);
    $media = $note['media'];

    if (strlen($media) > 0) {
        unlink($media);
    }
}




// get <tags list> in <note_id list>
function map_tags_foreach_id($note_id_list) {
    $note_id_list_str = implode(', ', $note_id_list);
    $sql = "SELECT `tag`, `note_id` FROM `tags` WHERE `note_id` IN ( $note_id_list_str )";
    $result = query($sql, true);

    $output = array();
    foreach ($result as $value) {
        $note_id = $value['note_id'];
        $tag = $value['tag'];
        if (!$output[$note_id]) {
            $output[$note_id] = array();
        }
        $output[$note_id][] = $tag;
    }

    return $output;
}

// extend <tags list> for each corresponding <note> in <note list>
function set_tags_foreach_note($note_list, $tags_list) {
    foreach ($note_list as $i => $note) {
        $note_id = $note['id'];
        $note['tags'] = $tags_list[$note_id];
        $note_list[$i] = $note;
    }
    return $note_list;
}




// upload a img file and return the path
function upload_img($img) {
    $date = date("Ymd_His");

    if ($img['type'] == "image/jpeg") {
        $file_name = $date.".jpg";
    }
    else if ($img['type'] == "image/png") {
        $file_name = $date.".png";
    }
    else {
        return '';
    }

    $first_dir = substr($file_name, 0, 4);
    $second_dir = substr($file_name, 4, 2);

    $dir_path = '../archives/'.$first_dir.'/'.$second_dir;
    $file_path = $dir_path.'/'.$file_name;

    mkdir($dir_path, 0777, true);
    move_uploaded_file($img['tmp_name'], $file_path);

    return $file_path;
}




?>