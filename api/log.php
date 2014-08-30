<?php

function obj_to_string($mixed) {
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function n_log($str) {
    // $fp = fopen('api.log', 'a');
    // fwrite($fp, date("Y-m-d H:i:s").": ".$str."\n");
    // fclose($fp);
}

function n_log_obj($mixed) {
    $content = obj_to_string($mixed);
    n_log($content);
}

// $content = obj_to_string($_POST);
// n_log($content);
// n_log_obj($_FILES);

?>