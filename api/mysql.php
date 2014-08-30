<?php

include_once('log.php');

function connect() {
    mysql_connect('localhost', 'root', '') or die('connect error');
    mysql_set_charset('utf8');
    mysql_select_db('test') or die('database error');
}

function query($sql, $need_result) {

    n_log($sql);

    $output = array();
    $result = mysql_query($sql);
    if ($need_result) {
        while ($row = mysql_fetch_assoc($result)) {
            $output[] = $row;
        }
        return $output;
    }
}

function close() {
    mysql_close();
}

?>